<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge([
    'name' => 'LetMeSport',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'UTC',
    'bootstrap' => [
        'log',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'itemFile' => '@app/rbac/items.php',
            'assignmentFile' => '@app/rbac/assignments.php',
            'ruleFile' => '@app/rbac/rules.php',
            'defaultRoles' => [
                'user',
                'admin',
                'super-admin',
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,//set this property to false to send mails to real email addresses
            //comment the following array to send mail using php's mail function
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'info@weev.ru',
                'password' => 'WeevWeev2018',
                'port' => '587',
                'encryption' => 'tls',
            ],
            'viewPath' => '@app/mail',
            'htmlLayout' => 'layouts/html.sphp',
            'textLayout' => 'layouts/text.php',
        ],
    ],
    'params' => require(__DIR__ . '/params.php'),
], require(__DIR__ . '/common-' . YII_ENV . '.php'));
