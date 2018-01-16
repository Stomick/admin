<?php

namespace app\models;

use app\base\behaviors\multiplier\MultiplierBehavior;
use app\base\behaviors\multiplier\One2Many;
use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%playing_field}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $sportCenterId
 * @property integer $playingFieldId
 * @property string $info
 *
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property AvailableTime[] $availableTimes
 * @property SportCenter $sportCenter
 *
 */
class PlayingField extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%playing_field}}';
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
    public function fields()
    {
        return [
            'id',
            'name',
            'sportCenterId',
            'availableTimes',
            'info',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => 255],
            ['name', 'required'],
            ['info', 'string', 'max' => 65535],
            [['sportCenterId', 'playingFieldId'], 'integer']
        ];
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getSportCenter()
    {
        return $this->hasOne(SportCenter::class, ['id' => 'sportCenterId']);
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'refnoext']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getPlayingField()
    {
        return $this->hasOne(self::class, ['id' => 'playingFieldId']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getAvailableTimes()
    {
        return $this->hasMany(AvailableTime::class, ['playingFieldId' => 'id']);
//            ->orderBy(AvailableTime::tableName() . '.hour');
    }
}
