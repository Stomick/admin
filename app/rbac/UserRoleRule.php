<?php

namespace app\rbac;

use app\models\User;

class UserRoleRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'userRole';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $userIdentity = $this->getUser($user);
        
        if (!$userIdentity instanceof User) {
            return false;
        } elseif (!$userIdentity->role) {
            return false;
        } elseif ($item->name === User::ROLE_USER) {
            return $userIdentity->role === User::ROLE_USER;
        } elseif ($item->name === User::ROLE_ADMIN) {
            return $userIdentity->role === User::ROLE_ADMIN;
        } elseif ($item->name === User::ROLE_SUPER_ADMIN) {
            return $userIdentity->role === User::ROLE_SUPER_ADMIN;
        }

        return false;
    }
}
