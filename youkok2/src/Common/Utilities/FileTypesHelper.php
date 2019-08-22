<?php

namespace Youkok\Common\Utilities;

class FileTypesHelper
{
    public static function getValidFileTypes(): array
    {
        $types = [];

        $settingsTypes = explode(',', getenv('FILE_ENDINGS'));
        foreach ($settingsTypes as $type) {
            if (mb_strlen($type) > 0) {
                $types[] = $type;
            }
        }

        return $types;
    }
}
