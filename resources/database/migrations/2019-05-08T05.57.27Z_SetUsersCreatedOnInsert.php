<?php // 2019-05-08T05.57.27Z_SetUsersCreatedOnInsert.php

namespace Breyta\Migration;

use Breyta\AbstractMigration;

class SetUsersCreatedOnInsert extends AbstractMigration
{
    public function up(): void
    {
        $this->exec("ALTER TABLE users 
            ALTER COLUMN created SET DEFAULT NOW(),
            ALTER COLUMN updated SET DEFAULT NOW()");
    }

    public function down(): void
    {
        $this->exec("ALTER TABLE users
            ALTER COLUMN created DROP DEFAULT,
            ALTER COLUMN updated DROP DEFAULT");
    }
}
