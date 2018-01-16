<?php
namespace app\models;

use app\base\db\ActiveQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class SportComplexSearch
 * @package app\models
 */
class SportCenterSearch extends SportCenter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone'], 'string'],
        ];
    }

    /**
     * @param array $data
     * @return \yii\data\DataProviderInterface
     */
    public function search($data)
    {
        $table = self::tableName();

        $this->load($data, '');

        $moderSportCenters = self::find()
            ->andWhere(['not', ['sport_center.sportCenterId' => null]])
            ->all();
        $moderIds = ArrayHelper::getColumn($moderSportCenters, 'sportCenterId');
        $query = self::find()
            ->andWhere(['not in', 'sport_center.id', $moderIds])
            ->orderBy(["$table.[[createdAt]]" => SORT_DESC]);
        if (Yii::$app->user->identity->role == User::ROLE_ADMIN) {
            $query->joinWith('users')
                ->andWhere(['user.id' => Yii::$app->user->id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}