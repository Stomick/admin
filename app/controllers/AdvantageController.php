<?php

namespace app\controllers;

use app\base\web\ActiveController;
use app\files\UploadAction;
use app\models\Advantage;
use app\models\AdvantageSearch;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use Yii;

/**
 * Class AdvantageController
 * @package app\controllers
 */
class AdvantageController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = Advantage::class;

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
                        'index',
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
        $result['create']['scenario'] = Advantage::SCENARIO_CREATE;
        $result['update']['scenario'] = Advantage::SCENARIO_UPDATE;
        $result['upload-icon'] = [
            'class' => UploadAction::class,
            'context' => 'advantage-icon',
            'postName' => 'file',
            'urlsScheme' => 'http',
        ];
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataProvider()
    {
        $model = Advantage::find()->all();

        return $model;
    }

    public function actionTest()
    {
           var_dump(Yii::getAlias('@web') . '/upload/files/');die;
    }
}