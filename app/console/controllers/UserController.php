<?php

namespace app\console\controllers;

use Yii;
use yii\helpers\Console;

use app\console\base\Controller;
use app\console\models\User;

/**
 * UserController may be used for managing users from console application.
 */
class UserController extends Controller
{
    /**
     * Creates a new user.
     * @param string $name The name of user.
     * @param string $email The email of user.
     * @param string $phone The phone of user.
     * @param string $password User's password. If it will not be passed than random password will be generated.
     */
    public function actionCreate($name, $email, $phone, $password = null)
    {
        if (empty($password)) {
            $password = $this->generatePassword();
        }

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->phone = $phone;
        $user->newPassword = $password;

        if (!$user->save()) {
            $this->outputErrors($user);
            return;
        }

        $this->stdout("User has been successfully created.\n", Console::FG_GREEN);
        $this->outputUser($user);
        $this->stdout($user->apiToken);
    }

    /**
     * @param string $email
     */
    public function actionToken($email)
    {
        $user = User::findOne(['[[email]]' => $email]);
        $user->role = User::ROLE_SUPER_ADMIN;
        $user->save(false);
        $this->outputUser($user);
        $this->stdout($user->role);
        $this->stdout($user->apiToken);
    }

    /**
     * Changes user's password.
     * @param string $email user's email.
     * @param string $newPassword The new password. If it will not be not passed than random password will be generated.
     */
    public function actionChangePassword($email, $newPassword = null)
    {
        $user = User::find()->byEmail($email)->one();
        if (!$user instanceof User) {
            $this->stdout("User with email '$email' was not found!\n", Console::FG_RED);
            return;
        }

        if (empty($newPassword)) {
            $newPassword = $this->generatePassword();
        }
        $user->newPassword = $newPassword;
        if (!$user->save()) {
            $this->outputErrors($user);
            return;
        }

        $this->stdout("User's password has been successfully changed.\n", Console::FG_GREEN);
        $this->outputUser($user);
    }

    /**
     * Generates new password.
     * @return string
     */
    protected function generatePassword()
    {
        $str = Yii::$app->getSecurity()->generateRandomString(5);
        $number = (int) mt_rand(0, 9);
        return str_shuffle("$str$number");
    }

    /**
     * @param User $user
     */
    protected function outputUser($user)
    {
        $this->stdout($user->getAttributeLabel('name') . ': ', Console::FG_YELLOW);
        $this->stdout("$user->name\n");
        $this->stdout($user->getAttributeLabel('email') . ': ', Console::FG_YELLOW);
        $this->stdout("$user->email\n");
        $this->stdout($user->getAttributeLabel('newPassword') . ': ', Console::FG_YELLOW);
        $this->stdout("$user->newPassword\n");
    }

    /**
     * @param User $user
     */
    protected function outputErrors($user)
    {
        $this->stdout("Error was occured!\n", Console::FG_RED);
        foreach ($user->getErrors() as $field => $errors) {
            $this->stdout($user->getAttributeLabel($field) . ":\n");
            foreach ($errors as $error) {
                $this->stdout("$error\n", Console::FG_RED);
            }
        }
    }
}
