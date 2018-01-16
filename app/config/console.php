<?php

use app\console\controllers\AssetController;
use app\console\controllers\MigrateController;
use yii\helpers\ArrayHelper;

return ArrayHelper::merge(require(__DIR__ . '/common.php'), [
    'id' => 'letmesport-console',
    'language' => 'en-US',
    'bootstrap' => [
        'initWebAssets' => function () {
            Yii::setAlias('@webroot', __DIR__ . '/../prod/api');
        },
    ],
    'controllerNamespace' => 'app\console\controllers',
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
        ],
        'asset' => [
            'class' => AssetController::class,
        ],
    ],
], require(__DIR__ . '/console-' . YII_ENV . '.php'));
