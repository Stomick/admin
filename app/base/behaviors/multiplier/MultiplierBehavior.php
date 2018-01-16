<?php

namespace app\base\behaviors\multiplier;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;

use app\base\db\ActiveRecord;

/**
 * MultiplierBehavior is used for linking active records.
 * It used linkers like many2many or one2many.
 *
 * @property ActiveRecord $owner
 */
class MultiplierBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        if (!$owner instanceof ActiveRecord) {
            throw new InvalidConfigException('Property $owner must be an instance of ' . ActiveRecord::className() . '.');
        }
        return parent::attach($owner);
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveDirtyLinks',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveDirtyLinks',
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteNeededLinks',
        ];
    }

    /**
     * Validates link ids.
     * This method may be used as inline validator in your model.
     * @param string $attribute
     * @param array $params
     * @return boolean whether the link has a valid ids or not.
     */
    public function validateLink($attribute, $params = [])
    {
        return $this->getLink($attribute)->validate((array) $params);
    }

    /**
     * Saves all dirty links.
     * @param Event|bool|null $event owner's save operation event.
     * Null (default) meaning opration is unknown. Boolean meaning whether there is
     * insert operation or update.
     */
    public function saveDirtyLinks($event = null)
    {
        $insert = null;
        if ($event instanceof Event) {
            if ($event->name === ActiveRecord::EVENT_AFTER_INSERT) {
                $insert = true;
            } elseif ($event->name === ActiveRecord::EVENT_AFTER_UPDATE) {
                $insert = false;
            }
        } elseif (is_bool($event)) {
            $insert = $event;
        }

        foreach ($this->_links as $data) {
            if ($data instanceof AbstractLinker) {
                $data->save($insert);
            }
        }
    }

    /**
     * Deletes links that has [[AbstractLinker::$deleteBeforeOwnerDeletion]] is true.
     * @param Event|bool|null $event owner's delete operation event.
     * Null (default) meaning opration is unknown. Boolean meaning whether there is
     * delete operation or not.
     */
    public function deleteNeededLinks($event = null)
    {
        $ownerDeletion = false;
        if ($event === true || ($event instanceof Event && $event->name === ActiveRecord::EVENT_BEFORE_DELETE)) {
            $ownerDeletion = true;
        }

        foreach ($this->getLinks() as $link) {
            if ($link->deleteBeforeOwnerDeletion) {
                $link->delete($ownerDeletion);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $this->hasLink($name) || parent::canGetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $this->hasLink($name) || parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (parent::canGetProperty($name, false) || !$this->hasLink($name)) {
            return parent::__get($name);
        }
        return $this->getLink($name)->getter();
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (parent::canSetProperty($name, false) || !$this->hasLink($name)) {
            return parent::__set($name, $value);
        }
        $this->getLink($name)->setter($value);
    }

    /**
     * @inheritdoc
     */
    public function __isset($name)
    {
        if (parent::canGetProperty($name, false) || !$this->hasLink($name)) {
            return parent::__isset($name);
        }
        return $this->getLink($name)->getter() !== null;
    }

    /**
     * @inheritdoc
     */
    public function __unset($name)
    {
        if (parent::canSetProperty($name, false) || !$this->hasLink($name)) {
            parent::__unset($name);
        } else {
            $this->getLink($name)->setter(null);
        }
    }

    /**
     * @var array
     */
    private $_links = [];

    /**
     * @return AbstractLinker[]
     */
    public function getLinks()
    {
        $result = [];
        foreach (array_keys($this->_links) as $name) {
            $result[$name] = $this->getLink($name);
        }
        return $result;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function hasLink($name)
    {
        return isset($this->_links[$name]);
    }

    /**
     * @param string $name
     * @return AbstractLinker
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function getLink($name)
    {
        if (!isset($this->_links[$name])) {
            throw new InvalidParamException("Link '$name' is not defined in " . __CLASS__ . '.');
        }

        $result = $this->_links[$name];
        if (!$result instanceof AbstractLinker) {
            $params = [
                'owner' => $this,
                'ownerAttribute' => $name,
            ];
            if (is_array($result)) {
                $result = array_merge($result, $params);
                $params = [];
            }
            $this->_links[$name] = $result = Yii::createObject($result, $params);
            if (!$result instanceof AbstractLinker) {
                throw new InvalidConfigException("Link '$name' is configured incorrectly. It must keep an instance or a config for creating an instance of " . AbstractLinker::className() . '.');
            }
        }

        return $result;
    }

    /**
     * @param array $links
     * @throws InvalidConfigException
     */
    public function setLinks($links)
    {
        if (!is_array($links)) {
            throw new InvalidConfigException('Property $links must be an array.');
        }
        $this->_links = $links;
    }
}
