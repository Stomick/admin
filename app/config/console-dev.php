<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(call_user_func(require(__DIR__ . '/debug.php'), true), [

]);
