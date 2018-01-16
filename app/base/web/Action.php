<?php

namespace app\base\web;

use Yii;

/**
 * Base Web Action Class.
 * 
 * @property-read \yii\web\Request $request
 * @property-read \yii\web\Response $response
 */
class Action extends \yii\rest\Action
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
