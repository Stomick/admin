<?php

namespace app\base\db;

use yii\base\UnknownClassException;

/**
 * Base project ActiveRecord class
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @var boolean whether need to use transaction by default or not.
     * @see [[self::isTransactional()]]
     */
    public $useTransactionsByDefault = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initAttributes();
        parent::init();
    }

    /**
     * Initializes attributes by default values.
     */
    protected function initAttributes()
    {
        $this->loadDefaultValues();
    }

    /**
     * @inheritdoc
     * @return ActiveQuery
     */
    public static function find()
    {
        return new ActiveQuery(get_called_class());
    }


    /**
     * Returns a value indicating whether the specified operation is transactional in the current [[scenario]].
     * If scenario is not defined in [[self::transactions()]] method is transactional by default.
     * @param integer $operation the operation to check. Possible values are [[OP_INSERT]], [[OP_UPDATE]] and [[OP_DELETE]].
     * @return boolean whether the specified operation is transactional in the current [[scenario]].
     */
    public function isTransactional($operation)
    {
        if (!$this->useTransactionsByDefault) {
            return parent::isTransactional($operation);
        }

        $scenario = $this->getScenario();
        $transactions = $this->transactions();

        if (isset($transactions[$scenario]) || array_key_exists($scenario, $transactions)) {
            if (!$transactions[$scenario]) {
                return false;
            }
            return (bool) $transactions[$scenario] & $operation;
        }
        return true;
    }

    /**
     * Searches the highest (in namespaces sense) class name
     * of this model according to `$className`
     * @param string $className the name of class.
     * @return string the highest existing class name of this model according to `$className`.
     */
    public static function searchHighestClass($className)
    {
        $shortClassName = trim($className, '\\');
        if (false !== $pos = strrpos($className, '\\')) {
            $shortClassName = substr($className, $pos + 1);
        }

        $notValidParents = [
            trim(__CLASS__, '\\'),
            trim(get_parent_class(__CLASS__), '\\'),
        ];

        $checkClass = trim(get_called_class(), '\\');
        do {
            $namespace = '';
            if (false !== $pos = strrpos($checkClass, '\\')) {
                $namespace = substr($checkClass, 0, $pos);
            }

            $result = trim("$namespace\\$shortClassName", '\\');
            try {
                if (!class_exists($result)) {
                    $result = null;
                }
            } catch (UnknownClassException $ex) {
                $result = null;
            }

            $checkClass = trim(get_parent_class($checkClass), '\\');
        } while ($result === null && !in_array($checkClass, $notValidParents, true));

        if ($result === null) {
            return $className;
        } else {
            return $result;
        }
    }

    /**
     * @inheritdoc
     * @param boolean $searchHighestClass whether need to try to find the highest name of class.
     * @return ActiveQuery the active query object.
     */
    public function hasOne($class, $link, $searchHighestClass = true)
    {
        if ($searchHighestClass) {
            $class = static::searchHighestClass($class);
        }
        return parent::hasOne($class, $link);
    }

    /**
     * @inheritdoc
     * @param boolean $searchHighestClass whether need to try to find the highest name of class.
     * @return ActiveQuery the active query object.
     */
    public function hasMany($class, $link, $searchHighestClass = true)
    {
        if ($searchHighestClass) {
            $class = static::searchHighestClass($class);
        }
        return parent::hasMany($class, $link);
    }
}
