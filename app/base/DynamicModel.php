<?php

namespace app\base;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * DynamicModel is extended [[\yii\base\DynamicModel]].
 */
class DynamicModel extends \yii\base\DynamicModel
{
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function __construct(array $attributes = [], $config = [])
    {
        $rules = ArrayHelper::remove($config, 'rules', []);
        parent::__construct($attributes, $config);

        foreach ($rules as $rule) {
            if (!is_array($rule) || !isset($rule[0], $rule[1])) { // attributes, validator type
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }

            list($ruleAttributes, $ruleValidator) = [$rule[0], $rule[1]];
            unset($rule[0], $rule[1]);
            $this->addRule($ruleAttributes, $ruleValidator, $rule);
        }
    }
}
