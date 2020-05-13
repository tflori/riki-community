<?php // 2020-05-07T22.56.57Z_AlterUsersTable.php

namespace Breyta\Migration;

use App\Application;
use Breyta\AbstractMigration;
use Community\Model\User;
use Hugga\Input\Question\Confirmation;

class AlterUsersTable extends AbstractMigration
{
    public function up(): void
    {
        $this->checkForEmptyUsers();

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

    private function checkForEmptyUsers()
    {
        $count = User::query()->where('displayName IS NULL')->orWhere('email IS NULL')->count();
        if ($count > 0) {
            $console = Application::console();
            if ($console->ask(new Confirmation(
                'There are ' . $count . ' users without displayName or email. ${red}Remove them?${r}' . PHP_EOL,
                false
            ))) {
                $console->line('ok we remove them!');
            }
        } else {
            $console = Application::console();
            $console->line('no users without dn or email');
        }
        //$this->exec('DELETE FROM users WHERE display_name IS NULL OR email IS NULL');
    }
}
