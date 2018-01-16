<?php

namespace app\base\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

use app\base\helpers\StringHelper;

/**
 * LowerizeBehavior change case of some fields to lower.
 * This behavior may be used for normalizing string fields.
 */
class LowerizeBehavior extends Behavior
{
    /**
     * @var array list of attributes that are to be automatically normalized with the value specified via [[value]].
     * If key specified as string it will be used as destination attribute, than value will be used as a source attribute.
     * If key is integer value will be used as destination and as source.
     * ```php
     * [
     *      'login', // login attribute will be filled by mb_strtolower('login')
     *      'normalizedName' => 'name', // normalizedName attribute will be filled by mb_strtolower('name')
     * ]
     * ```
     */
    public $attributes = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'evaluateAttributes',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'evaluateAttributes',
        ];
    }

    /**
     * Evaluates the attribute value and assigns it to the current attributes.
     */
    public function evaluateAttributes()
    {
        foreach ($this->attributes as $key => $source) {
            $destination = is_int($key) ? $source : $key;
            $value = $this->owner->{$source};

            if ($value === null) {
                $this->owner->{$destination} = null;
            } else {
                $this->owner->{$destination} = StringHelper::strtolower($value);
            }
        }
    }
}
