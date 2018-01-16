<?php
namespace app\controllers;

use app\base\web\Controller;
use app\controllers\actions\UploadAction;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use app\models\User;

class RedactorController extends Controller
{
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
                        'upload-image',
                        'upload-file',
                    ],
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
        $result['upload-image'] = [
            'class' => UploadAction::class,
            'context' => 'redactor-image',
            'postName' => 'file',
            'urlsScheme' => 'http',
        ];
        $result['upload-file'] = [
            'class' => UploadAction::class,
            'context' => 'redactor-file',
            'postName' => 'file',
            'urlsScheme' => 'http',
        ];

        return $result;
    }
}