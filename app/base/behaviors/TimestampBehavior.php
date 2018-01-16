<?php

namespace app\base\behaviors;

/**
 * TimestampBehavior customized for the project.
 */
class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    /**
     * @inheritdoc
     */
    public $createdAtAttribute = 'createdAt';

    /**
     * @inheritdoc
     */
    public $updatedAtAttribute = 'updatedAt';
}
