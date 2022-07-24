<?php

namespace App\Controller\Admin;

use App\Entity\Cpe;
use App\Entity\Cve;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TypeError;

class NVDController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $client;
    private ParameterBagInterface $parameterBag;
    private string $nvdFilesPath;
    private SessionInterface $session;
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer, SessionInterface $session, ParameterBagInterface $parameterBag, HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        set_time_limit(3600);
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->parameterBag = $parameterBag;
        $this->nvdFilesPath = $this->parameterBag->get('kernel.project_dir') . "/nvdlib/main.py";
        $this->session = $session;
        $this->mailer = $mailer;
    }

    #[Route('/nvd', name: 'nvd')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('admin');
    }

    #[Route('/nvd/check', name: 'nvd_check')]
    public function check(): RedirectResponse
    {
        $cpeList = $this->entityManager->getRepository(Cpe::class)->findAll();
        $cpesCves = $this->_parseCpes($cpeList)['parsed'];
        $kernel['creation'] = [];
        $kernel['update'] = [];
        $others['creation'] = [];
        $others['update'] = [];
        foreach ($cpesCves as $cpeCves) {
            foreach ($cpeCves as $cveColsData) {
                /** @var Cve $currentCve */
                $currentCve = $this->entityManager->getRepository(Cve::class)->findOneBy(['cve' => $cveColsData['cve']]);
                if ($currentCve) {
                    $currentCveArray = (array)$currentCve;
                    if ($this->_isKernel($currentCveArray)) {
                        $kernel['update'][] = $cveColsData;
                    } else if ($this->_isOthers($currentCveArray)) {
                        $others['update'][] = $cveColsData;
                    }
                    if (
                        empty($cveColsData['analysis_status']) &&
                        empty($cveColsData['analysis_date']) &&
                        empty($cveColsData['applicability_status']) &&
                        empty($cveColsData['applicability_rationale']) &&
                        empty($cveColsData['consequence']) &&
                        empty($cveColsData['operational_impact_level']) &&
                        empty($cveColsData['cve_condition']) &&
                        empty($cveColsData['exploit_likelihood'])
                    ) {
                        $this->_updateCve($currentCve, $cveColsData);
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
        foreach ($cvesTypes as $cvesType) {
            foreach ($cvesType as $cveStatus => $cve) {
                if (is_array($cve) && isset($cve['matching'])) {
                    $version = explode(':', $cve['matching'])[5];
                    $currentMessage = "A vulnerability $cveStatus detected : $cve[cve] on the $cve[cots] $version with severity Score $cve[base_score]<br>";
                    $message .= $currentMessage;
                    $this->_sendEmail("[$cve[cve]] A vulnerability $cveStatus detected", $currentMessage);
                }
            }
        }
        if (!empty(trim($message))) {
            $this->session->getFlashBag()->add('success', $message);
        }
        return $this->redirectToRoute('admin');
    }

    #[ArrayShape(['errors' => "array", 'cpes' => "mixed", 'cves' => "array", 'parsed' => "array"])] private function _parseCpes($cpes): array
    {
        $parsed = [];
        $cves = [];
        $errors = [];
        foreach ($cpes as $cpe) {
            $path = shell_exec('python3 ' . $this->nvdFilesPath . ' "' . $cpe->getCpe() . '" "b5d8d7c4-1f93-4584-9ef3-7855af11a960"');
            $path = str_replace(array("\r", "\n"), '', $path);
            if (!is_file($path)) {
                $errors[$cpe->getCpe()] = $path;
                continue;
            }
            $output = file_get_contents($path);
            $output = str_replace(array("\r", "\n"), '', $output);
            $data = json_decode($output, true);
            if ($output == "[]") $cves[$cpe->getCpe()] = [];
            else {
                if ($data) $cves[$cpe->getCpe()] = $data;
                else $errors[$cpe->getCpe()] = $output;
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
                    ];
                } catch (TypeError|ErrorException $e) {
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

    private function _sendEmail($subject, $content): void
    {
        $email = (new Email())
            ->from('nvd@imh-groupe.com')
            ->to('imed.mh@imh-groupe.com')
            ->cc('imed.meddeb-hamrouni.external@airbus.com')
            ->subject($subject)
            ->text($content);

        try {
            $this->mailer->send($email);
            return;
        } catch (TransportExceptionInterface $e) {
            return;
        }
    }

    private function _isKernel($cve): bool
    {
        if (!isset($cve['analysis_status']) || ($cve['analysis_status'] != 'Analysis complete')) {
            if (
                isset($cve['matching']) && (trim($cve['matching']) == "cpe:2.3:o:linux:linux_kernel:5.4.2:*:*:*:*:*:*:*") &&
                isset($cve['base_score']) && ($cve['base_score'] >= 7) &&
                isset($cve['cve_description']) && (str_contains($cve['cve_description'], 'usb')) &&
                isset($cve['attack_vector']) && (str_contains($cve['attack_vector'], 'Network') || str_contains($cve['attack_vector'], 'Adjacent'))
            ) {
                return true;
            }
        }
        return false;
    }

    private function _isOthers($cve): bool
    {
        if (!isset($cve['analysis_status']) || ($cve['analysis_status'] != 'Analysis complete')) {
            if (
                isset($cve['matching']) && (trim($cve['matching']) != "cpe:2.3:o:linux:linux_kernel:5.4.2:*:*:*:*:*:*:*") &&
                isset($cve['base_score']) && ($cve['base_score'] >= 7)
            ) {
                return true;
            }
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
                        empty($cveColsData['analysis_status']) &&
                        empty($cveColsData['analysis_date']) &&
                        empty($cveColsData['applicability_status']) &&
                        empty($cveColsData['applicability_rationale']) &&
                        empty($cveColsData['consequence']) &&
                        empty($cveColsData['operational_impact_level']) &&
                        empty($cveColsData['cve_condition']) &&
                        empty($cveColsData['exploit_likelihood'])
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
        if (!empty(trim($message))) {
            $this->session->getFlashBag()->add('warning', $message);
        }
        $message = "";
        foreach ($cpeList as $cpe) {
            $cpe = $cpe->getCpe();
            $message .= (isset($created[$cpe]) ? "Inserted $cpe : " . (count($created[$cpe]) + 1) . "<br>" : "");
            $message .= (isset($updated[$cpe]) ? "Updated $cpe : " . (count($updated[$cpe]) + 1) . "<br>" : "");
            $message .= (isset($skipped[$cpe]) ? "Skipped $cpe : " . (count($created[$cpe]) + 1) . "<br>" : "");
        }
        if (!empty(trim($message))) {
            $this->session->getFlashBag()->add('success', $message);
        }
        return $this->redirectToRoute('admin');
    }
}
