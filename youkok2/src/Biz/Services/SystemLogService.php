<?php
namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\GenericYoukokException;

class SystemLogService
{
    public function fetch(): string
    {
        $content = @file_get_contents(getenv('LOGS_DIRECTORY') . getenv('LOGGER_FILE'));

        if ($content === false) {
            throw new GenericYoukokException("Could not load log.");
        }

        return $content;
    }
}
