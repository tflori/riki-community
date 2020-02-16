<?php

namespace Community\Model;

use Community\Model\Concerns\InheritsPermissionsFromRoles;
use Community\Model\Concerns\WithCreated;
use Community\Model\Concerns\WithUpdated;
use Community\Model\Role\RolePermission;
use ORM\Entity;

/**
 * @property-read RolePermission[] $permissions
 * @property-read Role[] $parents
 * @property-read Role[] $children
 * @property-read User[] $users
 */
class Role extends Entity
{
    use WithCreated, WithUpdated, InheritsPermissionsFromRoles;

    protected static $relations = [
        'permissions' => [RolePermission::class, 'role'],
        'children' => [Role::class, ['id' => 'parent_id'], 'parents', 'role_inherits'],
        'parents' => [Role::class, ['id' => 'child_id'], 'children', 'role_inherits'],
        'users' => [User::class, ['id' => 'role_id'], 'roles', 'user_roles'],
    ];

    protected function getInheritedRoles(): array
    {
        return $this->parents;
    }

    protected function getGrants(): array
    {
        return $this->permissions;
    }
}
