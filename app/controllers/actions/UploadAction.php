<?php

namespace app\controllers\actions;

/**
 * UploadAction is used for uploading files through API.
 */
class UploadAction extends \app\files\UploadAction
{
    /**
     * @inheritdoc
     */
    public $postName = 'file';

    /**
     * @inheritdoc
     */
    public $urlsScheme = 'http';
}
