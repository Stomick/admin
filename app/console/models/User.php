<?php

namespace app\console\models;

use app\base\helpers\StringHelper;

/**
 * Console User Model.
 */
class User extends \app\models\User
{
    /**
     * @var string
     */
    public $newPassword;

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->newPassword !== null) {
            $this->changePassword($this->newPassword);
            $this->role = self::ROLE_SUPER_ADMIN;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'max' => 255],

            ['email', 'required'],
            ['email', 'filter', 'filter' => [StringHelper::class, 'strtolower']],
            ['email', 'email'],
            ['email', 'unique'],

            ['phone', 'string', 'max' => 255],

            ['newPassword', 'required'],
        ];
    }
}
