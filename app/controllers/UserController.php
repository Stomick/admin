<?php

namespace app\controllers;

use app\base\web\ActiveController;
use app\controllers\actions\user\CreateAction;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class UserController
 * @package app\controllers
 */
class UserController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = User::class;

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
        $result['create']['class'] = CreateAction::class;
        $result['create']['scenario'] = User::SCENARIO_CREATE_USER;
        $result['update']['scenario'] = User::SCENARIO_UPDATE;

        unset($result['delete']);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataProvider()
    {
        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;

        return new ActiveDataProvider([
            'query' => $modelClass::find()
                ->andWhere(['[[role]]' => User::ROLE_USER])
                ->andFilterWhere(['LIKE', '[[username]]', $this->request->get('username', null)])
            ,
        ]);
    }

    /**
     * Delete user
     *
     * @param integer $id
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDelete($id)
    {
        $model = User::find()->andWhere([
            '[[role]]' => User::ROLE_USER,
            '[[id]]' => $id,
        ])->one();

        if (!$model) {
            throw new NotFoundHttpException("Object not found: $id");
        }
        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        \Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     *
     */
    public function actionListAdmins()
    {
        return User::find()
            ->select(['id', 'username'])
            ->andWhere(['[[role]]' => User::ROLE_ADMIN])
            ->asArray()
            ->orderBy('id')
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function actionNotification()
    {
        $params = Yii::$app->request->post();
        $user = User::findOne(['[[id]]' => $params['userId']]);
        
        if (!$user) {
            throw new NotFoundHttpException("User not found!");
        }

        return Yii::$app->getSecurity()->validatePassword($params['password'], $user->passwordHash);
    }

    /**
     * @throws NotFoundHttpException
     * @return boolean
     */
    public function actionChangePassword()
    {
        $params = Yii::$app->request->post();
        $user = User::findOne(['[[id]]' => $params['userId']]);

        if (!$user) {
            throw new NotFoundHttpException("User not found!");
        }

        $user->changePassword($params['newPassword']);

        return $user->save();
    }
}