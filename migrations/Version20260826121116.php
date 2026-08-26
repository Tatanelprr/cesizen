<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260826121116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, url_media VARCHAR(255) DEFAULT NULL, type VARCHAR(50) NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE activity_favorite (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, activity_id INT NOT NULL, INDEX IDX_C8CF3ED7A76ED395 (user_id), INDEX IDX_C8CF3ED781C06096 (activity_id), UNIQUE INDEX UNIQ_C8CF3ED7A76ED39581C06096 (user_id, activity_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_88BDF3E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME DEFAULT NULL, is_published TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, category_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_23A0E66989D9B62 (slug), INDEX IDX_23A0E6612469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE article_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE breathing_exercise (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, inhale INT NOT NULL, hold INT NOT NULL, exhale INT NOT NULL, description VARCHAR(255) DEFAULT NULL, is_default TINYINT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_C813B9A1A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE diagnostic_event (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, points INT NOT NULL, is_active TINYINT NOT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE diagnostic_threshold (id INT AUTO_INCREMENT NOT NULL, score_min INT NOT NULL, score_max INT NOT NULL, niveau VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, conseil LONGTEXT NOT NULL, code_color VARCHAR(7) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE emotion (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, code_color VARCHAR(7) NOT NULL, icon VARCHAR(50) DEFAULT NULL, is_active TINYINT NOT NULL, category_id INT NOT NULL, INDEX IDX_DEBC7712469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE emotion_category (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, code_color VARCHAR(7) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE journal_entry (id INT AUTO_INCREMENT NOT NULL, intensite SMALLINT NOT NULL, note_perso LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, user_id INT NOT NULL, emotion_id INT NOT NULL, INDEX IDX_C8FAAE5AA76ED395 (user_id), INDEX IDX_C8FAAE5A1EE4A582 (emotion_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activity_favorite ADD CONSTRAINT FK_C8CF3ED7A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE activity_favorite ADD CONSTRAINT FK_C8CF3ED781C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES article_category (id)');
        $this->addSql('ALTER TABLE breathing_exercise ADD CONSTRAINT FK_C813B9A1A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE emotion ADD CONSTRAINT FK_DEBC7712469DE2 FOREIGN KEY (category_id) REFERENCES emotion_category (id)');
        $this->addSql('ALTER TABLE journal_entry ADD CONSTRAINT FK_C8FAAE5AA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE journal_entry ADD CONSTRAINT FK_C8FAAE5A1EE4A582 FOREIGN KEY (emotion_id) REFERENCES emotion (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_favorite DROP FOREIGN KEY FK_C8CF3ED7A76ED395');
        $this->addSql('ALTER TABLE activity_favorite DROP FOREIGN KEY FK_C8CF3ED781C06096');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6612469DE2');
        $this->addSql('ALTER TABLE breathing_exercise DROP FOREIGN KEY FK_C813B9A1A76ED395');
        $this->addSql('ALTER TABLE emotion DROP FOREIGN KEY FK_DEBC7712469DE2');
        $this->addSql('ALTER TABLE journal_entry DROP FOREIGN KEY FK_C8FAAE5AA76ED395');
        $this->addSql('ALTER TABLE journal_entry DROP FOREIGN KEY FK_C8FAAE5A1EE4A582');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_favorite');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_category');
        $this->addSql('DROP TABLE breathing_exercise');
        $this->addSql('DROP TABLE diagnostic_event');
        $this->addSql('DROP TABLE diagnostic_threshold');
        $this->addSql('DROP TABLE emotion');
        $this->addSql('DROP TABLE emotion_category');
        $this->addSql('DROP TABLE journal_entry');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
