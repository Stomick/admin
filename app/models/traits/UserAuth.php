<?php

namespace app\models\traits;

use Yii;

/**
 * UserAuthTrait adds auth methods to user model.
 */
trait UserAuth
{
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        if (!is_scalar($id)) {
            return null;
        }

        $table = static::tableName();
        $query = static::find();
        /* @var $query \app\models\UserQuery */

        return $query
            ->andWhere(["$table.[[id]]" => $id])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $query = static::find();
        /* @var $query \app\models\UserQuery */
        return $query
            ->byApiToken($token)
            ->one() ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey(false);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        $currentAuthKey = $this->getAuthKey();
        if (empty($currentAuthKey) || empty($authKey)) {
            return false;
        }
        return Yii::$app->getSecurity()->compareString($currentAuthKey, $authKey);
    }
}