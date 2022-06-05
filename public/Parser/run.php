<?php /** @noinspection ALL */
/**
 * Licensed under Creative Commons 3.0 Attribution
 * Copyright Adam Wulf 2013
 */

include("config.php");
include("include.classloader.php");

$classLoader->addToClasspath(ROOT);

$mysql = new MySQLConn(DATABASE_HOST, DATABASE_NAME, DATABASE_USER, DATABASE_PASS);

$db = new JSONtoMYSQL($mysql);

$tables = [
    "generic",
    "generic_CVE_Items",
    "generic_CVE_Items_cve",
    "generic_CVE_Items_cve_CVE_data_meta",
    "generic_CVE_Items_cve_problemtype",
    "generic_CVE_Items_cve_problemtype_problemtype_data",
    "generic_CVE_Items_cve_problemtype_problemtype_data_description",
    "generic_CVE_Items_cve_references",
    "generic_CVE_Items_cve_references_reference_data",
    "generic_CVE_Items_cve_references_reference_data_tags",
    "generic_CVE_Items_cve_description",
    "generic_CVE_Items_cve_description_description_data",
    "generic_CVE_Items_configurations",
    "generic_CVE_Items_configurations_nodes",
    "generic_CVE_Items_configurations_nodes_children",
    "generic_CVE_Items_configurations_nodes_cpe_match",
    "generic_CVE_Items_configurations_nodes_cpe_match_cpe_name",
    "generic_CVE_Items_impact",
    "generic_CVE_Items_impact_baseMetricV3",
    "generic_CVE_Items_impact_baseMetricV3_cvssV3",
    "generic_CVE_Items_impact_baseMetricV2",
    "generic_CVE_Items_impact_baseMetricV2_cvssV2",
];

if (isset($_GET['extract']) && is_numeric($_GET['extract'])) {
    $file = "Files/nvdcve-1.1-$_GET[extract].json";
    if (is_file($file)) {
        $content = file_get_contents($file);
        $data = json_decode($content);
        var_dump($data);
    } else {
        die("Error while reading $file");
    }
}

if (isset($_GET['download']) && is_numeric($_GET['download'])) {
    $file = "Files/nvdcve-1.1-$_GET[download].json.zip";
    if (file_put_contents("Files/$_GET[download]",
        fopen("https://nvd.nist.gov/feeds/json/cve/1.1/$_GET[download]",
            'r'
        )
    )) {
        if (is_file($file)) {
            $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
            $zip = new ZipArchive;
            $res = $zip->open($file);
            if ($res === TRUE) {
                $zip->extractTo($path);
                $zip->close();
                if (unlink($file)) {
                    die("Success");
                } else {
                    die("Error while removing $file");
                }
            } else {
                die("Error while reading $file");
            }
        } else {
            die("Error while unzipping $file");
        }
    } else {
        die("Error while downloading $file");
    }
}


// create some json

//$obj = json_decode('{"id":4,"asdf" : "asfd"}');

// save it to a table
$db->save($obj, "brandnewtable");

