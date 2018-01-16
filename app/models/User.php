<?php

namespace app\models;

use app\base\behaviors\LowerizeBehavior;
use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use app\models\traits\UserAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\InvalidValueException;
use Zelenin\SmsRu\Api;
use Zelenin\SmsRu\Entity\Sms;
use Zelenin\SmsRu\Auth\LoginPasswordSecureAuth;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string $phone
 * @property string $description
 * @property string $authKey
 * @property string $passwordHash
 * @property string $apiToken
 * @property string $code
 * @property string $smsId
 * @property string $facebook
 * @property string $google
 * @property string $linkedin
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property Booking[] $bookings
 * @property SportCenter[] $sportCenters
 */
class User extends ActiveRecord implements IdentityInterface
{
    use UserAuth;

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_SUPER_ADMIN = 'super-admin';

    const SCENARIO_CREATE_USER = 'create-user';
    const SCENARIO_CREATE_ADMIN = 'create-admin';
    const SCENARIO_UPDATE = 'update';

    /**
     * @var bool
     */
    public $sendUserPasswordEmail = false;

    /**
     * @var string
     */
    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => TimestampBehavior::class,
            'lowerize' => [
                'class' => LowerizeBehavior::class,
                'attributes' => [
                    'email',
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert) && ($this->scenario == self::SCENARIO_CREATE_ADMIN)) {
            $this->role = self::ROLE_ADMIN;
            $password = Yii::$app->security->generateRandomString(8);
            $this->changePassword($password);
            $this->sendEmailToAdmin($this, $password);
            $this->sendSmsToAdmin($this);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'email',
            'role',
            'token' => 'apiToken',
            'phone',
            'description',
            'code',
            'sportCenterId' => function(){
                if (isset($this->sportCenters[0])) {
                    return $this->sportCenters[0]->id;
                } else {
                    return null;
                }
            },
            'bookings',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return [
            'id',
            'email',
            'name',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'role' => Yii::t('app', 'Role'),
            'phone' => Yii::t('app', 'Phone'),
            'description' => Yii::t('app', 'Description'),
            'authKey' => Yii::t('app', 'Auth Key'),
            'passwordHash' => Yii::t('app', 'Password Hash'),
            'apiToken' => Yii::t('app', 'Api Token'),
            'code' => Yii::t('app', 'Code'),
            'facebook' => Yii::t('app', 'Facebook'),
            'google' => Yii::t('app', 'GooglePlus'),
            'linkedin' => Yii::t('app', 'LinkedIn'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'email'],
            [['email', 'phone'], 'unique'],
            [['name', 'phone'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 65535],
            ['phone', 'required', 'on' => self::SCENARIO_CREATE_USER],
            [['code', 'smsId'], 'string', 'max' => 255, 'on' => self::SCENARIO_CREATE_USER],
            [['email', 'name'], 'required', 'on' => self::SCENARIO_CREATE_ADMIN],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE_ADMIN => [
                'email',
                'name',
                'phone',
            ],
            self::SCENARIO_CREATE_USER => [
                'name',
                'phone',
                'smsId',
                'code',
            ],
            self::SCENARIO_UPDATE => [
                'email',
                'name',
                'phone',
                'code',
                'description',
            ],
        ]);
    }

    /**
     * Regenerates API token.
     */
    public function regenerateApiToken()
    {
        do {
            $token = Yii::$app->getSecurity()->generateRandomString(64);
        } while (static::find()->byApiToken($token)->exists());

        $this->apiToken = $token;
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Send email to user with password
     * @param User $user
     * @param string $password
     * @throws InvalidValueException
     */
    public function sendEmailToAdmin($user, $password)
    {
        $send = Yii::$app
            ->mailer
            ->compose(
                [
                    'html' => 'create-admin/html.sphp',
                    'text' => 'create-admin/text.php',
                ],
                [
                    'model' => $user,
                    'password' => $password,
                ])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($user->email)
            ->setSubject('Создание администратора на LetMeSport')
            ->send();
        if (!$send) {
            throw new InvalidValueException("Can't send email");
        }
    }

    /**
     * Send email to user with password
     * @param User $user
     * @throws InvalidValueException
     */
    public function sendSmsToAdmin($user)
    {
        $client = new Api(new LoginPasswordSecureAuth(
            Yii::$app->params['sms']['login'],
            Yii::$app->params['sms']['password'],
            Yii::$app->params['sms']['api_id']
        ));
        $message = 'Для вас создан аккаунт на портале Weev.ru. Подробности в почтовом ящике ' . $user->email;
        $sms = new Sms($user->phone, $message);
        $sms->from = 'WEEV';

        $client->smsSend($sms);
    }

    /**
     * Validates password.
     * @param string $password
     * @return boolean if provided password is valid for current user
     */
    public function validatePassword($password)
    {
        $passwordHash = $this->passwordHash;
        if (empty($passwordHash) || empty($password)) {
            return false;
        }

        return Yii::$app->getSecurity()->validatePassword($password, $passwordHash);
    }

    /**
     * Generates password hash from password and sets it to the model.
     * @param string $password
     */
    public function changePassword($password)
    {
        $this->passwordHash = Yii::$app->getSecurity()->generatePasswordHash($password);
        $this->regenerateAuthKey();
        $this->regenerateApiToken();
    }

    /**
     * Regenerates authentication key
     */
    public function regenerateAuthKey()
    {
        $this->authKey = Yii::$app->getSecurity()->generateRandomString(32);
    }

    /**
     * @return \app\base\db\ActiveQuery
     */
    public function getBookings()
    {
        return $this->hasMany(Booking::className(), ['userId' => 'id']);
    }

    public function getSportCenters()
    {
        return $this->hasMany(SportCenter::className(), ['id' => 'sportCenterId'])
            ->viaTable('sport_center_user', ['userId' => 'id']);
    }

    public static function isUserAdmin($id)
    {
      if (static::findOne(['id' => $id, 'role' => self::ROLE_ADMIN])){
             return true;
      } else {
             return false;
      }

    }
}
