<?php

namespace app\models\forms;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\base\InvalidValueException;

/**
 * Class LoginFormAdmin
 * @package app\models\forms
 *
 * @property string $password
 * @property string $email
 */
class LoginFormAdmin extends Model
{
    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $email;

    /**
     * @var User|null
     */
    private $_user = false;
    
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password', 'email'], 'required'],
            ['password', 'validatePassword'],
            ['email', 'email'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
            }
        }

        return false;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $this->getUser()->regenerateApiToken();
            if ($this->getUser()->save(false)) {
                return Yii::$app->user->login($this->getUser());
            }
            throw new InvalidValueException("Can not save new token");

        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::find()->byEmail($this->email)->one();
        }
        return $this->_user;
    }
}