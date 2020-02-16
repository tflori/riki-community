<?php

namespace Community\Model\User;

use Community\Model\Permission;
use Community\Model\User;
use ORM\Entity;

/**
 * @property bool $restrict
 * @property string $permissionKey
 * @property-read User $user
 * @property-read Permission $permission
 */
class UserPermission extends Entity
{
    protected static $primaryKey = ['user_id', 'permission_key'];
    protected static $autoIncrement = false;

    protected static $relations = [
        'permission' => [Permission::class, ['permission_key' => 'key']],
        'user' => [User::class, ['user_id' => 'id']],
    ];
}
