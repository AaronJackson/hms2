<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20161024103730_add_initial_member_role_and_viewing_permission extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO roles VALUES (1, \'member.approval\')');
        $this->addSql('INSERT INTO roles VALUES (2, \'member.payment\')');
        $this->addSql('INSERT INTO roles VALUES (3, \'member.young\')');
        $this->addSql('INSERT INTO roles VALUES (4, \'member.current\')');
        $this->addSql('INSERT INTO roles VALUES (5, \'member.ex\')');
        $this->addSql('INSERT INTO permissions VALUES (1, \'view.self\')');
        $this->addSql('INSERT INTO permission_role VALUES (1, 1), (2, 1), (3, 1), (4, 1), (5, 1)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // don't need to delete from permission_role table as this is taken care of by the cascade
        $this->addSql('DELETE FROM permissions WHERE id IN (1)');
        $this->addSql('DELETE FROM roles WHERE id IN (1,2,3,4,5)');
    }
}
