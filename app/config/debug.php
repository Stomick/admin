<?php

use yii\debug\Module as DebugModule;
use yii\gii\Module as GiiModule;
use yii\web\Response;

return function ($isConsole) {
    error_reporting(E_ALL);

    $config = [
        'components' => [
            'log' => [
                'traceLevel' => 10,
            ],
            'assetManager' => [
                'linkAssets' => true,
            ],
        ],
    ];

    $allowedIPs = [
        '127.0.0.1',
        '::1',
    ];

    if (!$isConsole) {
        $config['bootstrap'][] = 'debug';
        $config['modules']['debug'] = [
            'class' => DebugModule::class,
            'allowedIPs' => $allowedIPs,
        ];
    }

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => GiiModule::class,
        'allowedIPs' => $allowedIPs,
    ];

//    $changeResponseFormat2Html = function () {
//        Yii::$app->getResponse()->format = Response::FORMAT_HTML;
//    };
//    $config['modules']['debug']['on ' . DebugModule::EVENT_BEFORE_ACTION] = $changeResponseFormat2Html;
//    $config['modules']['gii']['on ' . GiiModule::EVENT_BEFORE_ACTION] = $changeResponseFormat2Html;

    return $config;
};
