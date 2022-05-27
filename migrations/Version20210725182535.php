<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210725182535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
        -- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
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
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_key` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cpe_list`
--

DROP TABLE IF EXISTS `cpe_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cpe_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `cpe` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpe_list`
--

LOCK TABLES `cpe_list` WRITE;
/*!40000 ALTER TABLE `cpe_list` DISABLE KEYS */;
INSERT INTO `cpe_list` (`id`, `name`, `version`, `cpe`) VALUES (1,'iptables ','1.8.3','cpe:2.3:a:netfilter:iptables:1.8.3:*:*:*:*:*:*:*'),(2,'strongswan ','5.8.1','cpe:2.3:a:strongswan:strongswan:5.8.1:*:*:*:*:*:*:*'),(3,'openssl ','1.1.1d','cpe:2.3:a:openssl:openssl:1.1.1d:*:*:*:*:*:*:*'),(4,'bash ','5.0','cpe:2.3:a:gnu:bash:5.0:-:*:*:*:*:*:*'),(5,'curl ','7.66.0','cpe:2.3:a:haxx:curl:7.66.0:*:*:*:*:*:*:*'),(6,'NetworkManager ','1.18.4','cpe:2.3:a:gnome:networkmanager:1.18.4:*:*:*:*:*:*:*'),(7,'dnsmasq ','2.80','cpe:2.3:a:thekelleys:dnsmasq:2.80:*:*:*:*:*:*:*'),(8,'GCC','9.2','cpe:2.3:a:gnu:gcc:9.2.0:*:*:*:*:*:*:*'),(9,'GNUTLS ','3.6.8','cpe:2.3:a:gnu:gnutls:3.6.8:*:*:*:*:*:*:*'),(10,'glibc ','2,3','cpe:2.3:a:gnu:glibc:2.30:*:*:*:*:*:*:*'),(11,'Linux Kernel ','5.4.2','cpe:2.3:o:linux:linux_kernel:5.4.2:*:*:*:*:*:*:*'),(12,'Libqmi ','1.24.2','cpe:2.3:a:freedesktop:libqmi:1.24.2:*:*:*:*:*:*:*'),(13,'ModemManager ','1.12.4','ModemManager ');
/*!40000 ALTER TABLE `cpe_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `export`
--

DROP TABLE IF EXISTS `export`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `export` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(2500) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `export`
--

LOCK TABLES `export` WRITE;
/*!40000 ALTER TABLE `export` DISABLE KEYS */;
INSERT INTO `export` (`id`, `path`, `name`, `type`) VALUES (1,'/uploads/test-.xlsx','test','DATA'),(2,'/uploads/test2-6290d9485659e.xlsx','test2','DATA'),(3,'/uploads/test3-6290dc617cbdf.xlsx','test3','DATA');
/*!40000 ALTER TABLE `export` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cve` text DEFAULT NULL,
  `link` text DEFAULT NULL,
  `cve_description` text DEFAULT NULL,
  `attack_vector` text DEFAULT NULL,
  `base_score` text DEFAULT NULL,
  `matching` text DEFAULT NULL,
  `cots` text DEFAULT NULL,
  `analysis_status` text DEFAULT NULL,
  `analysis_date` text DEFAULT NULL,
  `applicability_status` text DEFAULT NULL,
  `applicability_rationale` text DEFAULT NULL,
  `consequence` text DEFAULT NULL,
  `operational_impact_level` text DEFAULT NULL,
  `cve_condition` text DEFAULT NULL,
  `exploit_likelihood` text DEFAULT NULL,
  `exploit_likelihood_rationale` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file`
--

LOCK TABLES `file` WRITE;
/*!40000 ALTER TABLE `file` DISABLE KEYS */;
INSERT INTO `file` (`id`, `cve`, `link`, `cve_description`, `attack_vector`, `base_score`, `matching`, `cots`, `analysis_status`, `analysis_date`, `applicability_status`, `applicability_rationale`, `consequence`, `operational_impact_level`, `cve_condition`, `exploit_likelihood`, `exploit_likelihood_rationale`) VALUES (4,'CVE-2022-30333','https://nvd.nist.gov/vuln/detail/CVE-2022-30333','RARLAB UnRAR before 6.12 on Linux and UNIX allows directory traversal to write to files during an extract (aka unpack) operation, as demonstrated by creating a ~/.ssh/authorized_keys file. NOTE: WinRAR and Android RAR are unaffected.','Network (N)','9.8','cpe:2.3:a:netfilter:iptables:1.8.3:*:*:*:*:*:*:*','iptables','Analysis complete','1212122022年5月9日','1','applicable due to blabla','blabla','Medium','blabla','Unlikly','blabla'),(5,'CVE-2022-30333','https://nvd.nist.gov/vuln/detail/CVE-2022-30333','RARLAB UnRAR before 6.12 on Linux and UNIX allows directory traversal to write to files during an extract (aka unpack) operation, as demonstrated by creating a ~/.ssh/authorized_keys file. NOTE: WinRAR and Android RAR are unaffected.','Network (N)','9.8','cpe:2.3:a:netfilter:iptables:1.8.3:*:*:*:*:*:*:*','iptables','Analysis complete','1212122022年5月9日','1','applicable due to blabla','blabla','Medium','blabla','Unlikly','blabla');
/*!40000 ALTER TABLE `file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_field`
--

DROP TABLE IF EXISTS `file_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_field`
--

LOCK TABLES `file_field` WRITE;
/*!40000 ALTER TABLE `file_field` DISABLE KEYS */;
INSERT INTO `file_field` (`id`, `key`, `label`) VALUES (2,'cve','CVE Id'),(3,'link','Link (NVD NIST)'),(4,'cve_description','Description (from NVD NIST)'),(5,'attack_vector','Attack Vector - AV (from NDV NIST)'),(6,'base_score','Base score - CVSS 3.X Severity (from NDV NIST)'),(7,'matching','Matching CPEs (from CPE-List file)'),(8,'cots','COTS name (from CPE-List file)'),(9,'analysis_status','Analysis status'),(10,'analysis_date','Analysis date'),(11,'applicability_status','Applicability status'),(12,'applicability_rationale','Applicability rationale'),(13,'consequence','Consequence'),(14,'operational_impact_level','Operational impact level'),(15,'cve_condition','Condition'),(16,'exploit_likelihood','Exploit likelihood'),(17,'exploit_likelihood_rationale','Exploit likelihood rationale');
/*!40000 ALTER TABLE `file_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import`
--

DROP TABLE IF EXISTS `import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(2500) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import`
--

LOCK TABLES `import` WRITE;
/*!40000 ALTER TABLE `import` DISABLE KEYS */;
INSERT INTO `import` (`id`, `path`, `name`, `type`) VALUES (1,'old-vuln-analysis-83-d488a0a18f81c9c2e8ad578498edad6b13cf5796.csv','test','DATA'),(2,'old-vuln-analysis-83-d488a0a18f81c9c2e8ad578498edad6b13cf5796.csv','test2','DATA');
/*!40000 ALTER TABLE `import` ENABLE KEYS */;
UNLOCK TABLES;

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

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` (`id`, `username`, `roles`, `password`) VALUES (1,'admin','[\"ROLE_ADMIN\", \"ROLE_USER\"]','$2y$13$BI3xOTSdju6IJaUXxrP1hOqcjhAGAWfmkAxSxjHy1JjawRTkn3Wdu'),(3,'tarek','[]','$2y$13$ekEgI.IUojo8OaZXPC0rEeRZL1CWLuI6dfP2MWlIRIwWdD8Is5cpq');
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-05-27 21:04:06
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE cpe_list');
        $this->addSql('DROP TABLE export');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE file_field');
        $this->addSql('DROP TABLE import');
        $this->addSql('DROP TABLE config');
    }
}
