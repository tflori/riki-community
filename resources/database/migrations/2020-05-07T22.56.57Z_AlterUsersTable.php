<?php // 2020-05-07T22.56.57Z_AlterUsersTable.php

namespace Breyta\Migration;

use Breyta\AbstractMigration;

class AlterUsersTable extends AbstractMigration
{
    public function up(): void
    {
        $this->exec('ALTER TABLE users
            ALTER COLUMN email SET NOT NULL,
            ALTER COLUMN display_name SET NOT NULL');
    }

    public function down(): void
    {
        $this->exec('ALTER TABLE users
            ALTER COLUMN email DROP NOT NULL,
            ALTER COLUMN display_name DROP NOT NULL');
    }
}
