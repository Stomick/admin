<?php

namespace app\models;

use app\base\behaviors\multiplier\Many2Many;
use app\base\behaviors\multiplier\MultiplierBehavior;
use app\base\behaviors\multiplier\One2Many;
use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use app\models\files\SportCenterLogoFile;
use Yii;
use flexibuild\file\ModelBehavior;
use app\base\validators\FileDataValidator;
use yii\helpers\ArrayHelper;
use app\base\helpers\SerializeHelper;

/**
 * This is the model class for table "{{%sport_center}}".
 *
 * @property integer $id
 * @property integer $name
 * @property integer $sportCenterId
 * @property string $phone
 * @property string $logo
 * @property string $latitude
 * @property string $longitude
 * @property string $image
 * @property string $image2
 * @property string $image3
 * @property string $image4
 * @property string $address
 * @property string $description
 * @property string $approvementStatus
 * @property string $confirmationStatus
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property SportCenterLogoFile $logoFile
 * @property Service[] $services
 * @property Image[] $images
 * @property PlayingField[] $playingFields
 * @property User[] $users
 * @property Advantage[] $advantages
 * @property SportCenterAdvantage[] $sportCenterAdvantages
 * @property SportCenterUser[] $sportCenterUsers
 * @property CompanyDetails $details
 * @property SportCenter $sportCenter
 */
class SportCenter extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPDATE_SUPER_ADMIN = 'update-super-admin';

    const APPROVEMENT_STATUS_ACTIVE = 'active';
    const APPROVEMENT_STATUS_NOT_ACTIVE = 'not active';
    const APPROVEMENT_STATUS_MODER = 'moder';

    const CONFIRMATION_STATUS_EMPTY = 'empty';
    const CONFIRMATION_STATUS_WAITING = 'waiting';
    const CONFIRMATION_STATUS_TRUE = 'true';
    const CONFIRMATION_STATUS_FALSE = 'false';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sport_center}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => TimestampBehavior::class,
            'file' => [
                'class' => ModelBehavior::class,
                'attributes' => [
                    'logo' => 'sport-center-logo',
                ],
            ],
            'multiplier' => [
                'class' => MultiplierBehavior::class,
                'links' => [
                    'serviceModels' => [
                        'class' => One2Many::class,
                        'targetClass' => Service::class,
                        'targetAttribute' => 'sportCenterId',
                        'targetPopulateRelations' => 'sportCenter',
                    ],
                    'images' => [
                        'class' => One2Many::class,
                        'targetClass' => Image::class,
                        'targetAttribute' => 'sportCenterId',
                        'targetPopulateRelations' => 'sportCenter',
                    ],
                    'advantageIds' => [
                        'class' => Many2Many::class,
                        'linkClass' => SportCenterAdvantage::class,
                        'linkOwnerAttribute' => 'sportCenterId',
                        'linkTargetAttribute' => 'advantageId',
                        'linkPopulateRelations' => 'sportCenter',
                        'targetClass' => Advantage::class,
                    ],
                    'admins' => [
                        'class' => Many2Many::class,
                        'linkClass' => SportCenterUser::class,
                        'linkOwnerAttribute' => 'sportCenterId',
                        'linkTargetAttribute' => 'userId',
                        'linkPopulateRelations' => 'sportCenter',
                        'targetClass' => User::class,
                    ],
                ]
            ]
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
            'phone',
            'logo' => function () {
                return $this->logo;
            },
            'logoSrc' => function () {
                if ($this->logoFile->isEmpty) {
                    return null;
                } else {
                    return SerializeHelper::fixFileUrl($this->logoFile->getUrl(null, true));
                }
            },
            'images',
            'latitude',
            'longitude',
            'approvementStatus',
            'confirmationStatus',
            'address',
            'description',
            'services',
            'playingFields',
            'advantageIds',
            'companyDetails' => function() {
                return $this->details;
            },
            'minPrice' => function () {
                $minPrice = null;
                $min = null;

                if (Yii::$app->request->isGet && Yii::$app->request->get('hour')) {
                    $hour = Yii::$app->request->get('hour');
                    $type = Yii::$app->request->get('type');

                    foreach ($this->playingFields as $playingField) {
                        foreach ($playingField->availableTimes as $availableTime) {
                            if ($availableTime->hour == $hour && $availableTime->type == $type) {
                                $min = $availableTime->price;
                            }
                        }

                        if (is_null($minPrice) || $min < $minPrice) {
                            $minPrice = $min;
                        }
                    }
                } else {
                    foreach ($this->playingFields as $playingField) {
                        $min = empty(ArrayHelper::getColumn($playingField->availableTimes, 'price'))
                            ? null
                            : min(ArrayHelper::getColumn($playingField->availableTimes, 'price'));

                        if (is_null($minPrice) || ($min < $minPrice && !is_null($min))) {
                            $minPrice = $min;
                        }
                    }

                }

                return $minPrice;
            },
            'admins' => function () {
                return $this->users;
            },
            'sportCenterId',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $editableAttributes = [
            'name',
            'admins',
        ];

        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => $editableAttributes,
            self::SCENARIO_UPDATE_SUPER_ADMIN => [
                'approvementStatus',
                'admins',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['logo', FileDataValidator::class, 'context' => 'sport-center-logo'],
            ['name', 'required'],
            ['sportCenterId', 'integer'],
            [['name', 'latitude', 'longitude', 'name', 'phone', 'address'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 65535],
            ['approvementStatus', 'in', 'range' => self::getAllApprovementStatus()],
            [['serviceModels', 'advantageIds', 'admins', 'images'], 'validateLink'],
            ['confirmationStatus', 'in', 'range' => self::getAllConfirmationStatus()],
            [
                'sportCenterId',
                'exist',
                'skipOnError' => true,
                'targetClass' => self::className(),
                'targetAttribute' => ['sportCenterId' => 'id']
            ],
        ];
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert && $this->approvementStatus !== self::APPROVEMENT_STATUS_MODER) {
            for ($i = 0; $i < 4; $i++) {
                $image = new Image();
                $image->value = null;
                $image->sportCenterId = $this->id;
                $image->save(false);
            }

            $companyDetails = new CompanyDetails();
            $companyDetails->sportCenterId = $this->id;
            $companyDetails->save();
        }

        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }


    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getDetails()
    {
        return $this->hasOne(CompanyDetails::class, ['sportCenterId' => 'id']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::class, ['sportCenterId' => 'id']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getPlayingFields()
    {
        return $this->hasMany(PlayingField::class, ['sportCenterId' => 'id']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Image::class, ['sportCenterId' => 'id']);
    }

    /**
     * @return $this
     */
    public function getAdvantages()
    {
        return $this->hasMany(Advantage::className(), ['id' => 'advantageId'])
            ->viaTable('sport_center_advantage', ['sportCenterId' => 'id']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getSportCenterAdvantages()
    {
        return $this->hasMany(SportCenterAdvantage::class, ['sportCenterId' => 'id']);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getSportCenterUsers()
    {
        return $this->hasMany(SportCenterUser::class, ['sportCenterId' => 'id']);
    }

    /**
     * @return array
     */
    public static function getAllApprovementStatus()
    {
        return [
            self::APPROVEMENT_STATUS_ACTIVE,
            self::APPROVEMENT_STATUS_MODER,
            self::APPROVEMENT_STATUS_NOT_ACTIVE
        ];
    }

    /**
     * @return $this
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])
            ->viaTable('sport_center_user', ['sportCenterId' => 'id']);
    }

    /**
     * @return array
     */
    public static function getAllConfirmationStatus()
    {
        return [
            self::CONFIRMATION_STATUS_EMPTY,
            self::CONFIRMATION_STATUS_WAITING,
            self::CONFIRMATION_STATUS_TRUE,
            self::CONFIRMATION_STATUS_FALSE
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
