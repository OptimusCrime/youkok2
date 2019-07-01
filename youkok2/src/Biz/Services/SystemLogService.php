<?php
namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\GenericYoukokException;

class SystemLogService
{
    const PHP_LOG = '/var/log/youkok2/php.log';
    const ERROR_LOG = '/var/log/youkok2/error.log';

    public function fetch($log): string
    {
        $content = file_get_contents($log);

        if ($content === false) {
            throw new GenericYoukokException("Could not load log " . $log);
        }

        return static::rotateLog(file_get_contents($log));
    }

    private static function rotateLog(string $content): string
    {
        $splitContent = explode(PHP_EOL, $content);
        return implode(PHP_EOL, array_reverse($splitContent));
    }
}
