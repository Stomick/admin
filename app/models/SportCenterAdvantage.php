<?php

namespace app\models;

use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%sport_center_advantage}}".
 *
 * @property integer $id
 * @property integer $sportCenterId
 * @property integer $advantageId
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property SportCenter $sportCenter
 * @property Advantage $advantage
 */
class SportCenterAdvantage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sport_center_advantage}}';
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
            [['sportCenterId', 'advantageId'], 'integer'],
        ];
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getSportCenter()
    {
        return $this->hasOne(SportCenter::class, ['id' => 'sportCenterId']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getAdvantage()
    {
        return $this->hasOne(Advantage::class, ['id' => 'advantageId']);
    }

}
