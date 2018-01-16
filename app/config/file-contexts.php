<?php

return function () {
    $maxSize = Yii::$app->params['files']['image.defaultMaxSize'] * 1024 * 1024;
    $defaultQuality = Yii::$app->params['files']['image.defaultQuality'];

    $createFormat = function (array $params, $quality = null) use ($defaultQuality) {
        if (!isset($params['saveOptions']['quality'])) {
            $params['saveOptions']['quality'] = $quality === null ? $defaultQuality : $quality;
        }
        if (!isset($params['imagineConfig'])) {
            $params['imagineConfig'] = 'gd2';
        }
        return $params;
    };

    return Yii::createObject([
        'class' => flexibuild\file\ContextManager::class,
        'contexts' => [
            'sport-center-image' => [
                'class' => 'app\files\ImageContext',
                'validatorParams' => [
                    'maxSize' => $maxSize,
                ],
                'formatters' => [
                    'thumb' => $createFormat(['image/thumb', 'height' => 23], 100 /* quality */),
                    'preview' => $createFormat(['image/thumb', 'height' => 200], 100 /* quality */),
                ],
                'generateFormatsAfterSave' => true,
                'fileClass' => 'app\models\files\SportCentersImage',
                'fileConfig' => [
                    'defaultUrls' => [
                        '/assets/img/placeground-empty.png',
                        'medium' => '/assets/img/placeground-empty.png',
                    ],
                    'on cannotGetUrl' => 'flexibuild\file\events\CannotGetUrlHandlers::formatFileOnFly',
                ],
            ],
            'sport-center-logo' => [
                'class' => 'app\files\ImageContext',
                'validatorParams' => [
                    'maxSize' => $maxSize,
                ],
                'formatters' => [
                    'thumb' => $createFormat(['image/thumb', 'height' => 23], 100 /* quality */),
                    'preview' => $createFormat(['image/thumb', 'height' => 100], 100 /* quality */),
                ],
                'generateFormatsAfterSave' => true,
                'fileClass' => 'app\models\files\SportCenterLogoFile',
                'fileConfig' => [
                    'on cannotGetUrl' => 'flexibuild\file\events\CannotGetUrlHandlers::formatFileOnFly',
                ],
            ],
            'advantage-icon' => [
                'class' => 'app\files\ImageContext',
                'validatorParams' => [
                    'maxSize' => $maxSize,
                ],
                'formatters' => [
                    'thumb' => $createFormat(['image/thumb', 'height' => 23], 100 /* quality */),
                    'preview' => $createFormat(['image/thumb', 'height' => 100], 100 /* quality */),
                ],
                'generateFormatsAfterSave' => true,
                'fileClass' => 'app\models\files\AdvantageIconFile',
                'fileConfig' => [
                    'on cannotGetUrl' => 'flexibuild\file\events\CannotGetUrlHandlers::formatFileOnFly',
                ],
            ],
        ],
    ]);
};
