<?php

namespace app\base\web;

use app\models\User;
use Yii;
use yii\web\UnauthorizedHttpException;

/**
 * Common web controller class.
 *
 * @property-read \yii\web\Request $request
 * @property-read \yii\web\Response $response
 */
class Controller extends \yii\rest\Controller
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

    /**
     * @param boolean $throwException
     * @throws UnauthorizedHttpException
     * @return User|null
     */
    public function getAuthorized($throwException = true)
    {
        $identity = Yii::$app->getUser()->getIdentity();
        if ($identity instanceof User) {
            return $identity;
        } elseif ($throwException) {
            throw new UnauthorizedHttpException;
        }

        return null;
    }
}
