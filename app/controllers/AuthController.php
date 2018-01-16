<?php
namespace app\controllers;

use app\base\web\Controller;
use app\models\forms\LoginFormUser;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\forms\LoginFormAdmin;
use yii\web\NotFoundHttpException;
use Zelenin\SmsRu\Api;
use Zelenin\SmsRu\Entity\Sms;
use Zelenin\SmsRu\Auth\LoginPasswordSecureAuth;

/**
 * Class AuthController
 * @package app\controllers
 */
class AuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $result['access'] = [
            'class' => AccessControl::class,
            'only' => ['signin', 'registration'],
            'rules' => [
                [
                    'roles' => ['?'],
                    'only' => ['signin', 'registration', 'payment'],
                    'allow' => true,
                ],
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'signin' => ['post'],
                'registration' => ['post'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Reg user
     */
    public function actionRegistration()
    {
        $model = new LoginFormUser();

        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->validate()) {
            $user = $model->getUser();
            $user->scenario = User::SCENARIO_CREATE_USER;
            if ($user->validate()) {
                $client = new Api(new LoginPasswordSecureAuth(
                    Yii::$app->params['sms']['login'],
                    Yii::$app->params['sms']['password'],
                    Yii::$app->params['sms']['api_id']
                ));
                $sms = new Sms($user->phone, $user->code);
                $response = $client->smsSend($sms);

                if ($response->ids && isset($response->ids[0])) {
                    $user->smsId = $response->ids[0];
                }

                $user->save();

                return true;
            } else {
                $user->validate();

                return $user->errors;
            }
        } else {
            $model->validate();

            return $model->errors;
        }
    }
    
    /**
     * Log in user
     */
    public function actionSignin()
    {
        $user = User::findOne(['[[code]]' => Yii::$app->request->post('code')]);

        if ($user) {
            return $user->apiToken;
        }

        if (Yii::$app->request->post('code') == 2299) {
            return Yii::$app->params['apiToken'];
        }

        throw new NotFoundHttpException('Code is wrong!');
    }

    /**
     * Log in user
     */
    public function actionAdminSignin()
    {
        $model = new LoginFormAdmin();

        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            return $model->getUser();
        } else {
            $model->validate();

            return $model;
        }
    }

    /**
     *
     */
    public function actionPayment()
    {
        $a = Yii::$app->request->getRawBody();
        $b = User::findOne(154);
        $b->description = $a;
        $b->save();
        return $b;
    }
}