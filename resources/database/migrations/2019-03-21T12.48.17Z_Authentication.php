<?php // 2019-03-21T12.48.17Z_Authentication.php

namespace Breyta\Migration;

use Breyta\AbstractMigration;

class Authentication extends AbstractMigration
{
    public function up(): void
    {
        $this->exec("CREATE TYPE account_status AS ENUM ('pending', 'activated', 'disabled', 'archived')");

        $this->exec("ALTER TABLE users 
            DROP CONSTRAINT users_name_uindex,
            ADD CONSTRAINT user_display_name_uindex UNIQUE (display_name),
            ADD COLUMN account_status account_status NOT NULL DEFAULT 'pending'");

        $this->exec("CREATE TABLE activation_tokens (
            id SERIAL NOT NULL PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(20) NOT NULL,
            valid_until TIMESTAMP NOT NULL,
            CONSTRAINT activation_tokens_token_uindex UNIQUE (token),
            FOREIGN KEY (user_id) REFERENCES users (id)
                ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE activation_codes (
            id SERIAL NOT NULL PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(6) NOT NULL,
            valid_until TIMESTAMP NOT NULL,
            CONSTRAINT activation_codes_token_uindex UNIQUE (token),
            FOREIGN KEY (user_id) REFERENCES users (id)
                ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE password_reset_tokens (
            id SERIAL NOT NULL PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(20) NOT NULL,
            valid_until TIMESTAMP NOT NULL,
            CONSTRAINT password_reset_tokens_token_uindex UNIQUE (token),
            FOREIGN KEY (user_id) REFERENCES users (id)
                ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE remember_tokens (
            id SERIAL NOT NULL PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(20) NOT NULL,
            valid_until TIMESTAMP NOT NULL,
            browser VARCHAR(50),
            version VARCHAR(10),
            location VARCHAR(50),
            CONSTRAINT remember_tokens_token_uindex UNIQUE (token),
            FOREIGN KEY (user_id) REFERENCES users (id)
                ON DELETE CASCADE
        )");
    }

    public function down(): void
    {
        $this->exec("ALTER TABLE users 
            DROP CONSTRAINT user_display_name_uindex,
            ADD CONSTRAINT users_name_uindex UNIQUE (name),
            DROP COLUMN account_status");
        $this->exec("DROP TYPE account_status");

        $this->exec("DROP TABLE activation_tokens");
        $this->exec("DROP TABLE activation_codes");
        $this->exec("DROP TABLE password_reset_tokens");
        $this->exec("DROP TABLE remember_tokens");
    }
}
