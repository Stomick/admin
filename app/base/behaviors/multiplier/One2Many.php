<?php

namespace app\base\behaviors\multiplier;

use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\InvalidValueException;
use yii\base\NotSupportedException;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

use app\base\db\ActiveRecord;
use app\base\db\ActiveQuery;
use app\base\db\QueryEvent;
use app\base\helpers\StringHelper;

/**
 * One2Many keeps config and data of one2many link for [[MultiplierBehavior]].
 *
 *
 * @property ActiveRecord[] $models indexed by target's key.
 * @property-read ActiveRecord[] $deletedModels indexed by target's key
 * @property-read string $targetKey the name of target's primary key.
 */
class One2Many extends AbstractLinker
{
    /**
     * @event QueryEvent
     */
    const EVENT_BEFORE_MODELS_QUERY = 'beforeModelsQuery';
    /**
     * @event ModelsEvent
     */
    const EVENT_AFTER_MODELS_QUERY = 'afterModelsQuery';

    /**
     * @var string the name of target class.
     */
    public $targetClass;

    /**
     * @var string the name of field that links with owner's table.
     */
    public $targetAttribute;

    /**
     * @var string the name of position field (optional).
     */
    public $targetPositionAttribute;

    /**
     * @var string|array the name(s) of target's relations that must be populated
     * by owner active record.
     */
    public $targetPopulateRelations;

    /**
     * @var string|null|boolean scenario for target models.
     * Null is meaning scenario of the owner will be used.
     * False (default) is meaning scenario will not be changed.
     */
    public $targetScenario = false;

    /**
     * You must configure this property as `true` if your target table has unique indexes.
     * Otherwise in this situation must be integrity fails, e.g. if you changed order of unique fields.
     * In `YII_DEBUG` mode this class checked table unique indexes and this property value.
     * @see [[self::checkUniqueIndexes]]
     * @var boolean whether need to delete all records and than to insert them as new records.
     * @see [[self::save()]]
     */
    public $deleteAllBeforeSaving = false;

    /**
     * @var boolean whether submodels errors must be added to error text for this link attribute.
     */
    public $addModelsErrorsText = false;

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
        } elseif (!isset($this->targetAttribute)) {
            throw new InvalidConfigException('Property $targetAttribute is required.');
        }

        if (YII_DEBUG) {
            $this->checkUniqueIndexes();
        }
    }

    /**
     * Checks target table's unique indexes. And if they are exist and property
     * [[self::$deleteAllBeforeSaving]] is not `true` than the method will throw exception.
     * @see [[self::$deleteAllBeforeSaving]]
     * @throws InvalidConfigException if table has unique indexes and [[self::$deleteAllBeforeSaving]] is no `true`.
     * @throws InvalidValueException
     */
    protected function checkUniqueIndexes()
    {
        if ($this->deleteAllBeforeSaving) {
            return;
        }

        $db = $this->getDb();
        $schema = $db->getSchema();

        $tableName = call_user_func([$this->targetClass, 'tableName']);
        $tableSchema = $db->getTableSchema($tableName);

        try {
            $hasUniqueIndexes = (bool) $schema->findUniqueIndexes($tableSchema);
        } catch (NotSupportedException $ex) {
            // database does not support unique indexes
            return;
        }

        if ($hasUniqueIndexes) {
            throw new InvalidConfigException("Your table '$tableName' has unique indexes, so you must to drop unique indexes or set " . static::className() . '::$deleteAllBeforeSaving property to true.');
        }
    }

    /**
     * @inheritdoc
     */
    public function getter()
    {
        return $this->getModels();
    }

    /**
     * @inheritdoc
     */
    public function setter($value)
    {
        return $this->setModels($value);
    }

    /**
     * @var ActiveRecord[]|null
     */
    private $_models = null;

    /**
     * @var ActiveRecord[]
     */
    private $_deletedModels = [];

    /**
     * @return ActiveRecord[] indexed by target's key.
     */
    public function getModels()
    {
        if ($this->_models !== null) {
            return $this->_models;
        } elseif ($this->owner->owner->isNewRecord) {
            return $this->_models = [];
        }

        $table = call_user_func([$this->targetClass, 'tableName']);
        $modelsQuery = call_user_func([$this->targetClass, 'find']);
        /* @var $modelsQuery ActiveQuery */

        $modelsQuery->indexBy($this->targetKey);
        $modelsQuery->andWhere([
            "$table.[[$this->targetAttribute]]" => $this->owner->owner->{$this->ownerKey},
        ]);

        if ($this->targetPositionAttribute !== null) {
            $modelsQuery->orderBy([
                "$table.[[$this->targetPositionAttribute]]" => SORT_ASC,
            ]);
        }

        $modelsQuery = $this->beforeModelsQuery($modelsQuery) ?: $modelsQuery;
        $models = $modelsQuery->all();
        /* @var $models ActiveRecord[] */

        foreach ($models as $model) {
            $model->useTransactionsByDefault = false;
            if ($this->targetScenario !== false) {
                $model->setScenario($this->targetScenario ?: $this->owner->owner->getScenario());
            }
            $this->populateRelationsByOwner($model);
        }

        return $this->_models = $this->afterModelsQuery($models) ?: [];
    }

    /**
     * @param mixed $models
     */
    public function setModels($models)
    {
        if ($models === null) {
            $this->_models = null;
            $this->_deletedModels = [];
            return;
        } elseif (!is_array($models)) {
            $models = [];
        }

        $dbModels = array_replace($this->_deletedModels, $this->getModels());
        $resultModels = [];
        $position = 0;
        foreach ($models as $key => $model) {
            if (isset($dbModels[$key])) {
                $dbModel = $dbModels[$key];
                unset($dbModels[$key]);
            } else {
                $dbModel = $this->createTargetModel();
            }
            /* @var $dbModel ActiveRecord */

            $modelAttributes = [];
            foreach (is_object($model) ? $model : (array) $model as $k => $v) {
                $modelAttributes[$k] = $v;
            }
            $dbModel->setAttributes($modelAttributes);

            if ($this->targetPositionAttribute !== null) {
                if (intval($dbModel->{$this->targetPositionAttribute}) !== ++$position) {
                    $dbModel->{$this->targetPositionAttribute} = $position;
                }
            }

            $resultModels[$key] = $dbModel;
        }
        foreach ($dbModels as $key => $dbModel) {
            if ($dbModel->getIsNewRecord()) {
                unset($dbModels[$key]);
            }
        }

        $this->_models = $resultModels;
        $this->_deletedModels = $dbModels;
    }

    /**
     * @return ActiveRecord
     */
    public function createTargetModel()
    {
        $result = new $this->targetClass;
        /* @var $result ActiveRecord */

        $result->useTransactionsByDefault = false;
        if ($this->targetScenario !== false) {
            $result->setScenario($this->targetScenario ?: $this->owner->owner->getScenario());
        }
        if ($key = $this->owner->owner->{$this->ownerKey}) {
            $result->{$this->targetAttribute} = $key;
        }
        $this->populateRelationsByOwner($result);

        return $result;
    }

    /**
     * Returns models that must be deleted from database.
     * @return ActiveRecord[] indexed by target's key.
     */
    public function getDeletedModels()
    {
        return $this->_deletedModels;
    }

    /**
     * Populates `$record` relations by owner active record according to [[self::$targetPopulateRelations]] config.
     * @param ActiveRecord $record
     * @throws InvalidParamException
     */
    protected function populateRelationsByOwner($record)
    {
        if (!$record instanceof ActiveRecord) {
            throw new InvalidParamException('Param $link must be an instance of ' . ActiveRecord::className() . '.');
        }
        $owner = $this->owner->owner;
        foreach ((array) $this->targetPopulateRelations as $relation) {
            $multiple = $record->getRelation($relation)->multiple;
            $record->populateRelation($relation, $multiple ? [$owner] : $owner);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveQuery
     * @throws InvalidCallException
     */
    protected function beforeModelsQuery($query)
    {
        if (!$query instanceof ActiveQuery) {
            throw new InvalidCallException('Param $query must be an instance of ' . ActiveQuery::className() . '.');
        }

        $event = new QueryEvent;
        $event->query = $query;
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
        foreach ($this->getDeletedModels() as $model) {
            if (!$model->getIsNewRecord() && $model->delete() === false) {
                $id = $model->{$this->targetKey};
                throw new InvalidValueException("Cannot delete $this->targetClass #$id record.");
            }
        }

        $ownerId = $this->owner->owner->{$this->ownerKey};
        $models = $this->getModels();
        $resultModels = [];

        if ($this->deleteAllBeforeSaving && $ownerInsertOperation !== true) {
            $models = $this->deleteAllBeforeSaving($models);
        }

        foreach ($models as $model) {
            $model->{$this->targetAttribute} = $ownerId;
            if (!$model->save(false)) {
                throw new InvalidValueException("Cannot save $this->targetClass for #$ownerId record.");
            }
            $resultModels[$model->{$this->targetKey}] = $model;
        }

        $this->_models = $resultModels;
        $this->_deletedModels = [];
    }

    /**
     * This method used if [[self::$deleteAllBeforeSaving]] property is true.
     * It deletes from DB all existing records and create new models without them.
     * Note: this method will replace old keys, because they will not have a sense.
     * @param ActiveRecord[] $models all models that you have to save in database (new and existing).
     * @return ActiveRecord[] new models created without `$models`
     * @throws InvalidValueException if model was not deleted.
     */
    protected function deleteAllBeforeSaving($models)
    {
        $newModels = [];
        foreach ($models as $model) {
            if ($model->isNewRecord) {
                $newModels[] = $model;
                continue;
            }

            $newModel = $this->createTargetModel();
            foreach ($model->safeAttributes() as $name) {
                $newModel->{$name} = $model->{$name};
            }
            if ($this->targetPositionAttribute !== null) {
                $newModel->{$this->targetPositionAttribute} = $model->{$this->targetPositionAttribute};
            }
            $newModels[] = $newModel;

            if ($model->delete() === false) {
                $id = $model->{$this->targetKey};
                throw new InvalidValueException("Cannot delete $this->targetClass #$id record.");
            }
        }
        return $newModels;
    }

    /**
     * @inheritdoc
     * @throw InvalidValueException
     */
    protected function deleteInternal($isOwnerDeletion = false)
    {
        if (!$isOwnerDeletion && $this->owner->owner->getIsNewRecord()) {
            $this->_models = $this->_deletedModels = [];
            return;
        }

        $dbModels = array_replace($this->_deletedModels, $this->getModels());
        /* @var $dbModels ActiveRecord[] */

        foreach ($dbModels as $model) {
            if (!$model->getIsNewRecord() && $model->delete() === false) {
                $id = $model->{$this->targetKey};
                throw new InvalidValueException("Cannot delete $this->targetClass #$id record.");
            }
        }

        $this->_models = $this->_deletedModels = [];
    }

    /**
     * @inheritdoc
     */
    public function validate($params = [])
    {
        $result = true;
        $attributeNames = ArrayHelper::getValue($params, 'attributeNames', null);
        $clearErrors = ArrayHelper::getValue($params, 'clearErrors', true);
        foreach ($this->getModels() as $model) {
            if (!$model->validate($attributeNames, $clearErrors)) {
                $result = false;
            }
        }

        if ($result && $uniqueFieldsCollection = ArrayHelper::getValue($params, 'uniqueFields', false)) {
            $result = $this->validateUniqueFields((array) $uniqueFieldsCollection);
        }

        if (!$result) {
            $errorMsg = Yii::t('app', 'You have errors in your "{attribute}" records.', [
                'attribute' => $this->owner->owner->getAttributeLabel($this->ownerAttribute),
            ]);
            if ($this->addModelsErrorsText) {
                foreach ($this->getModels() as $model) {
                    foreach ($model->getErrors() as $errors) {
                        foreach ($errors as $error) {
                            $errorMsg .= ' ' . $error;
                        }
                    }
                }
            }
            $this->owner->owner->addError($this->ownerAttribute, $errorMsg);
        }

        return $result;
    }

    /**
     * @param array $uniqueFieldsCollection
     * @return boolean
     * @throws InvalidParamException
     */
    protected function validateUniqueFields($uniqueFieldsCollection)
    {
        if (!is_array($uniqueFieldsCollection)) {
            throw new InvalidParamException('Pararm $uniqueFieldsCollection must be an array.');
        }

        foreach ($uniqueFieldsCollection as $uniqueFields) {
            $uniqueFields = (array) $uniqueFields;
            $caseSensitive = ArrayHelper::remove($uniqueFields, 'caseSensitive', false);

            if (empty($uniqueFields)) {
                throw new InvalidConfigException('Unique fields list in collection cannot be empty.');
            }
            $firstField = reset($uniqueFields);

            $existsValues = [];
            foreach ($this->getModels() as $model) {
                $values = [];
                foreach ($uniqueFields as $field) {
                    $values[$field] = $caseSensitive
                        ? (string) $model->{$field}
                        : StringHelper::strtolower($model->{$field});
                }
                $key = serialize($values);

                if (!isset($existsValues[$key])) {
                    $existsValues[$key] = $values;
                    continue;
                }

                $msgValues = [];
                foreach ($uniqueFields as $field) {
                    $msgValues[] = $model->getAttributeLabel($field) . ' "' . $model->{$field} . '"';
                }
                $model->addError($firstField, Yii::t('app', 'Duplicate entries: {value}.', [
                    'value' => implode(', ', $msgValues),
                ]));
                return false;
            }
        }

        return true;
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
        $result = call_user_func([$this->targetClass, 'getDb']);
        if (!$result instanceof Connection) {
            throw new InvalidValueException("Unexpected value of `$this->targetClass::getDb()` method, expected an instance of " . Connection::className() . ' as result.');
        }
        return $result;
    }
}
