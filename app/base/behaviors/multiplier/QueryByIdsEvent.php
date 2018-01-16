<?php

namespace app\base\behaviors\multiplier;

use app\base\db\QueryEvent;

/**
 * QueryByIdsEvent is used in [[Many2Many]].
 * @see Many2Many
 *
 */
class QueryByIdsEvent extends QueryEvent
{
    /**
     * @var array|null array of ids if querying will by a concrete list of ids,
     * otherwise null.
     */
    public $byIds;
}
