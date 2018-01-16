<?php
namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class BookingSearch
 * @package app\models
 */
class BookingSearch extends Booking
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'string'],
        ];
    }

    /**
     * @param array $data
     * @return \yii\data\DataProviderInterface
     */
    public function search($data)
    {
        $table = self::tableName();

//        $isAdmin = (Yii::$app->user->identity ? Yii::$app->user->identity->role === User::ROLE_ADMIN : false);
        $this->load($data, '');
        $query = self::find()
            ->orderBy(["$table.[[createdAt]]" => SORT_DESC]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}