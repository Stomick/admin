<?php

namespace app\base\db;

/**
 * Base project ActiveQuery class
 * 
 * @method \base\db\ActiveRecord|array|null one(\yii\db\Connection $db = null)
 * @method \base\db\ActiveRecord[]|array[] all(\yii\db\Connection $db = null)
 * 
 * @property-read string $tableName
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return call_user_func([$this->modelClass, 'tableName']);
    }
}
