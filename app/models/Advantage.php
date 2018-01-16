<?php

namespace app\models;

use app\base\behaviors\multiplier\Many2Many;
use app\base\behaviors\multiplier\MultiplierBehavior;
use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use app\models\files\AdvantageIcon;
use app\models\files\AdvantageIconFile;
use Yii;
use yii\helpers\ArrayHelper;
use flexibuild\file\ModelBehavior;
use app\base\helpers\SerializeHelper;

/**
 * This is the model class for table "{{%advantage}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $icon
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property AdvantageIconFile $iconFile
 */
class Advantage extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%advantage}}';
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
                    'icon' => 'advantage-icon',
                ],
            ],
            'multiplier' => [
                'class' => MultiplierBehavior::class,
                'links' => [
                    'sportCenterIds' => [
                        'class' => Many2Many::class,
                        'linkClass' => SportCenterAdvantage::class,
                        'linkOwnerAttribute' => 'advantageId',
                        'linkTargetAttribute' => 'sportCenterId',
                        'linkPopulateRelations' => 'advantage',
                        'targetClass' => SportCenter::class,
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
            'icon' => function () {
                return $this->icon;
            },
            'iconSrc' => function () {
                if ($this->iconFile->isEmpty) {
                    return null;
                } else {
                    return SerializeHelper::fixFileUrl($this->iconFile->getUrl(null, true));
                }
            },
            'iconName' => function () {
                if ($this->iconFile->isEmpty) {
                    return null;
                } else {
                    return $this->iconFile->baseName;
                }
            },
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'icon'], 'string', 'max' => 255],
            ['name', 'required'],
            ['name', 'unique'],
            ['sportCenterIds', 'validateLink'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $editableAttributes = [
            'name',
            'icon',
        ];

        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => $editableAttributes,
            self::SCENARIO_UPDATE => $editableAttributes,
        ]);
    }
}
