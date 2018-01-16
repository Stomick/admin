<?php

namespace app\models;

use app\base\helpers\StringHelper;
use app\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[User]].
 * 
 * @method \app\models\User|array|null one(\yii\db\Connection $db = null)
 * @method \app\models\User[]|array[] all(\yii\db\Connection $db = null)
 *
 * @see User
 */
class UserQuery extends ActiveQuery
{
    /**
     * Scope to query user by email value.
     * @param string $phone
     * @return static $this object
     */
    public function byPhone($phone)
    {
        $this->andWhere([
            "$this->tableName.[[phone]]" => $phone,
        ]);
        return $this;
    }

    /**
     * Scope to query user by email value.
     * @param string $email
     * @return static $this object
     */
    public function byEmail($email)
    {
        $this->andWhere([
            "$this->tableName.[[email]]" => StringHelper::strtolower($email),
        ]);
        return $this;
    }

    /**
     * Scope to query user by API token value.
     * @param string $apiToken
     * @return static $this object
     */
    public function byApiToken($apiToken)
    {
        $this->andWhere([
            "$this->tableName.[[apiToken]]" => $apiToken,
        ]);
        return $this;
    }

    /**
     * Scope to query user with confirmed emails only.
     * @return static $this query object.
     */
    public function isEmailConfirmed()
    {
        $this->andWhere([
            "$this->tableName.[[isEmailConfirmed]]" => true,
        ]);
        return $this;
    }

    /**
     * @param string $hash
     * @return static $this query object.
     */
    public function byEmailHash($hash)
    {
        $this->andWhere([
            "$this->tableName.[[emailHash]]" => $hash,
        ]);
        return $this;
    }

    /**
     * @param string $token
     * @return static $this query object.
     */
    public function byResetPasswordToken($token)
    {
        $this->andWhere([
            "$this->tableName.[[resetPasswordToken]]" => $token,
        ]);
        return $this;
    }
}
