<?php

namespace app\base\helpers;

use Yii;

/**
 * SerializeHelper
 */
class SerializeHelper
{
    /**
     * Return array with file info for exporting in API.
     * @param \app\files\File $file
     * @param boolean $exportValue whether need to export file data value or not
     * @return array|null
     */
    public static function file2array($file, $exportValue = false)
    {
        if ($file->getIsEmpty()) {
            return null;
        }

        $origin = static::fixFileUrl($file->getUrl(null));
        $formats = [];
        foreach ($file->context->formatterNames() as $format) {
            $formats[$format] = static::fixFileUrl($file->getUrl($format));
        }

        return array_merge($exportValue ? [
            'value' => $file->getData(),
        ] : [], [
            'origin' => $origin,
            'formats' => $formats,
        ]);
    }

    /**
     * Fixes file url for outputing in api.
     * @staticvar string $protocol cached protocol value
     * @param string $url
     * @return string fixed url
     */
    public static function fixFileUrl($url)
    {
        static $protocol = null;
        if ($protocol === null) {
            $request = Yii::$app->getRequest();
            if ($request instanceof \yii\web\Request) {
                $protocol = $request->getIsSecureConnection() ? 'https' : 'http';
            } else {
                $protocol = 'http';
            }
        }

        if (is_string($url) && !strncmp($url, '//', 2)) {
            $url = "$protocol:$url";
        }
        return $url;
    }
}
