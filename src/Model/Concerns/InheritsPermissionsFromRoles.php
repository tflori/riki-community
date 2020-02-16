<?php

namespace Community\Model\Concerns;

trait InheritsPermissionsFromRoles
{
    /**
     * Get all permissions including inherited from roles
     *
     * Warning: the roles don't have an order and restriction has higher priority
     *
     * @return array
     */
    public function getAllPermissions(): array
    {
        $permissions = [];
        foreach ($this->getInheritedRoles() as $role) {
            foreach ($role->getAllPermissions() as $permissionKey => $granted) {
                $permissions[$permissionKey] =
                    isset($permissions[$permissionKey]) && $permissions[$permissionKey] === false ?
                        false : $granted;
            }
        }

        $permissions = array_filter($permissions);

        foreach ($this->getGrants() as $grant) {
            $permissions[$grant->permissionKey] = !$grant->restrict;
        }

        return $permissions;
    }

    /**
     * Get the roles that are inherited
     *
     * @return array
     */
    abstract protected function getInheritedRoles(): array;

    /**
     * Get the granted permissions
     *
     * @return array
     */
    abstract protected function getGrants(): array;
}
