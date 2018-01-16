<?php

namespace app\base\db;

use yii\base\Event;

/**
 * QueryEvent
 */
class QueryEvent extends Event
{
    /**
     * @var ActiveQuery
     */
    public $query;
}
