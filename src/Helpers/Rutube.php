<?php


namespace App\Tools\Editor\Helpers;


class Rutube
{
    /**
     * Ссыки могут быть видов:
     * https://rutube.ru/video/7edf90f050cd681c07414c1d716288bf/
     * https://rutube.ru/play/embed/11794667
     *
     * @param $url
     * @param string $default
     * @return string
     */
    static public function getVideoCode($url, $default = '')
    {
        $matches = [];
        if (preg_match(
                '%(https?://)?(www\.)?rutube\.ru/video/([a-zA-Z0-9_\-]+)[?]?.*%',
                $url,
                $matches
            ) && !empty($matches[3])) {
            return $matches[3];
        }

        $matches = [];
        if (preg_match(
                '%(https?://)?(www\.)?rutube\.ru/play/embed/([a-zA-Z0-9_\-]+)[?]?.*%',
                $url,
                $matches
            ) && !empty($matches[3])) {
            return $matches[3];
        }

        return $default;
    }

    static public function getPreviewImg($url)
    {
        return '';
    }

    static public function getVideoHtml($url, $width = 560, $height = 315)
    {
        $code = self::getVideoCode($url);
        if ($code) {
            return sprintf(
                '<iframe width="%s" height="%s" src="https://rutube.ru/play/embed/%s" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>',
                $width,
                $height,
                $code
            );
        }
        return '';
    }

    static public function getVideoHtmlForAmp($url, $width = 560, $height = 315)
    {
        $code = self::getVideoCode($url);
        if ($code) {
            return sprintf(
                '<amp-iframe width="%s" height="%s" src="https://rutube.ru/play/embed/%s" frameborder="0" allowfullscreen layout="responsive" sandbox="allow-scripts allow-same-origin allow-popups"></amp-iframe>',
                $width,
                $height,
                $code
            );
        }
        return '';
    }
}
