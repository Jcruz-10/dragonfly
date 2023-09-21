<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230920234205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add user entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $password = $this->hashPassword($_ENV['ADMIN_PASSWORD']);
        $this->addSql("INSERT INTO `user` VALUES (1, 'admin', '[\"ROLE_USER\", \"ROLE_ADMIN\"]', '$password')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
    }

    /**
     * Hash the initial admin password.
     */
    private function hashPassword(string $password): string
    {
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'auto'],
        ]);
        return $factory->getPasswordHasher('common')->hash($password);
    }
}
