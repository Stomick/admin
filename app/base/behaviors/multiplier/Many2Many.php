<?php

namespace app\base\behaviors\multiplier;

use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\InvalidValueException;
use yii\base\NotSupportedException;
use yii\db\Connection;
use yii\validators\Validator;

use app\base\db\ActiveRecord;
use app\base\db\ActiveQuery;
use app\base\db\QueryEvent;

/**
 * Many2Many keeps config and data of many2many link for [[MultiplierBehavior]].
 *
 * @property array $ids
 * @property-read ActiveRecord[] $models
 * @property-read string $targetKey the name of target's primary key.
 */
class Many2Many extends AbstractLinker
{
    /**
     * @event QueryByIdsEvent
     */
    const EVENT_BEFORE_MODELS_QUERY = 'beforeModelsQuery';
    /**
     * @event ModelsEvent
     */
    const EVENT_AFTER_MODELS_QUERY = 'afterModelsQuery';
    /**
     * @event QueryEvent
     */
    const EVENT_BEFORE_LINKS_QUERY = 'beforeLinksQuery';
    /**
     * @event ModelsEvent
     */
    const EVENT_AFTER_LINKS_QUERY = 'afterLinksQuery';

    /**
     * @var string the name of class that links 2 tables.
     */
    public $linkClass;

    /**
     * @var string the name of attribute in [[self::$linkClass]],
     * that links it with owner's model.
     */
    public $linkOwnerAttribute;

    /**
     * @var string the name of attribute in [[self::$linkClass]],
     * that links it with [[self::$targetClass]] model.
     */
    public $linkTargetAttribute;

    /**
     * @var string the name of attribute in [[self::$linkClass]],
     * that keeps position value.
     */
    public $linkPositionAttribute;

    /**
     * @var string|array the name(s) of link's relations that must be populated
     * by owner active record.
     */
    public $linkPopulateRelations;

    /**
     * @var string|null|boolean scenario for link models.
     * Null is meaning scenario of the owner will be used.
     * False (default) is meaning scenario will not be changed.
     */
    public $linkScenario = false;

    /**
     * @var string the name of target class.
     */
    public $targetClass;

    /**
     * @var array|null
     */
    private $_ids = null;

    /**
     * @var ActiveRecord[]|null
     */
    private $_models = null;

    /**
     * @inheritdoc
     */
    protected function checkConfig()
    {
        parent::checkConfig();

        if ($this->targetClass === null) {
            throw new InvalidConfigException('Property $targetClass is required.');
        } elseif (!is_string($this->targetClass) || !is_a($this->targetClass, ActiveRecord::className(), true)) {
            throw new InvalidConfigException('Property $targetClass must keep the name of class, that extends ' . ActiveRecord::className() . '.');
        } elseif (!isset($this->linkClass, $this->linkOwnerAttribute, $this->linkTargetAttribute)) {
            throw new InvalidConfigException('Properties ' . implode(', ', [
                '$linkClass',
                '$linkOwnerAttribute',
                '$linkTargetAttribute',
            ]) . ' are required.');
        } elseif (!is_string($this->linkClass) || !is_a($this->linkClass, ActiveRecord::className(), true)) {
            throw new InvalidConfigException('Property $linkClass must keep the name of class, that extends ' . ActiveRecord::className() . '.');
        }
    }

    /**
     * @inheritdoc
     */
    public function getter()
    {
        return $this->getIds();
    }

    /**
     * @inheritdoc
     */
    public function setter($value)
    {
        return $this->setIds($value);
    }

    /**
     * @return array
     */
    public function getIds()
    {
        if ($this->_ids !== null) {
            return $this->_ids;
        } elseif ($this->owner->owner->isNewRecord) {
            return $this->_ids = $this->_models = [];
        }

        $tableTarget = call_user_func([$this->targetClass, 'tableName']);
        $tableLink = call_user_func([$this->linkClass, 'tableName']);

        $modelsQuery = call_user_func([$this->targetClass, 'find']);
        /* @var $modelsQuery ActiveQuery */
        $modelsQuery->indexBy($this->targetKey);

        $modelsQuery->innerJoin(
            "$tableLink $tableLink",
            "$tableLink.[[$this->linkTargetAttribute]] = $tableTarget.[[$this->targetKey]]"
        );
        $modelsQuery->andWhere([
            "$tableLink.[[$this->linkOwnerAttribute]]" => $this->owner->owner->{$this->ownerKey},
        ]);

        if ($this->linkPositionAttribute !== null) {
            $modelsQuery->orderBy([
                "$tableLink.[[$this->linkPositionAttribute]]" => SORT_ASC,
            ]);
        }

        $modelsQuery = $this->beforeModelsQuery($modelsQuery, null) ?: $modelsQuery;
        $models = $this->afterModelsQuery($modelsQuery->all()) ?: [];

        $this->_models = $models;
        return $this->_ids = array_keys($models);
    }

    /**
     * @param mixed $ids
     */
    public function setIds($ids)
    {
        if ($ids === null) {
            $this->_ids = $this->_models = null;
        } elseif ($ids === '' || $ids === []) {
            $this->_ids = $this->_models = [];
        } else {
            $this->_ids = array_filter((array) $ids, 'is_scalar');
            $this->_models = null;
        }
    }

    /**
     * @return ActiveRecord[]
     */
    public function getModels()
    {
        if ($this->_models !== null) {
            return $this->_models;
        } elseif (!$ids = $this->getIds()) {
            return $this->_models = [];
        }

        $table = call_user_func([$this->targetClass, 'tableName']);
        $modelsQuery = call_user_func([$this->targetClass, 'find']);
        /* @var $modelsQuery ActiveQuery */

        $modelsQuery->indexBy($this->targetKey);
        $modelsQuery->andWhere(['IN', "$table.[[$this->targetKey]]", $ids]);

        $modelsQuery = $this->beforeModelsQuery($modelsQuery, $ids) ?: $modelsQuery;
        $models = $this->afterModelsQuery($modelsQuery->all()) ?: [];

        $result = [];
        foreach ($ids as $id) {
            if (isset($models[$id])) {
                $result[$id] = $models[$id];
            }
        }
        return $this->_models = $result;
    }

    /**
     * @param ActiveQuery $query
     * @param array|null $byIds array of ids, null meaning querying is not by ids.
     * @return ActiveQuery
     * @throws InvalidCallException
     */
    protected function beforeModelsQuery($query, $byIds)
    {
        if (!$query instanceof ActiveQuery) {
            throw new InvalidCallException('Param $query must be an instance of ' . ActiveQuery::className() . '.');
        }

        $event = new QueryByIdsEvent;
        $event->query = $query;
        $event->byIds = $byIds;
        $event->sender = $this;
        $this->trigger(self::EVENT_BEFORE_MODELS_QUERY, $event);

        return $event->query;
    }

    /**
     * @param ActiveRecord[] $models
     * @return ActiveRecord[]
     */
    protected function afterModelsQuery($models)
    {
        $event = new ModelsEvent();
        $event->models = $models;
        $event->sender = $this;
        $this->trigger(self::EVENT_AFTER_MODELS_QUERY, $event);

        return $event->models;
    }

    /**
     * @inheritdoc
     * @throws InvalidValueException
     */
    protected function saveInternal($ownerInsertOperation = null)
    {
        $ids = $this->getIds();
        if (!empty($ids)) {
            $ids = array_combine($ids, $ids);
        }

        $links = $ownerInsertOperation === true ? [] : $this->fetchLinks();
        /* @var $links ActiveRecord[] */

        foreach (array_diff_key($links, $ids) as $id => $link) {
            /* @var $link ActiveRecord */
            if ($link->delete() === false) {
                throw new InvalidValueException("Cannot delete $this->linkClass #$id record.");
            }
        }

        $position = 0;
        foreach ($ids as $id) {
            if ($exists = isset($links[$id])) {
                $link = $links[$id];
            } else {
                $link = $this->createLinkModel();
                $link->{$this->linkTargetAttribute} = $id;
            }

            if ($this->linkPositionAttribute === null) {
                if ($exists) {
                    continue;
                }
            } elseif (intval($link->{$this->linkPositionAttribute}) === ++$position && $exists) {
                continue;
            } else {
                $link->{$this->linkPositionAttribute} = $position;
            }

            if (!$link->save(false)) {
                $ownerId = $this->owner->owner->{$this->ownerKey};
                throw new InvalidValueException("Cannot save $this->linkClass for [#$ownerId, #$id] record.");
            }
        }
    }

    /**
     * @inheritdoc
     * @throw InvalidValueException
     */
    protected function deleteInternal($isOwnerDeletion = false)
    {
        if (!$isOwnerDeletion && $this->owner->owner->getIsNewRecord()) {
            $this->_ids = $this->_models = [];
            return;
        }

        foreach ($this->fetchLinks() as $id => $link) {
            if ($link->delete() === false) {
                throw new InvalidValueException("Cannot delete $this->linkClass #$id record.");
            }
        }

        $this->_ids = $this->_models = [];
    }

    /**
     * @return ActiveRecord
     */
    public function createLinkModel()
    {
        $result = new $this->linkClass;
        /* @var $result ActiveRecord */

        $result->useTransactionsByDefault = false;
        if ($this->linkScenario !== false) {
            $result->setScenario($this->linkScenario ?: $this->owner->owner->getScenario());
        }
        if ($key = $this->owner->owner->{$this->ownerKey}) {
            $result->{$this->linkOwnerAttribute} = $key;
        }
        $this->populateRelationsByOwner($result);

        return $result;
    }

    /**
     * Populates `$record` relations by owner active record according to [[self::$linkPopulateRelations]] config.
     * @param ActiveRecord $record
     * @throws InvalidParamException
     */
    protected function populateRelationsByOwner($record)
    {
        if (!$record instanceof ActiveRecord) {
            throw new InvalidParamException('Param $link must be an instance of ' . ActiveRecord::className() . '.');
        }
        $owner = $this->owner->owner;
        foreach ((array) $this->linkPopulateRelations as $relation) {
            $multiple = $record->getRelation($relation)->multiple;
            $record->populateRelation($relation, $multiple ? [$owner] : $owner);
        }
    }

    /**
     * @return ActiveRecord[] indexed by link target's attribute.
     */
    protected function fetchLinks()
    {
        $ownerId = $this->owner->owner->{$this->ownerKey};
        $table = call_user_func([$this->linkClass, 'tableName']);

        $linksQuery = call_user_func([$this->linkClass, 'find']);
        /* @var $linksQuery ActiveQuery */
        $linksQuery->andWhere([
            "$table.[[$this->linkOwnerAttribute]]" => $ownerId,
        ]);
        $linksQuery->indexBy($this->linkTargetAttribute);

        $linksQuery = $this->beforeLinksQuery($linksQuery) ?: $linksQuery;
        $links = $linksQuery->all();

        foreach ($links as $link) {
            $link->useTransactionsByDefault = false;
            if ($this->linkScenario !== false) {
                $link->setScenario($this->linkScenario ?: $this->owner->owner->getScenario());
            }
            $this->populateRelationsByOwner($link);
        }

        return $this->afterLinksQuery($links) ?: [];
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveQuery
     * @throws InvalidCallException
     */
    protected function beforeLinksQuery($query)
    {
        if (!$query instanceof ActiveQuery) {
            throw new InvalidCallException('Param $query must be an instance of ' . ActiveQuery::className() . '.');
        }

        $event = new QueryEvent;
        $event->query = $query;
        $event->sender = $this;
        $this->trigger(self::EVENT_BEFORE_LINKS_QUERY, $event);

        return $event->query;
    }

    /**
     * @param ActiveRecord[] $models
     * @return ActiveRecord[]
     */
    protected function afterLinksQuery($models)
    {
        $event = new ModelsEvent();
        $event->models = $models;
        $event->sender = $this;
        $this->trigger(self::EVENT_AFTER_LINKS_QUERY, $event);

        return $event->models;
    }

    /**
     * @inheritdoc
     */
    public function validate($params = [])
    {
        $validator = Validator::createValidator('exist', $this->owner->owner, $this->ownerAttribute, array_merge([
            'targetClass' => $this->targetClass,
            'targetAttribute' => $this->targetKey,
            'allowArray' => true,
        ], (array) $params));
        $validator->validateAttributes($this->owner->owner);
        return !$this->owner->owner->hasErrors($this->ownerAttribute);
    }

    /**
     * @return string the name of target's primary key.
     * @throws InvalidValueException
     * @throws NotSupportedException
     */
    public function getTargetKey()
    {
        $keys = call_user_func([$this->targetClass, 'primaryKey']);
        if (!is_array($keys)) {
            throw new InvalidValueException("Invalid value was returned by $this->targetClass::primaryKey() method.");
        } elseif (count($keys) !== 1) {
            throw new NotSupportedException(static::className() . ' works with single primary keys only.');
        }
        return reset($keys);
    }

    /**
     * @inheritdoc
     * @throws InvalidValueException
     */
    public function getDb()
    {
        $result = call_user_func([$this->linkClass, 'getDb']);
        if (!$result instanceof Connection) {
            throw new InvalidValueException("Unexpected value of `$this->linkClass::getDb()` method, expected an instance of " . Connection::className() . ' as result.');
        }
        return $result;
    }
}
