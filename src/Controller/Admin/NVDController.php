<?php

namespace App\Controller\Admin;

use App\Entity\CpeList;
use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    public function __construct(SessionInterface $session, ParameterBagInterface $parameterBag, HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->parameterBag = $parameterBag;
        $this->nvdFilesPath = $this->parameterBag->get('kernel.project_dir') . "/public/nvd_files/";
        $this->session = $session;
    }

    #[Route('/nvd', name: 'nvd')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('admin');
    }

    #[Route('/nvd/check', name: 'nvd_check')]
    public function check()//: RedirectResponse
    {
        return null;
    }

    #[Route('/nvd/update', name: 'nvd_update')]
    public function update(): RedirectResponse
    {
        $parsed = [];
        $cves = [];
        $errors = [];
        $cpes = $this->entityManager->getRepository(CpeList::class)->findAll();
        foreach ($cpes as $cpe) {
            $path = shell_exec('python3 C:\Users\MSI\Documents\projects\nvdlib\main.py "' . $cpe->getCpe() . '"');
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
        $updated = [];
        $created = [];
        $skipped = [];
        foreach ($parsed as $cpe => $cpeCves) {
            foreach ($cpeCves as $item) {
                /** @var File $cve */
                $cve = $this->entityManager->getRepository(File::class)->findOneBy(['cve' => $item['cve']]);
                if ($cve) {
                    if (
                        empty($item['analysis_status']) &&
                        empty($item['analysis_date']) &&
                        empty($item['applicability_status']) &&
                        empty($item['applicability_rationale']) &&
                        empty($item['consequence']) &&
                        empty($item['operational_impact_level']) &&
                        empty($item['cve_condition']) &&
                        empty($item['exploit_likelihood'])
                    ) {
                        $this->_updateCve($cve, $item);
                        $updated[$cpe][] = $item;
                    } else {
                        $skipped[$cpe][] = $item;
                    }
                } else {
                    $this->_updateCve(new File(), $item);
                    $created[$cpe][] = $item;
                }
            }
        }
        $message = "";
        foreach ($errors as $errorKey => $errorMessage) {
            $message .= "$errorKey : $errorMessage <br>";
        }
        $this->session->getFlashBag()->add('warning', $message);
        $message = "";
        foreach ($cpes as $cpe) {
            $cpe = $cpe->getCpe();
            $message .= (isset($created[$cpe]) ? "Inserted $cpe : " . (count($created[$cpe]) + 1) . "<br>" : "");
            $message .= (isset($updated[$cpe]) ? "Updated $cpe : " . (count($updated[$cpe]) + 1) . "<br>" : "");
            $message .= (isset($skipped[$cpe]) ? "Skipped $cpe : " . (count($created[$cpe]) + 1) . "<br>" : "");
        }
        $this->session->getFlashBag()->add('success', $message);
        return $this->redirectToRoute('admin');
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
}
