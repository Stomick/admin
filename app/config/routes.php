<?php

return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => true,
    'suffix' => '',
    'rules' => [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'sport-center',
            'prefix' => '',
            'extraPatterns' => [
                'GET list' => 'list',
                'GET sport-center' => 'sport-center',
                'POST upload-image' => 'upload-image',
                'POST upload-logo' => 'upload-logo',
                'GET change-confirmation-status' => 'change-confirmation-status',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'company-details',
            'prefix' => '',
            'extraPatterns' => [
                'POST upload-image' => 'upload-image',
                'POST upload-document' => 'upload-document',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'advantage',
            'prefix' => '',
            'extraPatterns' => [
                'POST upload-icon' => 'upload-icon',
                'GET test' => 'test'
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'playing-field',
            'prefix' => '',
            'extraPatterns' => [
                'GET list' => 'list',
                'GET create-route' => 'create-route',
                'DELETE delete-playfileds' => 'delete-playfileds'
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'booking',
            'prefix' => '',
            'extraPatterns' => [
                'GET my-bookings' => 'my-bookings',
                'GET my-booking' => 'my-booking',
                'GET list' => 'list',
                'POST create-booking' => 'create-booking',
                'POST payment' => 'payment',
                'GET schedule' => 'schedule',
                'POST block' => 'block',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'user',
            'prefix' => '',
            'extraPatterns' => [
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'admin',
            'prefix' => '',
            'extraPatterns' => [
                'GET list' => 'list',
                'GET change-role' => 'change-role'
            ],
        ],

        ['pattern' => 'redactor/upload-image', 'route' => 'redactor/upload-image'],
        ['pattern' => 'redactor/upload-file', 'route' => 'redactor/upload-file'],
        ['pattern' => 'auth/<action>', 'route' => 'auth/<action>'],
        ['pattern' => 'payment/<action>', 'route' => 'payment/<action>'],
    ],
];
