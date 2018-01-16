<?php

namespace app\models;
use app\base\behaviors\TimestampBehavior;
use app\base\db\ActiveRecord;

use Yii;

/**
 *  Модель для заблокированного администратором объекта времени - если объект был сдан
 *  например по телефону и в таком случае не должно быть показан в приложении
 *  что бы другие пользователи его бронировать
 *
 * @property integer $id
 * @property integer $hour
 * @property integer $year
 * @property integer $month
 * @property integer $day
 * @property integer $playing_field_id
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class UnavailableTime extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%unavailable_time}}';
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
            [['hour', 'playingFieldId', 'userId'], 'required'],
            [[ 'playingFieldId', 'userId',], 'integer'],
            [['hour'], 'string']
        ];
    }
}
