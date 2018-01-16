<?php

namespace app\console\base;

use Yii;

/**
 * Base console Controller class.
 *
 * @property-read \yii\console\Request $request
 * @property-write \yii\console\Response $response
 */
class Controller extends \yii\console\Controller
{
    /**
     * @return \yii\console\Request
     */
    public function getRequest()
    {
        return Yii::$app->getRequest();
    }

    /**
     * @return \yii\console\Response
     */
    public function getResponse()
    {
        return Yii::$app->getResponse();
    }
}
