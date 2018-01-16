<?php


namespace app\files;

/**
 * Base FileSystemStorage class.
 * Main reason for creating this class is predefined folder properties for project structure.
 */
class FileSystemStorage extends \flexibuild\file\storages\FileSystemStorage
{
    /**
     * @inheritdoc
     */
    public $saveOriginNames = true;

    /**
     * @inheritdoc
     */
    public $dir = '@app/../prod/api/upload/files/{context}';
    /**
     * @inheritdoc
     */
    public $webPath = '@web/upload/files/{context}';
}
