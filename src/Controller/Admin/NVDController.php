<?php

namespace App\Controller\Admin;

use App\Entity\Config;
use App\Entity\Cpe;
use App\Entity\Cve;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use TypeError;

class NVDController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $parameterBag;
    private string $nvdFilesPath;
    private SessionInterface $session;
    /**
     * @var array|object[]
     */
    private array $configs;

    public function __construct(SessionInterface $session, ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        set_time_limit(3600);
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
        $this->nvdFilesPath = $this->parameterBag->get('kernel.project_dir') . "/nvdlib/main.py";
        $this->session = $session;
        $this->configs = $this->entityManager->getRepository(Config::class)->findAll();
    }

    #[Route('/nvd', name: 'nvd')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('admin');
    }

    #[Route('/nvd/check', name: 'nvd_check')]
    public function check($cli = false)
    {
        $configDays = $this->configs[7]->getConfigValue();
        if (str_contains($configDays, ',')) {
            $configDays = explode(",", $configDays);
        } else {
            $configDays = [$configDays];
        }
        $days = [];
        foreach ($configDays as $configDay) {
            if (is_numeric($configDay)) {
                $configDay = (int) $configDay;
                if ($configDay > 0 && $configDay < 8) {
                    $days[] = $configDay;
                }
            }
        }
        $today = date('N', time()) + 1;
        if (count($days) && !in_array($today, $days))
        die('Not allowed to be executed right now');
        $cpeList = $this->entityManager->getRepository(Cpe::class)->findAll();
        $cpesCves = $this->_parseCpes($cpeList)['parsed'];
        $kernel['creation'] = [];
        $kernel['update'] = [];
        $kernel['analysed'] = [];
        $others['creation'] = [];
        $others['update'] = [];
        $others['analysed'] = [];
        foreach ($cpesCves as $cpeCves) {
            foreach ($cpeCves as $cveColsData) {
                /** @var Cve $currentCve */
                $currentCve = $this->entityManager->getRepository(Cve::class)->findOneBy(['cve' => $cveColsData['cve']]);
                if ($currentCve) {
                    if (
                        trim(strtolower($currentCve->getAnalysisStatus())) !== "analysis completed"
                    ) {
                        if ($this->_isKernel($cveColsData)) {
                            $kernel['update'][] = $cveColsData;
                            $this->_updateCve($currentCve, $cveColsData);
                        } else if ($this->_isOthers($cveColsData)) {
                            $others['update'][] = $cveColsData;
                            $this->_updateCve($currentCve, $cveColsData);
                        }
                    } else {
                        $isNew = (
                            strtotime($currentCve->getUpdated()->format('y-m-d H:m')) < strtotime(date('y-m-d H:m', strtotime($cveColsData['lastModifiedDate'])))
                        );
                        if ($this->_isKernel($cveColsData) && $isNew) {
                            $kernel['analysed'][] = $cveColsData;
                            $this->_updateCve($currentCve, $cveColsData);
                        } else if ($this->_isOthers($cveColsData) && $isNew) {
                            $others['analysed'][] = $cveColsData;
                            $this->_updateCve($currentCve, $cveColsData);
                        }
                    }
                } else {
                    if ($this->_isKernel($cveColsData)) {
                        $kernel['creation'][] = $cveColsData;
                    } else if ($this->_isOthers($cveColsData)) {
                        $others['creation'][] = $cveColsData;
                    }
                    $this->_updateCve(new Cve(), $cveColsData);
                }
            }
        }
        $message = "";
        $cvesTypes = ['kernel' => $kernel, 'others' => $others];
        $counter = 0;
        foreach ($cvesTypes as $cvesType) {
            foreach ($cvesType as $cveStatus => $cves) {
                if ($cveStatus == 'analysed') continue;
                if (is_array($cves) && count($cves)) {
                    foreach ($cves as $cve) {
                        $counter++;
                        $version = isset($cve['matching']) ? explode(':', $cve['matching'])[5] : 'N/A';
                        $cveText = $cve['cve'] ?? 'N/A';
                        $cotsText = $cve['cots'] ?? 'N/A';
                        $baseScoreText = $cve['base_score'] ?? 'N/A';
                        $currentMessage = "A vulnerability $cveStatus detected : $cveText on the $cotsText $version with severity Score $baseScoreText\n\n";
                        $message .= $currentMessage;
                    }
                }
            }
        }
        $this->_sendEmail("$counter Vulnerabilities detected", $message);
        $this->session->getFlashBag()->add('success', str_replace("\n", "<br>", $message));

        $message = "";
        $cvesTypes = ['kernel' => $kernel['analysed'], 'others' => $others['analysed']];
        $counter = 0;
        foreach ($cvesTypes as $cves) {
            if (is_array($cves) && count($cves)) {
                foreach ($cves as $cve) {
                    $counter++;
                    $version = isset($cve['matching']) ? explode(':', $cve['matching'])[5] : 'N/A';
                    $cveText = $cve['cve'] ?? 'N/A';
                    $cotsText = $cve['cots'] ?? 'N/A';
                    $baseScoreText = $cve['base_score'] ?? 'N/A';
                    $currentMessage = "An analysed vulnerability has been updated : $cveText on the $cotsText $version with severity Score $baseScoreText\n\n";
                    $message .= $currentMessage;
                }
            }
        }
        $this->_sendEmail("$counter Analysed vulnerabilities updated", $message);
        $this->session->getFlashBag()->add('success', str_replace("\n", "<br>", $message));
        if ($cli) {
            return $message;
        }
        return $this->redirectToRoute('admin');
    }

    #[ArrayShape(['errors' => "array", 'cpes' => "mixed", 'cves' => "array", 'parsed' => "array"])] private function _parseCpes($cpes): array
    {
        $parsed = [];
        $cves = [];
        $errors = [];
        foreach ($cpes as $cpe) {
            $path = shell_exec(('python3 ' . $this->nvdFilesPath . ' "' . $cpe->getCpe() . '" "' . $this->configs[0]->getConfigValue() . '" 2>&1'));
            $path = str_replace(array("\r", "\n"), '', $path);
            if (!is_string($path) || str_contains($path, 'Traceback') || !is_file($path)) {
                $errors[$cpe->getCpe()] = $path . " NOT FOUND";
                continue;
            }
            $output = file_get_contents($path);
            $output = str_replace(array("\r", "\n"), '', $output);
            $data = json_decode($output, true);
            if ($output == "[]") $cves[$cpe->getCpe()] = [];
            else {
                if ($data) $cves[$cpe->getCpe()] = $data;
                else $errors[$cpe->getCpe()] = $output . " $path EXECUTION ERROR";
            }
        }
        foreach ($cves as $cpe => $currentCves) {
            foreach ($currentCves as $cve) {
                $cve = json_decode($cve, true);
                $version = isset($cve['impact']['baseMetricV3']) ? '3' : '2';
                $vector = $version == '3' ? 'attackVector' : 'accessVector';
                try {
                    $cols = [
                        "id" => null,
                        "cve" => $cve['cve']['CVE_data_meta']['ID'],
                        "link" => "https://nvd.nist.gov/vuln/detail/" . $cve['cve']['CVE_data_meta']['ID'],
                        "cve_description" => $cve['cve']['description']['description_data'][0]['value'],
                        "attack_vector" => $cve['impact']["baseMetricV$version"]["cvssV$version"][$vector],
                        "base_score" => $cve['impact']["baseMetricV$version"]["cvssV$version"]['baseScore'],
                        "matching" => $cpe,
                        "cots" => explode(':', $cpe)[4],
                        "publishedDate" => $cve['publishedDate'],
                        "lastModifiedDate" => $cve['lastModifiedDate'],
                    ];
                } catch (TypeError $e) {
                    $errors[$cve->getCve()] = $e->getMessage();
                }
                $parsed[$cpe][] = $cols;
            }
        }
        return [
            'errors' => $errors,
            'cpes' => $cpes,
            'cves' => $cves,
            'parsed' => $parsed
        ];
    }

    private function _sendEmail($subject, $content)
    {
        /** @noinspection DuplicatedCode */
        $emails = $this->configs[6]->getConfigValue();
        $emails = str_contains($emails, ',') ? explode(',', $emails) : [$emails];
        $user = $this->configs[1]->getConfigValue();
        $from = "R-MAX@imh-service.com";
        $pass = $this->configs[2]->getConfigValue();
        $server = $this->configs[3]->getConfigValue();
        $port = $this->configs[4]->getConfigValue();
        $dsn = "smtp://" . $user . ":" . $pass . "@" . $server . ":" . $port;
        $transport = Transport::fromDsn($dsn);
        foreach ($emails as $currentEmail) {
            $customMailer = new Mailer($transport);
            $email = (new TemplatedEmail())
                ->from(new Address($from, 'R-MAX'))
                ->subject($subject)
                ->text($content)
                ->context([]);
            $email->to($currentEmail);
            try {
                $customMailer->send($email);
                $this->session->getFlashBag()->add('success', 'Email sent successfully to ' . $currentEmail);
            } catch (TransportExceptionInterface $e) {
                $this->session->getFlashBag()->add('danger', 'Error while sending email to' . $currentEmail . ' :  ' . $e->getMessage());
            }
        }
    }

    private function _isKernel($cve): bool
    {
        if (
            isset($cve['matching']) && (trim(strtolower($cve['matching'])) == "cpe:2.3:o:linux:linux_kernel:5.4.2:*:*:*:*:*:*:*") &&
            isset($cve['base_score']) && ($cve['base_score'] >= 7) &&
            isset($cve['cve_description']) && (str_contains(strtolower($cve['cve_description']), 'usb')) &&
            isset($cve['attack_vector']) && (str_contains(strtolower($cve['attack_vector']), 'network') || str_contains(strtolower($cve['attack_vector']), 'adjacent'))
        ) {
            return true;
        }
        return false;
    }

    private function _isOthers($cve): bool
    {
        if (
            isset($cve['matching']) && (trim(strtolower($cve['matching'])) != "cpe:2.3:o:linux:linux_kernel:5.4.2:*:*:*:*:*:*:*") &&
            isset($cve['base_score']) && ($cve['base_score'] >= 7)
        ) {
            return true;
        }
        return false;
    }

    private function _updateCve($cve, $values)
    {
        $cve->setCve($values['cve']);
        $cve->setLink($values['link']);
        $cve->setCveDescription($values['cve_description']);
        $cve->setAttackVector($values['attack_vector']);
        $cve->setBaseScore($values['base_score']);
        $cve->setMatching($values['matching']);
        $cve->setCots($values['cots']);
        $this->entityManager->persist($cve);
        $this->entityManager->flush();
    }

    #[Route('/nvd/update', name: 'nvd_update')]
    public function update(): RedirectResponse
    {
        $cpeList = $this->entityManager->getRepository(Cpe::class)->findAll();
        $cpeParseResult = $this->_parseCpes($cpeList);
        $cpesCves = $cpeParseResult['parsed'];
        $errors = $cpeParseResult['errors'];
        $updated = [];
        $created = [];
        $skipped = [];
        foreach ($cpesCves as $cpe => $cpeCves) {
            foreach ($cpeCves as $cveColsData) {
                /** @var Cve $currentCve */
                $currentCve = $this->entityManager->getRepository(Cve::class)->findOneBy(['cve' => $cveColsData['cve']]);
                if ($currentCve) {
                    if (
                        trim(strtolower($currentCve->getAnalysisStatus())) !== "analysis completed"
                    ) {
                        $this->_updateCve($currentCve, $cveColsData);
                        $updated[$cpe][] = $cveColsData;
                    } else {
                        $skipped[$cpe][] = $cveColsData;
                    }
                } else {
                    $this->_updateCve(new Cve(), $cveColsData);
                    $created[$cpe][] = $cveColsData;
                }
            }
        }
        $message = "";
        foreach ($errors as $errorKey => $errorMessage) {
            $message .= "$errorKey : $errorMessage <br>";
        }
        if (!empty($message)) {
            $this->session->getFlashBag()->add('danger', $message);
        }
        $message = "";
        foreach ($cpeList as $cpe) {
            $cpe = $cpe->getCpe();
            $message .= (isset($created[$cpe]) ? "Inserted $cpe : " . (count($created[$cpe]) + 1) . "<br>" : "");
            $message .= (isset($updated[$cpe]) ? "Updated $cpe : " . (count($updated[$cpe]) + 1) . "<br>" : "");
            $message .= (isset($skipped[$cpe]) ? "Skipped $cpe : " . (count($created[$cpe]) + 1) . "<br>" : "");
        }
        if (!empty($message)) {
            $this->session->getFlashBag()->add('success', $message);
        }
        return $this->redirectToRoute('admin');
    }

    /** @noinspection PhpUnhandledExceptionInspection
     * @noinspection SqlDialectInspection
     * @noinspection PhpDeprecationInspection
     */
    #[Route('/database/clear', name: 'clear_database')]
    public function clearDatabase(): RedirectResponse
    {
        $this->entityManager->getConnection()->executeUpdate(
            "DELETE FROM cve"
        );
        $this->entityManager->flush();
        $this->entityManager->getConnection()->executeUpdate(
            "DELETE FROM export"
        );
        $this->entityManager->flush();
        $this->entityManager->getConnection()->executeUpdate(
            "DELETE FROM import"
        );
        $this->entityManager->flush();
        return $this->redirectToRoute('admin');
    }

    private function _sendTestingEmail() {
        /** @noinspection DuplicatedCode */
        $emails = $this->configs[6]->getConfigValue();
        $emails = str_contains($emails, ',') ? explode(',', $emails) : [$emails];
        $user = $this->configs[1]->getConfigValue();
        $from = "R-MAX@imh-service.com";
        $pass = $this->configs[2]->getConfigValue();
        $server = $this->configs[3]->getConfigValue();
        $port = $this->configs[4]->getConfigValue();
        $dsn = "smtp://" . $user . ":" . $pass . "@" . $server . ":" . $port;
        $transport = Transport::fromDsn($dsn);
        foreach ($emails as $currentEmail) {
            $customMailer = new Mailer($transport);
            $email = (new TemplatedEmail())
                ->from(new Address($from, 'R-MAX'))
                ->subject('R-MAX TESTING EMAIL')
                ->text('Hello from R-MAX')
                ->context([]);
            $email->to($currentEmail);
            try {
                $customMailer->send($email);
                $this->session->getFlashBag()->add('success', 'Email sent successfully to ' . $currentEmail);
            } catch (TransportExceptionInterface $e) {
                $this->session->getFlashBag()->add('danger', 'Error :  ' . $e->getMessage());
            }
        }
    }

    #[Route('/email/test', name: 'test_email_sending')]
    public function testEmailSending(): RedirectResponse
    {
        $this->_sendTestingEmail();
        return $this->redirectToRoute('admin');
    }
}
