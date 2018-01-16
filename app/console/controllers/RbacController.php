<?php

namespace app\console\controllers;

use app\console\base\Controller;
use app\rbac\UserRoleRule;
use app\models\User;
use Yii;

/**
 * Allows you to generate RBAC files for PhpAuthManager.
 */
class RbacController extends Controller
{
    public function actionGenerate()
    {
        $authManager = Yii::$app->authManager;
        $authManager->removeAll();
        $this->stdout("Removed old items.\n");

        $userRoleRule = new UserRoleRule;
        $authManager->add($userRoleRule);
        $this->stdout('Added ' . UserRoleRule::className() . " rule.\n");

        $userRole = $authManager->createRole(User::ROLE_USER);
        $userRole->ruleName = $userRoleRule->name;
        $authManager->add($userRole);
        $this->stdout("Added " . User::ROLE_USER ." role.\n");

        $adminRole = $authManager->createRole(User::ROLE_ADMIN);
        $adminRole->ruleName = $userRoleRule->name;
        $authManager->add($adminRole);
        $this->stdout("Added " . User::ROLE_ADMIN ." role.\n");

        $superAdminRole = $authManager->createRole(User::ROLE_SUPER_ADMIN);
        $superAdminRole->ruleName = $userRoleRule->name;
        $authManager->add($superAdminRole);
        $this->stdout("Added " . User::ROLE_SUPER_ADMIN ." role.\n");

        $this->stdout("Done.\n");
    }
}
