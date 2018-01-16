<?php

namespace app\files;

/**
 * DefaultStorageTrait uses [[FileSystemStorage]] as default storage.
 *
 * @property FileSystemStorage $storage Storage for keeping files.
 * @method FileSystemStorage getStorage() Storage for keeping files.
 */
trait DefaultStorageTrait
{
    /**
     * @inheritdoc
     */
    protected function defaultStorage()
    {
        return FileSystemStorage::className();
    }
}
