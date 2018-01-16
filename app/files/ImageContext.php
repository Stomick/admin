<?php

namespace app\files;

use yii\validators\ImageValidator;

/**
 * ImageContext uses [[FileSystemStorage]] as default storage.
 */
class ImageContext extends \flexibuild\file\contexts\ImageContext
{
    use DefaultStorageTrait;

    /**
     * @var array of image validator params.
     */
    public $validatorParams = [];

    /**
     * @inheritdoc
     */
    protected function defaultValidators()
    {
        return [
            array_merge([ImageValidator::className()], $this->validatorParams),
        ];
    }
}
