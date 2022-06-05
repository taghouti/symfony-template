<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220605162859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: nvd
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cpe`
--

DROP TABLE IF EXISTS `cpe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cpe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `cpe` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpe`
--

INSERT INTO `cpe` (`id`, `name`, `version`, `cpe`) VALUES (1,'iptables','1.8.3','cpe:2.3:a:netfilter:iptables:1.8.3:*:*:*:*:*:*:*'),(2,'strongswan ','5.8.1','cpe:2.3:a:strongswan:strongswan:5.8.1:*:*:*:*:*:*:*'),(3,'openssl ','1.1.1d','cpe:2.3:a:openssl:openssl:1.1.1d:*:*:*:*:*:*:*'),(4,'bash ','5.0','cpe:2.3:a:gnu:bash:5.0:-:*:*:*:*:*:*'),(5,'curl ','7.66.0','cpe:2.3:a:haxx:curl:7.66.0:*:*:*:*:*:*:*'),(6,'NetworkManager ','1.18.4','cpe:2.3:a:gnome:networkmanager:1.18.4:*:*:*:*:*:*:*'),(7,'dnsmasq ','2.80','cpe:2.3:a:thekelleys:dnsmasq:2.80:*:*:*:*:*:*:*'),(8,'GCC','9.2','cpe:2.3:a:gnu:gcc:9.2.0:*:*:*:*:*:*:*'),(9,'GNUTLS ','3.6.8','cpe:2.3:a:gnu:gnutls:3.6.8:*:*:*:*:*:*:*'),(10,'glibc ','2,3','cpe:2.3:a:gnu:glibc:2.30:*:*:*:*:*:*:*'),(11,'Linux Kernel ','5.4.2','cpe:2.3:o:linux:linux_kernel:5.4.2:*:*:*:*:*:*:*'),(12,'Libqmi ','1.24.2','cpe:2.3:a:freedesktop:libqmi:1.24.2:*:*:*:*:*:*:*');

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_key` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `api_key`) VALUES (1,'b5d8d7c4-1f93-4584-9ef3-7855af11a960');

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_70E4FA78F85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `username`, `roles`, `password`) VALUES (1,'admin','[\"ROLE_ADMIN\", \"ROLE_USER\"]','$2y$13\$BI3xOTSdju6IJaUXxrP1hOqcjhAGAWfmkAxSxjHy1JjawRTkn3Wdu'),(3,'tarek','{\"0\":\"ROLE_USER\",\"2\":\"ROLE_USER\"}','$2y$13\$AIqnldsp2vLri/6.d.oIKeZLg55dC0a04WI7kwgvVVGasmCvmO/PW');

--
-- Table structure for table `field`
--

DROP TABLE IF EXISTS `field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field`
--

INSERT INTO `field` (`id`, `name`, `label`) VALUES (2,'cve','CVE Id'),(3,'link','Link (NVD NIST)'),(4,'cve_description','Description (from NVD NIST)'),(5,'attack_vector','Attack Vector - AV (from NDV NIST)'),(6,'base_score','Base score - CVSS 3.X Severity (from NDV NIST)'),(7,'matching','Matching CPEs (from CPE-List file)'),(8,'cots','COTS name (from CPE-List file)'),(9,'analysis_status','Analysis status'),(10,'analysis_date','Analysis date'),(11,'applicability_status','Applicability status'),(12,'applicability_rationale','Applicability rationale'),(13,'consequence','Consequence'),(14,'operational_impact_level','Operational impact level'),(15,'cve_condition','Condition'),(16,'exploit_likelihood','Exploit likelihood'),(17,'exploit_likelihood_rationale','Exploit likelihood rationale');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-06-05 17:34:36
");

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE import');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE export');
        $this->addSql('DROP TABLE cve');
        $this->addSql('DROP TABLE cpe');
        $this->addSql('DROP TABLE config');
    }
}
