<?php
namespace app\base\web;

use Yii;

/**
 * Class ActiveController
 * @package app\base\web
 *
 * @property-read \yii\web\Request $request
 * @property-read \yii\web\Response $response
 */
class ActiveController extends \yii\rest\ActiveController
{
    /**
     * @return \yii\web\Request
     */
    public function getRequest()
    {
        return Yii::$app->getRequest();
    }

    /**
     * @return \yii\web\Response
     */
    public function getResponse()
    {
        return Yii::$app->getResponse();
    }
}