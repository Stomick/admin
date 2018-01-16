<?php

namespace app\models;

use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;

/**
 * This is the model class for table "{{%service}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $price
 * @property string $description
 * @property integer $sportCenterId
 *
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 */
class Service extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%service}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => TimestampBehavior::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => 255],
            ['description', 'string', 'max' => 65535],
            [['name', 'price'], 'required'],
            ['price', 'number'],
        ];
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getSportCenter()
    {
        return $this->hasOne(SportCenter::class, ['id' => 'sportCenterId']);
    }
}
