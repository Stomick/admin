<?php

namespace app\controllers;

use app\base\web\ActiveController;
use app\models\Booking;
use app\models\PlayingField;
use app\models\AvailableTime;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotFoundHttpException;

/**
 * Class CompanyDetailsController
 * @package app\controllers
 */
class PlayingFieldController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = PlayingField::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'create-route',
                        'delete-playfileds'
                    ],
                    'roles' => [User::ROLE_USER],
                ],
                [
                    'allow' => true,
                    'roles' => [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $result = parent::actions();
        $result['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataProvider()
    {
        $playingFields = PlayingField::find()->all();

        return $playingFields;
    }

    /**
     * @param integer $id
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @param integer $hour
     * @param string $type
     *
     * @return array
     */
    public function actionList($id, $year, $month, $day, $hour, $type)
    {
        $tablePlayingField = PlayingField::tableName();
        $tableAvailableTime = AvailableTime::tableName();
        $subQuery = Booking::find()
            ->select('availableTimeId')
            ->andWhere(['[[year]]' => $year])
            ->andWhere(['[[month]]' => $month])
            ->andWhere(['[[day]]' => $day]);

        return PlayingField::find()
            ->joinWith(['availableTimes'])
            ->andWhere([$tablePlayingField . '.[[sportCenterId]]' => $id])
            ->andWhere(['not in', $tableAvailableTime . '.id', $subQuery])
            ->andWhere([$tableAvailableTime . '.[[type]]' => $type])
            ->andWhere([$tableAvailableTime . '.[[hour]]' => $hour])
            ->orderBy($tablePlayingField . '.name')
            ->all();
    }

    /**
     * @param integer $id
     * @throws NotFoundHttpException
     * @return array
     */
    public function actionCreateRoute($id)
    {
        $tablePlayingField = PlayingField::tableName();
        $playingField = PlayingField::find()
            ->with(['sportCenter'])
            ->andWhere([$tablePlayingField . '.[[id]]' => $id])
            ->one();

        /** @var $playingField PlayingField */

        if (!$playingField) {
            throw new NotFoundHttpException('Not Found Playing Field');
        }

        return [
            'id' => $playingField->id,
            'name' => $playingField->name,
            'info' => $playingField->info,
            'sportCenterName' => $playingField->sportCenter->name,
            'longitude' => $playingField->sportCenter->longitude,
            'latitude' => $playingField->sportCenter->latitude,
        ];
    }
}