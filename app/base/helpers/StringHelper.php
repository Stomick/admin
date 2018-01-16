<?php

namespace app\base\helpers;

use Yii;

/**
 * StringHelper is extended [[yii\helpers\StringHelper]] class.
 */
class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * @param string $string
     * @return string
     */
    public static function strtolower($string)
    {
        return mb_strtolower($string, Yii::$app->charset);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function strtoupper($string)
    {
        return mb_strtoupper($string, Yii::$app->charset);
    }
}
