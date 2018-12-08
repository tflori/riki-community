<?php // 2018-12-03T07.40.23Z_CreateUsersTable.php

namespace Breyta\Migration;

use Breyta\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    public function up(): void
    {
        $this->exec('CREATE TABLE users (
            id SERIAL NOT NULL PRIMARY KEY,
            name VARCHAR(255),
            display_name VARCHAR(20),
            password VARCHAR(64),
            email VARCHAR(255),
            created TIMESTAMP NOT NULL,
            updated TIMESTAMP NOT NULL,
            CONSTRAINT users_name_uindex UNIQUE (name),
            CONSTRAINT users_email_uindex UNIQUE (email)
        )');
    }

    public function down(): void
    {
        $this->exec('DROP TABLE users');
    }
}
