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
class AdminController extends ActiveController
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
                    'roles' => [User::ROLE_SUPER_ADMIN],
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
        $result['create']['scenario'] = User::SCENARIO_CREATE_ADMIN;
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
                ->andWhere(['[[role]]' => User::ROLE_ADMIN])
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
            '[[role]]' => User::ROLE_ADMIN,
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
     * @param $email
     * @param $originLink
     * @throws NotFoundHttpException
     */
    public function actionChangePassword($email, $originLink)
    {
        $user = User::findOne(['[[email]]' => $email]);

        if (!$user) {
            throw new NotFoundHttpException("Email not found: $email");
        }

        $password = Yii::$app->security->generateRandomString(8);
        $user->changePassword($password);
        $user->save(false);
//        $user->sendEmailToUser($user, $password, $originLink);
    }
    
    
    public function actionList()
    {
        return User::find()->all();
    }

    public function actionChangeRole($id, $role)
    {
        $user = User::findOne($id);
        $user->role = $role;
        
        return $user->save(false);
    }
}