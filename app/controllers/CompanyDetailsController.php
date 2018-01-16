<?php

namespace app\controllers;

use app\base\web\ActiveController;
use app\files\UploadAction;
use app\models\CompanyDetails;
use app\models\CompanyDetailsSearch;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use Yii;

/**
 * Class CompanyDetailsController
 * @package app\controllers
 */
class CompanyDetailsController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = CompanyDetails::class;

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
                    'roles' => [User::ROLE_ADMIN],
                ],
                [
                    'allow' => true,
                    'actions' => [
                        'view',
                    ],
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

        $result['upload-image'] = [
            'class' => UploadAction::class,
            'context' => 'company-image',
            'postName' => 'file',
            'urlsScheme' => 'http',
        ];
        $result['upload-document'] = [
            'class' => UploadAction::class,
            'context' => 'company-document',
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
        $searchModel = new CompanyDetailsSearch();

        return $searchModel->search($this->request->get());
    }
}