<?php

namespace app\models;

use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%sport_center_user}}".
 *
 * @property integer $id
 * @property integer $sportCenterId
 * @property string $userId
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property SportCenter $sportCenter
 * @property Advantage $user
 */
class SportCenterUser extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sport_center_user}}';
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
            [['sportCenterId', 'userId'], 'required'],
            [
                'sportCenterId',
                'exist',
                'skipOnError' => true,
                'targetClass' => SportCenter::className(),
                'targetAttribute' => ['sportCenterId' => 'id']
            ],
            [
                'userId',
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['userId' => 'id']
            ],
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
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

}
