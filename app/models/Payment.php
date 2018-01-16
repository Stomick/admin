<?php

namespace app\models;

use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;

/**
 * This is the model class for table "{{%payment}}".
 *
 * @property integer $id
 * @property string $orderstatus
 * @property string $refno
 * @property string $saledate
 * @property string $paymethod
 * @property string $country
 * @property string $phone
 * @property string $customeremail
 * @property string $currency
 * @property string $hash
 * @property integer $refnoext
 *
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 */
class Payment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payment}}';
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
            [
                [
                    'orderstatus',
                    'refno',
                    'saledate',
                    'paymethod',
                    'country',
                    'phone',
                    'customeremail',
                    'currency',
                    'hash'
                ],
                'string',
                'max' => 255
            ],
            ['refnoext', 'integer']
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
