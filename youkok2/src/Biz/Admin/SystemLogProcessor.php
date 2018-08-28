<?php
namespace Youkok\Biz\Admin;

class SystemLogProcessor
{
    const PHP_LOG = '/var/log/youkok2/php.log';
    const ERROR_LOG = '/var/log/youkok2/error.log';

    public static function fetch($log)
    {
        return static::rotateLog(file_get_contents($log));
    }

    private static function rotateLog($content)
    {
        $splitContent = explode(PHP_EOL, $content);
        return implode(PHP_EOL, array_reverse($splitContent));
    }
}