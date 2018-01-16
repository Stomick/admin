<?php

namespace app\files;

use Yii;

/**
 * UploadAction
 */
class UploadAction extends \flexibuild\file\web\UploadAction
{
    /**
     * @inheritdoc
     */
    public $notUploadedMessage = null; // Yii::t('common', 'File was not uploaded.')

    /**
     * @inheritdoc
     */
    public $notSavedMessage = null; // Yii::t('common', 'File was not saved.')

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->notUploadedMessage === null) {
            $this->notUploadedMessage = Yii::t('app', 'File was not uploaded.');
        }
        if ($this->notSavedMessage === null) {
            $this->notSavedMessage = Yii::t('app', 'File was not saved.');
        }

        parent::init();
    }
}
