<?php

namespace Test\Unit\Community\Model;

use Community\Model\Permission;
use Community\Model\Role;
use Community\Model\User;
use Mockery as m;
use Test\TestCase;

class PermissionInheritanceTest extends TestCase
{
    /** @var m\MockInterface|User */
    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->ormCreateMockedEntity(User::class);
        $this->user->shouldReceive('getRelated')->with('permissions')
            ->andReturn([])->byDefault();
        $this->user->shouldReceive('getRelated')->with('roles')
            ->andReturn([])->byDefault();
    }

    /** @test */
    public function getsThePermissionsFromUser()
    {
        $userPermission = $this->ormCreateMockedEntity(User\UserPermission::class, [
            'permissionKey' => 'user:edit',
            'userId' => $this->user->id,
        ]);
        $this->user->shouldReceive('getRelated')->with('permissions')
            ->once()->andReturn([$userPermission]);

        self::assertSame([
            'user:edit' => true,
        ], $this->user->getAllPermissions());
    }

    /** @test */
    public function getsThePermissionsFromRoles()
    {
        $role = $this->ormCreateMockedEntity(Role::class, [
            'name' => 'Admin'
        ]);
        $rolePermission = $this->ormCreateMockedEntity(Role\RolePermission::class, [
            'permissionKey' => 'user:edit',
            'roleId' => $role->id,
        ]);
        $role->shouldReceive('getRelated')->with('permissions')
            ->once()->andReturn([$rolePermission]);
        $role->shouldReceive('getRelated')->with('parents')
            ->once()->andReturn([]);
        $this->user->shouldReceive('getRelated')->with('roles')
            ->once()->andReturn([$role]);

        self::assertSame([
            'user:edit' => true,
        ], $this->user->getAllPermissions());
    }

    /** @test */
    public function restrictedPermissionsAreNotGranted()
    {
        $userPermission = $this->ormCreateMockedEntity(User\UserPermission::class, [
            'permissionKey' => 'user:edit',
            'userId' => $this->user->id,
            'restrict' => true,
        ]);
        $this->user->shouldReceive('getRelated')->with('permissions')
            ->once()->andReturn([$userPermission]);

        self::assertSame([
            'user:edit' => false,
        ], $this->user->getAllPermissions());
    }

    /** @test */
    public function overwritesPermissionsFromRoles()
    {
        $this->preparePermissionStructure([
            'user:edit' => true,
            'Admin' => [
                'user:edit' => false,
            ],
        ]);

        self::assertSame([
            'user:edit' => true,
        ], $this->user->getAllPermissions());
    }

    /** @test */
    public function inheritsFromMultipleRoles()
    {
        $this->preparePermissionStructure([
            'Foo' => [
                'foo:do' => true,
            ],
            'Bar' => [
                'bar:do' => true,
            ],
        ]);

        self::assertSame([
            'foo:do' => true,
            'bar:do' => true,
        ], $this->user->getAllPermissions());
    }

    /** @test */
    public function restrictionsFromParentsAreNotPassed()
    {
        $this->preparePermissionStructure([
            'Admin' => [
                'user:edit' => true,
                'user:delete' => false,
            ],
        ]);

        self::assertSame([
            'user:edit' => true,
        ], $this->user->getAllPermissions());
    }

    /** @test */
    public function inheritedRestrictionsHaveHigherPriority()
    {
        $this->preparePermissionStructure([
            'A' => [
                'user:edit' => true,
            ],
            'B' => [
                'user:edit' => false,
            ],
            'C' => [
                'user:edit' => true,
            ],
        ]);

        self::assertEmpty($this->user->getAllPermissions());
    }

    /**
     * @param array $structure
     * @param User|Role|m\MockInterface  $parent
     */
    protected function preparePermissionStructure(array $structure, m\MockInterface $parent = null)
    {
        if (!$parent) {
            $parent = $this->user;
        }

        $roles = [];
        $permissions = [];
        foreach ($structure as $name => $value) {
            if (is_array($value)) {
                $role = $this->ormCreateMockedEntity(Role::class, ['name' => $name]);
                $this->preparePermissionStructure($value, $role);
                $roles[] = $role;
            } elseif ($parent instanceof User) {
                $permission = $this->ormCreateMockedEntity(User\UserPermission::class, [
                    'permissionKey' => $name,
                    'userId' => $parent->id,
                    'restrict' => !$value,
                ]);
                $permissions[] = $permission;
            } else {
                $permission = $this->ormCreateMockedEntity(Role\RolePermission::class, [
                    'permissionKey' => $name,
                    'roleId' => $parent->id,
                    'restrict' => !$value,
                ]);
                $permissions[] = $permission;
            }
        }

        $parent->shouldReceive('getRelated')->with('permissions')
            ->andReturn($permissions);
        $parent->shouldReceive('getRelated')->with($parent instanceof User ? 'roles' : 'parents')
            ->andReturn($roles);
    }
}
