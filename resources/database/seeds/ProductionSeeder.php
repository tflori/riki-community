<?php

namespace Seeder;

class ProductionSeeder extends AbstractSeeder
{
    public function sprout()
    {
         $this->seed(PermissionSeeder::class);
    }
}
