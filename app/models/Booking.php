<?php

namespace app\models;

use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%booking}}".
 *
 * @property integer $id
 * @property integer $playingFieldId
 * @property integer $availableTimeId
 * @property integer $avaTimeId
 * @property array $serviceIds
 * @property integer $userId
 * @property integer $price
 * @property integer $year
 * @property integer $month
 * @property integer $day
 * @property integer $hour
 * @property string $start_hour
 * @property string $end_hour
 * @property string $sportCenterName
 * @property string $playingFieldName
 * @property string $sportCenterAddress
 * @property string $type
 * @property string $phone
 * @property string $comment
 * @property boolean $submit
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property User $user
 * @property BookingService[] $services
 * @property AvailableTime $availableTime
 * @property PlayingField $playingField
 */
class Booking extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $availableTimeId;
    public $serviceIds;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%booking}}';
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
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->userId = Yii::$app->user->id;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'services',
            'price',
            'userId',
            'year',
            'month',
            'day',
            'hour',
            'start_hour',
            'end_hour',
            'sportCenterName',
            'playingFieldName',
            'playingFieldId',
            'availableTimeId',
            'avaTimeId',
            'sportCenterAddress',
            'phone',
            'comment',
            'type',
            'submit',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['year', 'month', 'day', 'hour', 'price', 'playingFieldId', 'availableTimeId' , 'avaTimeId'], 'integer'
            ],
            [
                ['start_hour' , 'end_hour'], 'string'
            ],
            [
                ['year', 'month', 'day', 'hour', 'sportCenterName', 'playingFieldName', 'type' ], 'required'
            ],
            ['submit', 'boolean'],
            ['serviceIds', 'safe'],
            [
                ['sportCenterName', 'playingFieldName', 'type', 'phone'], 'string', 'max' => 255
            ],
            [
                ['comment', 'sportCenterAddress'], 'string', 'max' => 65535
            ],
            ['type', 'in', 'range' => [AvailableTime::TYPE_WORKING_DAYS, AvailableTime::TYPE_WEEKEND]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $editableAttributes = [
            'year',
            'month',
            'hour',
            'start_hour',
            'end_hour',
            'serviceIds',
            'sportCenterName',
            'playingFieldName',
            'sportCenterAddress',
            'playingFieldId',
            'availableTimeId',
            'avaTimeId',
            'type',
            'day',
            'price',
            'submit',
        ];

        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => $editableAttributes,
            self::SCENARIO_UPDATE => $editableAttributes,
        ]);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(BookingService::class, ['bookingId' => 'id']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getNameBooking($id)
    {
        /** @var Booking $booking */
        $booking = self::findOne($id);

        return 'Booking ' . $booking->sportCenterName . ' ' . $booking->playingFieldName . ' ' . $booking->hour;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getPriceBooking($id)
    {
        /** @var Booking $booking */
        $booking = self::findOne($id);

        return $booking->price;
    }

    /**
     * @param $id
     * @return User
     */
    public static function getUserBooking($id)
    {
        /** @var Booking $booking */
        $booking = self::findOne($id);

        return $booking->user;
    }
    public function getAvailableTimeId(){
        return $this->hasOne(availableTimeId::class, ['id' => $this->availableTimeId]);
    }
    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getPlayingField()
    {
        return $this->hasOne(PlayingField::class, ['id' => 'playingFieldId']);
    }
}
