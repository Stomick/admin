<?php

namespace app\base\behaviors\multiplier;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\base\NotSupportedException;
use yii\db\Connection;

use app\base\db\ActiveRecord;

/**
 * AbstractLinker is the base class for linkers like as [[Many2Many]] or [[One2Many]].
 *
 * @property-read string $ownerKey the name of owner's primary key.
 * @property-read Connection $db
 */
abstract class AbstractLinker extends Component
{
    /**
     * @var MultiplierBehavior
     */
    public $owner;

    /**
     * @var string the name of owner's attribute, that implemented by this linker.
     */
    public $ownerAttribute;

    /**
     * @var boolean|null whether need to use transaction for saving/deleting models.
     * Null meaning transaction will be used if connection not in transaction state
     * now, or current owner's operation is not transactional and
     * value from [[\yii\db\Connection::$enableSavepoint]] is true.
     */
    public $useTransaction = null;

    /**
     * @var boolean whether the linked records must be deleted before owner delete operation.
     * Default is false because there is meant developer will be use cascade deletion.
     */
    public $deleteBeforeOwnerDeletion = false;

    /**
     * Validates current linked data.
     * @param array $params additional params passed to [[\yii\validate\InlineValidator]].
     * @return boolean whether the all linked data is valid.
     */
    abstract public function validate($params = []);

    /**
     * Saves current linked data.
     * @param boolean|null $ownerInsertOperation whether the owner has insert or
     * update operation right now. Null (default) meaning operation is unknown.
     */
    abstract protected function saveInternal($ownerInsertOperation = null);

    /**
     * Deletes current linked data.
     * @param boolean $isOwnerDeletion whether the owner has delete operation right now.
     * False (default) meaning delete method of the linker was called manually.
     */
    abstract protected function deleteInternal($isOwnerDeletion = false);

    /**
     * This method used in magic getter of [[MultipleBehavior]].
     * @return mixed value that will be returned in [[MultipleBehavior::__get()]].
     */
    abstract public function getter();

    /**
     * This method used in magic setter of [[MultipleBehavior]].
     * @param mixed $value value that was passed in [[MultipleBehavior::__set()]].
     */
    abstract public function setter($value);

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->checkConfig();
    }

    /**
     * @throws InvalidConfigException
     */
    protected function checkConfig()
    {
        if (!$this->owner instanceof MultiplierBehavior) {
            throw new InvalidConfigException('Property $owner must be an instance of ' . MultiplierBehavior::className() . '.');
        } elseif ($this->ownerAttribute === null) {
            throw new InvalidConfigException('Property $ownerAttribute is required.');
        }
    }

    /**
     * Indicates whether need to use transaction according to [[self::$useTransaction]] property.
     * @param integer|null $operation owner's operation. Null meaning operation is unknown.
     * @return boolean whether need to use transaction or not.
     */
    public function isTransactional($operation = null)
    {
        if ($this->useTransaction !== null) {
            return (bool) $this->useTransaction;
        }

        $db = $this->getDb();
        if (!$db->getTransaction()) {
            return true;
        } elseif (!$db->enableSavepoint) {
            return false;
        } elseif ($operation !== null) {
            return !$this->owner->owner->isTransactional($operation);
        } else {
            return true;
        }
    }

    /**
     * Saves current linked data.
     * @param boolean|null $ownerInsertOperation whether the owner has insert or
     * update operation right now. Null (default) meaning operation is unknown.
     */
    public function save($ownerInsertOperation = null)
    {
        if ($ownerInsertOperation !== null) {
            $operation = $ownerInsertOperation ? ActiveRecord::OP_INSERT : ActiveRecord::OP_UPDATE;
        } else {
            $operation = null;
        }

        if (!$this->isTransactional($operation)) {
            return $this->saveInternal($ownerInsertOperation);
        }

        $db = $this->getDb();
        $transaction = $db->beginTransaction();
        try {
            $result = $this->saveInternal($ownerInsertOperation);
            $transaction->commit();
        } catch (\Exception $ex) {
            if ($transaction->getIsActive()) {
                $transaction->rollBack();
            }
            throw $ex;
        }

        return $result;
    }

    /**
     * Deletes current linked data.
     * @param boolean $isOwnerDeletion whether the owner has delete operation right now.
     * False (default) meaning delete method of the linker was called manually.
     */
    public function delete($isOwnerDeletion = false)
    {
        $operation = $isOwnerDeletion ? ActiveRecord::OP_DELETE : null;
        if (!$this->isTransactional($operation)) {
            return $this->deleteInternal($isOwnerDeletion);
        }

        $db = $this->getDb();
        $transaction = $db->beginTransaction();
        try {
            $result = $this->deleteInternal($isOwnerDeletion);
            $transaction->commit();
        } catch (\Exception $ex) {
            if ($transaction->getIsActive()) {
                $transaction->rollBack();
            }
            throw $ex;
        }

        return $result;
    }

    /**
     * @return string the name of owner's primary key.
     * @throws InvalidValueException
     * @throws NotSupportedException
     */
    public function getOwnerKey()
    {
        $keys = $this->owner->owner->primaryKey();
        if (!is_array($keys)) {
            throw new InvalidValueException('Invalid value was returned by ' . get_class($this->owner->owner) . '::primaryKey() method.');
        } elseif (count($keys) !== 1) {
            throw new NotSupportedException(static::className() . ' works with single primary keys only.');
        }
        return reset($keys);
    }

    /**
     * You must use connection of class, with that you manipulate in save method.
     * @return Connection
     */
    public function getDb()
    {
        return Yii::$app->getDb();
    }
}
