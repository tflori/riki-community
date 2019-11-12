<?php

namespace Community\Model;

use Community\Model\Concerns\WithCreated;
use Community\Model\Concerns\WithUpdated;
use Community\Model\Role\RolePermission;
use ORM\Entity;

class Role extends Entity
{
    use WithCreated, WithUpdated;

    protected static $relations = [
        'permissions' => [RolePermission::class, 'role'],
        'children' => [Role::class, ['id' => 'parent_id'], 'parents', 'role_inherits'],
        'parents' => [Role::class, ['id' => 'child_id'], 'children', 'role_inherits'],
        'users' => [User::class, ['id' => 'role_id'], 'roles', 'user_roles'],
    ];
}
