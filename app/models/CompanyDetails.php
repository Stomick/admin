<?php

namespace app\models;

use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%company_details}}".
 *
 * @property integer $id
 * @property string $shortName
 * @property string $fullName
 * @property string $okato
 * @property string $legalAddress
 * @property string $inn
 * @property string $ogrn
 * @property string $okpo
 * @property integer $sportCenterId
 * @property string $okved
 * @property string $bik
 * @property string $bank
 * @property string $kpp
 * @property string $corrAccount
 * @property string $account
 * @property string $generalManager
 * @property string $email
 * @property string $webSite
 * @property string $fax
 * @property string $phone
 * @property float $procent
 * @property string $payu_id
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property SportCenter $sportCenter
 *
 */

class CompanyDetails extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_details}}';
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
            'shortName',
            'fullName',
            'sportCenterId',
            'okato',
            'legalAddress',
            'inn',
            'ogrn',
            'okpo',
            'bank',
            'okved',
            'bik',
            'corrAccount',
            'account',
            'generalManager',
            'email',
            'webSite',
            'kpp',
            'fax',
            'phone',
            'payu_id',
            'procent',
            'confirmationStatus' => function () {
                return $this->sportCenter->confirmationStatus;
            },
            'sportCenterName' => function () {
                return $this->sportCenter->name;
            },
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
                [
                    'shortName',
                    'fullName',
                    'okato',
                    'legalAddress',
                    'inn',
                    'ogrn',
                    'okpo',
                    'okved',
                    'bik',
                    'bank',
                    'corrAccount',
                    'account',
                    'generalManager',
                    'email',
                    'webSite',
                    'fax',
                    'kpp',
                    'phone',
                    'payu_id',
                ],
                'string',
                'max' => 255
            ],
            ['sportCenterId', 'integer'],
            ['procent' , 'double']
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$insert) {
                $sportCenter = $this->sportCenter;
                $sportCenter->confirmationStatus = SportCenter::CONFIRMATION_STATUS_WAITING;
                $sportCenter->save(false);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getSportCenter()
    {
        return $this->hasOne(SportCenter::class, ['id' => 'sportCenterId']);
    }
}
