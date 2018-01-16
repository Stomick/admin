<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(call_user_func(require(__DIR__ . '/debug.php'), false), [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'vZ347735609pBZ48uCpVOICr9UF0UsJQ',
        ],
    ],
]);
