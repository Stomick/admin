<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge([
    'files' => [
        'image.defaultMaxSize' => 10, // in mega bytes
        'image.defaultQuality' => 92, // in percent
    ],
    'adminEmail' => null,
    'apiToken' => 'Itvw42d3Grnz4U7FcPUrq2AAUIaRrRz7t7cMejRTG_HeqvrG-cWJTHlUN-fm-o4h',
], require(__DIR__ . '/params-' . YII_ENV . '.php'));
