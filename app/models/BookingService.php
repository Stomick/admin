<?php

namespace app\models;

use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%booking_service}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $price
 * @property integer $bookingId
 *
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 */
class BookingService extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%booking_service}}';
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
            [['name', 'price'], 'required'],
            ['price', 'number'],
        ];
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'bookingId']);
    }
}
