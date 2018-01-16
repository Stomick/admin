<?php

namespace app\base\behaviors\multiplier;

use yii\base\Event;

/**
 * ModelsEvent is used in [[One2Many]].
 * @see One2Many
 */
class ModelsEvent extends Event
{
    /**
     * @var \app\base\db\ActiveRecord[]
     */
    public $models;
}
