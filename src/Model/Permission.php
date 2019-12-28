<?php

namespace Community\Model;

use Community\Model\Concerns\WithCreated;
use Community\Model\Concerns\WithUpdated;
use Community\Model\Role\RolePermission;
use Community\Model\User\UserPermission;
use ORM\Entity;

class Permission extends Entity
{
    use WithCreated, WithUpdated;

    const CATEGORY_ULTIMATE = 'ultimate';
    const CATEGORY_ORDINARY = 'ordinary';

    protected static $primaryKey = ['key'];
    protected static $autoIncrement = false;

    protected static $relations = [
        'roles' => [RolePermission::class, 'permission'],
        'users' => [UserPermission::class, 'permission'],
    ];
}
