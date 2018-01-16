<?php

namespace app\files;

use yii\validators\FileValidator;

/**
 * Context uses [[FileSystemStorage]] as default storage.
 */
class Context extends \flexibuild\file\contexts\Context
{
    use DefaultStorageTrait;

    /**
     * @var array of file validator params.
     */
    public $validatorParams = [];

    /**
     * @inheritdoc
     */
    protected function defaultValidators()
    {
        return [
            array_merge([FileValidator::className()], $this->validatorParams),
        ];
    }
}
