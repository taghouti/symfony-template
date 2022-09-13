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
        $this->addSql("create table config
        (
            id      int auto_increment
                primary key,
            config_name varchar(255) null,
            config_value varchar(255) null
        );");

        $this->addSql("create table cpe
        (
            id      int auto_increment
                primary key,
            name    varchar(255) not null,
            version varchar(255) not null,
            cpe     varchar(255) not null
        );");

        $this->addSql("create table cve
        (
            id                           int auto_increment
                primary key,
            cve                          text null,
            link                         text null,
            cve_description              text null,
            attack_vector                text null,
            base_score                   text null,
            matching                     text null,
            cots                         text null,
            analysis_status              text null,
            analysis_date                text null,
            applicability_status         text null,
            applicability_rationale      text null,
            consequence                  text null,
            operational_impact_level     text null,
            cve_condition                text null,
            exploit_likelihood           text null,
            exploit_likelihood_rationale text null,
            created DATETIME NOT NULL, 
            updated DATETIME NOT NULL
        );");

        $this->addSql("create table export
        (
            id   int auto_increment
                primary key,
            path varchar(2500) not null,
            name varchar(255)  not null,
            type varchar(255)  not null
        );");

        $this->addSql("create table field
        (
            id    int auto_increment
                primary key,
            name  varchar(255) not null,
            label varchar(255) not null
        );");

        $this->addSql("create table import
        (
            id   int auto_increment
                primary key,
            path varchar(2500) not null,
            name varchar(255)  not null,
            type varchar(255)  not null
        );
        ");

        $this->addSql("create table member
        (
            id       int auto_increment
                primary key,
            username varchar(180)                 not null,
            roles    longtext collate utf8mb4_bin not null,
            password varchar(255)                 not null,
            constraint UNIQ_70E4FA78F85E0677
                unique (username),
            constraint roles
                check (json_valid(`roles`))
        )
            collate = utf8mb4_unicode_ci;");

        $this->addSql("
        INSERT INTO `cpe` (`id`, `name`, `version`, `cpe`) VALUES (1,'iptables','1.8.3','cpe:2.3:a:netfilter:iptables:1.8.3:*:*:*:*:*:*:*'),(2,'strongswan ','5.8.1','cpe:2.3:a:strongswan:strongswan:5.8.1:*:*:*:*:*:*:*'),(3,'openssl ','1.1.1d','cpe:2.3:a:openssl:openssl:1.1.1d:*:*:*:*:*:*:*'),(4,'bash ','5.0','cpe:2.3:a:gnu:bash:5.0:-:*:*:*:*:*:*'),(5,'curl ','7.66.0','cpe:2.3:a:haxx:curl:7.66.0:*:*:*:*:*:*:*'),(6,'NetworkManager ','1.18.4','cpe:2.3:a:gnome:networkmanager:1.18.4:*:*:*:*:*:*:*'),(7,'dnsmasq ','2.80','cpe:2.3:a:thekelleys:dnsmasq:2.80:*:*:*:*:*:*:*'),(8,'GCC','9.2','cpe:2.3:a:gnu:gcc:9.2.0:*:*:*:*:*:*:*'),(9,'GNUTLS ','3.6.8','cpe:2.3:a:gnu:gnutls:3.6.8:*:*:*:*:*:*:*'),(10,'glibc ','2,3','cpe:2.3:a:gnu:glibc:2.30:*:*:*:*:*:*:*'),(11,'Linux Kernel ','5.4.2','cpe:2.3:o:linux:linux_kernel:5.4.2:*:*:*:*:*:*:*'),(12,'Libqmi ','1.24.2','cpe:2.3:a:freedesktop:libqmi:1.24.2:*:*:*:*:*:*:*');
        INSERT INTO `config` (`id`, `config_name`, `config_value`) VALUES (1,'API KEY','b5d8d7c4-1f93-4584-9ef3-7855af11a960');
        INSERT INTO `config` (`id`, `config_name`, `config_value`) VALUES (2,'SMTP USER','sobmti.smtp@gmail.com');
        INSERT INTO `config` (`id`, `config_name`, `config_value`) VALUES (3,'SMTP PASS','hbcmhxltotrtllay');
        INSERT INTO `config` (`id`, `config_name`, `config_value`) VALUES (4,'SMTP HOST','smtp.gmail.com');
        INSERT INTO `config` (`id`, `config_name`, `config_value`) VALUES (5,'SMTP PORT','587');
        INSERT INTO `config` (`id`, `config_name`, `config_value`) VALUES (6,'SMTP PROTOCOL','SSL/TLS');
        INSERT INTO `config` (`id`, `config_name`, `config_value`) VALUES (7,'EMAILS (seperated by comma ,)','imed.mh@imh-groupe.com,imed.meddeb-hamrouni.external@airbus.com,taghoutitarek@gmail.com');
        INSERT INTO `config` (`id`, `config_name`, `config_value`) VALUES (8,'DAYS 1 for Sunday (seperated by comma ,)','1,2,3,4,5,6,7');
        INSERT INTO `member` (`id`, `username`, `roles`, `password`) VALUES (1,'admin','[\"ROLE_ADMIN\", \"ROLE_USER\"]','$2y$13\$BI3xOTSdju6IJaUXxrP1hOqcjhAGAWfmkAxSxjHy1JjawRTkn3Wdu'),(3,'tarek','{\"0\":\"ROLE_USER\",\"2\":\"ROLE_USER\"}','$2y$13\$AIqnldsp2vLri/6.d.oIKeZLg55dC0a04WI7kwgvVVGasmCvmO/PW');
        INSERT INTO `field` (`id`, `name`, `label`) VALUES (2,'cve','CVE Id'),(3,'link','Link (NVD NIST)'),(4,'cve_description','Description (from NVD NIST)'),(5,'attack_vector','Attack Vector - AV (from NDV NIST)'),(6,'base_score','Base score - CVSS 3.X Severity (from NDV NIST)'),(7,'matching','Matching CPEs (from CPE-List file)'),(8,'cots','COTS name (from CPE-List file)'),(9,'analysis_status','Analysis status'),(10,'analysis_date','Analysis date'),(11,'applicability_status','Applicability status'),(12,'applicability_rationale','Applicability rationale'),(13,'consequence','Consequence'),(14,'operational_impact_level','Operational impact level'),(15,'cve_condition','Condition'),(16,'exploit_likelihood','Exploit likelihood'),(17,'exploit_likelihood_rationale','Exploit likelihood rationale');");

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
