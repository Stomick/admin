<?php

namespace app\base\helpers;

use yii\base\InvalidValueException;
use yii\web\JsExpression;

/**
 * Json helper is same as yii Json helper but with some added methods.
 */
class Json extends \yii\helpers\Json
{
    /**
     * @param mixed $value
     * @return JsExpression|mixed
     * @throws InvalidValueException
     */
    public static function createExpressionForNumber($value)
    {
        if ($value === null || is_int($value) || is_float($value)) {
            return $value;
        } elseif (!is_string($value) || trim($value) === '') {
            return null;
        } elseif (!preg_match('/^[\+\-]?\d[\d]*([,\.]\d[\d]*)?$/S', $value)) {
            throw new InvalidValueException("Unexpected number value: '$value'.");
        } else {
            return new JsExpression(str_replace(',', '.', $value));
        }
    }
}
