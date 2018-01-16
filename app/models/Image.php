<?php

namespace app\models;

use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;
use app\models\files\SportCentersImage;
use Yii;
use app\base\validators\FileDataValidator;
use flexibuild\file\ModelBehavior;
use app\base\helpers\SerializeHelper;

/**
 * This is the model class for table "{{%image}}".
 *
 * @property integer $id
 * @property string $value
 * @property integer $sportCenterId
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property SportCentersImage $valueFile
 *
 */
class Image extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%image}}';
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
                    'value' => 'sport-center-image',
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'value' => function () {
                return $this->value;
            },
            'src' => function () {
                if ($this->valueFile->isEmpty) {
                    return null;
                } else {
                    return SerializeHelper::fixFileUrl($this->valueFile->getUrl(null, true));
                }
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', FileDataValidator::class, 'context' => 'sport-center-image'],
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
