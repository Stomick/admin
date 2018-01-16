<?php

namespace app\models\forms;

use app\models\User;
use Yii;
use yii\base\InvalidValueException;
use yii\base\Model;

/**
 * Class LoginFormUser
 * @package app\models\forms
 *
 * @property string $phone
 * @property string $name
 */
class LoginFormUser extends Model
{
    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $name;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['phone', 'required'],
            ['phone', 'string'],

            ['name', 'string', 'max' => 256],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function registration()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->regenerateApiToken();

            if ($user->save(User::SCENARIO_CREATE_USER)) {
                return Yii::$app->user->login($user) ? $user->code : false ;
            }

            throw new InvalidValueException("Can not save new token");
        }

        return false;
    }

    /**
     * Finds user by [[phone]]
     *
     * @return User
     */
    public function getUser()
    {
        $user = User::find()->byPhone($this->phone)->one();

        if ($user == null) {
            $user = new User();
            $user->phone = $this->phone;
            $user->name = $this->name;
            $user->role = User::ROLE_USER;

        }

        $user->regenerateApiToken();
        $user->code = $this->generateRandomString();

        return $user;
    }

    public function generateRandomString($length = 4)
    {
        return substr(str_shuffle(str_repeat('0123456789', ceil($length/strlen('0123456789')) )), 1, $length);
    }
}
