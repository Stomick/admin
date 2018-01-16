<?php

namespace app\base\behaviors\multiplier;

use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\InvalidValueException;
use yii\base\NotSupportedException;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\validators\Validator;
use Yii;

use app\base\db\ActiveRecord;
use app\base\db\ActiveQuery;
use app\base\db\QueryEvent;
use app\base\helpers\StringHelper;

/**
 * Many2ManyTargets used as linker in [[MultiplierBehavior]].
 * It is like Many2Many linker, but it can to work with more than one targets.
 * There is may be when link table connects many records of one table
 * with many records of several others tables.
 *
 * @property array $keys
 * @property-read ActiveRecord[] $models
 */
class Many2ManyTargets extends AbstractLinker
{
    /**
     * @event QueryEvent
     */
    const EVENT_BEFORE_LINKS_QUERY = 'beforeLinksQuery';
    /**
     * @event ModelsEvent
     */
    const EVENT_AFTER_LINKS_QUERY = 'afterLinksQuery';
    /**
     * @event QueryByIdsEvent
     */
    const EVENT_BEFORE_MODELS_QUERY = 'beforeModelsQuery';
    /**
     * @event ModelsEvent
     */
    const EVENT_AFTER_MODELS_QUERY = 'afterModelsQuery';

    /**
     * @var string the name of class that links tables.
     */
    public $linkClass;

    /**
     * @var string the name of attribute in [[self::$linkClass]],
     * that links it with owner's model.
     */
    public $linkOwnerAttribute;

    /**
     * @var array keys of the array are the names of attributes in [[self::$linkClass]].
     * Values are target class names.
     */
    public $linkTargetClasses;

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
     * @var array|null
     */
    private $_keys = null;

    /**
     * @var ActiveRecord[]|null
     */
    private $_models = null;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    protected function checkConfig()
    {
        parent::checkConfig();

        if (!isset($this->linkClass, $this->linkOwnerAttribute)) {
            throw new InvalidConfigException('Properties $linkClass, $linkOwnerAttribute are required.');
        } elseif (!is_array($this->linkTargetClasses)) {
            throw new InvalidConfigException('Property $linkTargetClasses is required and must be an array.');
        }

        foreach ($this->linkTargetClasses as $targetClass) {
            if (!is_string($targetClass) || !is_a($targetClass, ActiveRecord::className(), true)) {
                throw new InvalidConfigException('Each value of property $linkTargetClasses must keep the name of class, that extends ' . ActiveRecord::className() . '.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getter()
    {
        return $this->getKeys();
    }

    /**
     * @param mixed $value
     */
    public function setter($value)
    {
        return $this->setKeys($value);
    }

    /**
     * @return array array of keys. Each key has format: `key-prefix#id`.
     */
    public function getKeys()
    {
        if ($this->_keys !== null) {
            return $this->_keys;
        } elseif ($this->owner->owner->isNewRecord) {
            return $this->_keys = $this->_models = [];
        }

        $links = $this->fetchLinks();
        return $this->_keys = array_keys($links);
    }

    /**
     * @param mixed $keys
     */
    public function setKeys($keys)
    {
        if ($keys === null) {
            $this->_keys = $this->_models = null;
        } elseif ($keys === '' || $keys === []) {
            $this->_keys = $this->_models = [];
        } else {
            $this->_keys = array_filter((array) $keys, 'is_scalar');
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
        } elseif (!$models = array_fill_keys($this->getKeys(), null)) {
            return $this->_models = [];
        }

        foreach ($this->linkTargetClasses as $linkTargetClass) {
            $keyPrefix = static::linkTargetClass2KeyPrefix($linkTargetClass) . '#';
            $keyPrefixLength = strlen($keyPrefix);

            $keys = [];
            foreach (array_keys($models) as $key) {
                if (!strncmp($key, $keyPrefix, $keyPrefixLength)) {
                    $keys[substr($key, $keyPrefixLength)] = $key;
                }
            }

            $targetModels = $this->fetchModels($linkTargetClass, array_keys($keys));
            foreach ($targetModels as $id => $model) {
                if (isset($keys[$id])) {
                    $models[$keys[$id]] = $model;
                }
            }
        }

        return $this->_models = array_filter($models);
    }

    /**
     * @param string $targetClass
     * @param array $ids
     * @return ActiveRecord[] fetched models indexed by primary key.
     * @throws InvalidParamException
     */
    protected function fetchModels($targetClass, $ids)
    {
        if (!is_array($ids)) {
            throw new InvalidParamException('Param $ids must be an array.');
        } elseif (!is_string($targetClass) || !is_a($targetClass, ActiveRecord::className(), true)) {
            throw new InvalidParamException('Param $targetClass must be a name of class, that extends ' . ActiveRecord::className() . '.');
        } elseif (empty($ids)) {
            return [];
        }

        $table = call_user_func([$targetClass, 'tableName']);
        $key = $this->getTargetKey($targetClass);
        $query = call_user_func([$targetClass, 'find']);
        /* @var $query ActiveQuery */

        $query->andWhere([
            "$table.[[$key]]" => $ids,
        ]);
        $query->indexBy($key);

        $query = $this->beforeModelsQuery($query, $ids) ?: $query;
        return $this->afterModelsQuery($query->all()) ?: [];
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
        $event->sender = $this;
        $event->byIds = $byIds;
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
     */
    public function validate($params = array())
    {
        $prefixes = [];
        foreach ($this->linkTargetClasses as $targetClass) {
            $keyPrefix = static::linkTargetClass2KeyPrefix($targetClass) . '#';
            $prefixes[$targetClass] = [$keyPrefix, strlen($keyPrefix)];
        }

        $targetClasses = [];
        foreach ($this->getKeys() as $key) {
            foreach ($prefixes as $targetClass => $prefixData) {
                list($keyPrefix, $keyPrefixLength) = $prefixData;
                if (!strncmp($key, $keyPrefix, $keyPrefixLength)) {
                    $targetClasses[$targetClass][] = substr($key, $keyPrefixLength);
                    continue 2;
                }
            }

            $error = ArrayHelper::getValue($params, 'message', Yii::t('yii', '{attribute} is invalid.', [
                'attribute' => $this->owner->owner->getAttributeLabel($this->ownerAttribute),
            ]));
            $this->owner->owner->addError($this->ownerAttribute, $error);
            return false;
        }

        foreach ($targetClasses as $targetClass => $ids) {
            $paramName = 'exist' . StringHelper::basename($targetClass);
            $validatorParams = (array) ArrayHelper::getValue($params, $paramName, []);
            $existsValidator = Validator::createValidator('exist', $this->owner->owner, [], array_merge([
                'targetClass' => $targetClass,
                'targetAttribute' => $this->getTargetKey($targetClass),
                'allowArray' => true,
            ], $validatorParams));

            if (!$existsValidator->validate($ids, $error)) {
                $this->owner->owner->addError($error);
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     * @throws InvalidValueException
     */
    protected function saveInternal($ownerInsertOperation = null)
    {
        $keys = $this->getKeys();
        if (!empty($keys)) {
            $keys = array_combine($keys, $keys);
        }

        $links = $ownerInsertOperation === true ? [] : $this->fetchLinks();

        foreach (array_diff_key($links, $keys) as $key => $link) {
            if ($link->delete() === false) {
                throw new InvalidValueException("Cannot delete $this->linkClass [$key] record.");
            }
        }

        $prefixes = [];
        foreach ($this->linkTargetClasses as $attribute => $targetClass) {
            $keyPrefix = static::linkTargetClass2KeyPrefix($targetClass) . '#';
            $prefixes[$attribute] = [$keyPrefix, strlen($keyPrefix)];
        }

        $position = 0;
        foreach ($keys as $key) {
            if ($exists = isset($links[$key])) {
                $link = $links[$key];
            } else {
                $link = $this->createLinkModel();
                $found = false;
                foreach ($prefixes as $attribute => $keyPrefixData) {
                    list($keyPrefix, $keyPrefixLength) = $keyPrefixData;
                    if (!strncmp($key, $keyPrefix, $keyPrefixLength)) {
                        $link->{$attribute} = substr($key, $keyPrefixLength);
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    throw new InvalidValueException("Cannot find link target attribute for key: '$key'.");
                }
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
                throw new InvalidValueException("Cannot save $this->linkClass for [#$ownerId, $key].");
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
            $this->_keys = $this->_models = [];
            return;
        }

        foreach ($this->fetchLinks() as $key => $link) {
            if ($link->delete() === false) {
                throw new InvalidValueException("Cannot delete $this->linkClass [$key] record.");
            }
        }

        $this->_keys = $this->_models = [];
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
     * Generate key prefix from link target class name.
     * @param string $linkTargetClass
     * @return string
     */
    public static function linkTargetClass2KeyPrefix($linkTargetClass)
    {
        return Inflector::camel2id(StringHelper::basename($linkTargetClass));
    }

    /**
     * @return ActiveRecord[] links in key => ActiveRecord format.
     * Keys are in `class-short-name#id` format.
     * @throws InvalidValueException
     */
    protected function fetchLinks()
    {
        $tableLink = call_user_func([$this->linkClass, 'tableName']);
        $linksQuery = call_user_func([$this->linkClass, 'find']);
        /* @var $linksQuery ActiveQuery */

        $linksQuery->andWhere([
            "$tableLink.[[$this->linkOwnerAttribute]]" => $this->owner->owner->{$this->ownerKey},
        ]);
        if ($this->linkPositionAttribute !== null) {
            $linksQuery->orderBy([
                "$tableLink.[[$this->linkPositionAttribute]]" => SORT_ASC,
            ]);
        }

        $linksQuery = $this->beforeLinksQuery($linksQuery) ?: $linksQuery;
        $links = $linksQuery->all();
        /* @var $links ActiveRecord[] */

        foreach ($links as $link) {
            $link->useTransactionsByDefault = false;
            if ($this->linkScenario !== false) {
                $link->setScenario($this->linkScenario ?: $this->owner->owner->getScenario());
            }
            $this->populateRelationsByOwner($link);
        }
        $links = $this->afterLinksQuery($links) ?: [];

        $attributes = [];
        foreach ($this->linkTargetClasses as $linkTargetAttribute => $linkTargetClass) {
            $attributes[$linkTargetAttribute] = static::linkTargetClass2KeyPrefix($linkTargetClass);
        }

        $result = [];
        foreach ($links as $link) {
            foreach ($attributes as $linkTargetAttribute => $keyPrefix) {
                if (isset($link->{$linkTargetAttribute}) && ($key = $link->{$linkTargetAttribute})) {
                    $result["$keyPrefix#$key"] = $link;
                    continue 2;
                }
            }

            $primaryKeys = [];
            foreach ($link->getPrimaryKey(true) as $name => $value) {
                $primaryKeys[] = "$name: $value";
            }
            throw new InvalidValueException("Link record $this->linkClass #(" . implode(', ', $primaryKeys) . ') has invalid type.');
        }

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

    /**
     * @param string $targetClass
     * @return string
     * @throws InvalidParamException
     * @throws InvalidValueException
     * @throws NotSupportedException
     */
    public function getTargetKey($targetClass)
    {
        if (!is_string($targetClass) || !is_a($targetClass, ActiveRecord::className(), true)) {
            throw new InvalidParamException('Param $targetClass must be a name of class, that extends ' . ActiveRecord::className() . '.');
        }

        $primaryKeys = call_user_func([$targetClass, 'primaryKey']);
        if (!is_array($primaryKeys)) {
            throw new InvalidValueException("Invalid value was returned by `$targetClass::primaryKey()` method: " . gettype($primaryKeys) . '.');
        } elseif (count($primaryKeys) !== 1) {
            throw new NotSupportedException(get_class($this) . ' does not support multiple primary keys.');
        }

        return reset($primaryKeys);
    }
}
