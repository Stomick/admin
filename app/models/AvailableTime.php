<?php

namespace app\models;

use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%available_time}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $price
 * @property integer $hour
 * @property boolean $working
 * @property integer $playingFieldId
 * @property integer $availableTimeId
 * @property integer $sportCenterId
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property PlayingField $playingField
 *@property Booking[] $bookings
 * 
 */
class AvailableTime extends ActiveRecord
{
    const TYPE_WORKING_DAYS = 'work';
    const TYPE_WEEKEND = 'weekend';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%available_time}}';
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
            'playingFieldId',
            'hour',
            'start_hour',
            'end_hour',
            'price',
            'working',
            'type',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hour', 'availableTimeId', 'playingFieldId'], 'integer'],
            [['start_hour', 'end_hour'], 'string'],
            ['hour', 'in', 'range' => range(0, 23)],
            ['type', 'in', 'range' => [self::TYPE_WORKING_DAYS, self::TYPE_WEEKEND]],
            ['price', 'number'],
            ['working', 'boolean'],
            [['type', 'hour'], 'required']
        ];
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getPlayingField()
    {
        return $this->hasOne(PlayingField::class, ['id' => 'playingFieldId']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['availableTimeId' => 'id']);
    }
}
