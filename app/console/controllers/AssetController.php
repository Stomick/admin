<?php

namespace app\console\controllers;

use Yii;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\console\Exception;


/**
 * AssetController
 */
class AssetController extends \yii\console\controllers\AssetController
{
    /**
     * @var array array of assets directories.
     */
    public $assetsDirs = [
        '@webroot/assets',
    ];

    /**
     * Clears all caches and assets.
     */
    public function actionClear()
    {
        $this->stdout("\nThe following assets directories were cleared:\n\n", Console::FG_YELLOW);
        foreach ($this->assetsDirs as $dir) {
            $this->stdout("\t* $dir\n", Console::FG_GREEN);

            $realPath = Yii::getAlias($dir);
            if (!($handle = opendir($realPath))) {
                throw new Exception("Cannot read directory: $realPath");
            }

            try {
                while (false !== $file = readdir($handle)) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $subdir = rtrim($realPath, '\/') . DIRECTORY_SEPARATOR . $file;
                    if (!is_dir($subdir)) {
                        continue;
                    }
                    FileHelper::removeDirectory($subdir);
                }
            } catch (\Exception $ex) {
                closedir($handle);
                throw $ex;
            }
            closedir($handle);
        }
        $this->stdout("\n");
    }
}
