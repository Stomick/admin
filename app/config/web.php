<?php

use app\base\web\JsonResponseFormatter;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\JsonParser;
use yii\web\Response;

return ArrayHelper::merge(require(__DIR__ . '/common.php'), [
    'id' => 'letmesport',
    'bootstrap' => [
        function () {
            \Yii::$container->set('yii\rest\Serializer', ['collectionEnvelope' => 'items']);
        },
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'response' => [
            'format' => Response::FORMAT_JSON,
            'formatters' => [
                Response::FORMAT_JSON => JsonResponseFormatter::class,
            ],
        ],
        'urlManager' => require(__DIR__ . '/routes.php'),
        'user' => [
            'enableAutoLogin' => false,
            'enableSession' => false,
            'identityClass' => User::class,
            'loginUrl' => null,
        ],
        'contextManager' => require(__DIR__ . DIRECTORY_SEPARATOR . 'file-contexts.php'),
    ],
], require(__DIR__ . '/web-' . YII_ENV . '.php'));
