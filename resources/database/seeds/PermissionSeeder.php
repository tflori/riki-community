<?php

namespace Seeder;

use Community\Model\Permission;

class PermissionSeeder extends AbstractSeeder
{
    public function sprout()
    {
        $bulkInsert = $this->em->useBulkInserts(Permission::class);

        foreach ($this->getPermissions() as $key => $data) {
            if ($this->em->fetch(Permission::class)->where('key', $key)->count() === 0) {
                $bulkInsert->add(new Permission(array_merge([
                    'description' => null,
                    'category' => Permission::CATEGORY_ULTIMATE,
                ], $data, [
                    'key' => $key,
                ])));
            }
        }

        $this->em->finishBulkInserts(Permission::class);
    }

    protected function getPermissions()
    {
        return [
            'user:edit' => ['description' => 'Can edit a user including name, email and password.'],
            'user:editSelf' => ['description' => 'Can edit his own user.', 'category' => 'ordinary'],
        ];
    }
}
