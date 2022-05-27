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
        $this->addSql('create table cpe_list
(
    id      int auto_increment
        primary key,
    name    varchar(255) not null,
    version varchar(255) not null,
    cpe     varchar(255) not null
);

create table doctrine_migration_versions
(
    version        varchar(191) not null
        primary key,
    executed_at    datetime     null,
    execution_time int          null
)
    collate = utf8_unicode_ci;

create table export
(
    id   int auto_increment
        primary key,
    path varchar(2500) not null,
    name varchar(255)  not null,
    type varchar(255)  not null
);

create table file
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
    exploit_likelihood_rationale text null
);

create table file_field
(
    id    int auto_increment
        primary key,
    `key` varchar(255) not null,
    label varchar(255) not null
);

create table import
(
    id   int auto_increment
        primary key,
    path varchar(2500) not null,
    name varchar(255)  not null,
    type varchar(255)  not null
);

create table member
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
    collate = utf8mb4_unicode_ci;

');
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
    }
}
