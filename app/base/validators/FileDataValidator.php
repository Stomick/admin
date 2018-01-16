<?php

namespace app\base\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\validators\Validator;

/**
 * FileDataValidator
 */
class FileDataValidator extends Validator
{
    /**
     * @var string the name of files context manager.
     */
    public $contextManager = 'contextManager';

    /**
     * @var string the name of context.
     */
    public $context;

    /**
     * @var string
     */
    public $message;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->message === null) {
            $this->message = Yii::t('app', 'File "{attribute}" does not exist.');
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        switch (false) {
            case is_string($value); // no break
            case trim($value) !== ''; // no break
            case $this->_filesContext()->getStorage()->fileExists($value); // no break
                return [$this->message, []];
            default: // all conditions are true
                return null;
        }
    }

    /**
     * @return \flexibuild\file\ContextManager
     */
    private function _contextManager()
    {
        return Yii::$app->get($this->contextManager);
    }

    /**
     * @return \app\files\Context
     * @throws InvalidConfigException
     */
    private function _filesContext()
    {
        if ($this->context === null) {
            throw new InvalidConfigException('Property $context required in ' . static::className() . '.');
        }

        $manager = $this->_contextManager();
        return $manager->getContext($this->context);
    }
}
