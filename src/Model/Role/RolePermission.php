<?php

namespace Community\Model\Role;

use Community\Model\Permission;
use Community\Model\Role;
use ORM\Entity;

/**
 * @property string $permissionKey
 * @property bool $restrict
 * @property-read Role $role
 * @property-read Permission $permission
 */
class RolePermission extends Entity
{
    protected static $primaryKey = ['role_id', 'permission_key'];
    protected static $autoIncrement = false;

    protected static $relations = [
        'permission' => [Permission::class, ['permission_key' => 'key']],
        'role' => [Role::class, ['role_id' => 'id']],
    ];
}
