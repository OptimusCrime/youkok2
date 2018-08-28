<?php
namespace Youkok\Common\Utilities;

class EnvParser
{
    public static function parse($path, $files = ['default'])
    {
        foreach ($files as $file) {
            $fullFilePath = $path . $file;
            $content = static::readFile($fullFilePath);
            if ($content === false or $content === null or strlen($content) === 0) {
                continue;
            }

            static::parseContent($content);
        }
    }

    private static function readFile($file) {
        if (!file_exists($file)) {
            return false;
        }

        return file_get_contents($file);
    }

    private static function parseEnvLine($line) {
        $REGEX_PATTERN = '/^export (?P<key>.*)=(?P<value>.*)$/';

        $line = trim($line);
        if (strlen($line) === 0) {
            return false;
        }

        if (!substr($line, 0, 6) === 'export') {
            return false;
        }

        preg_match_all($REGEX_PATTERN, $line, $matches, PREG_SET_ORDER, 0);
        if (!isset($matches[0]) or !isset($matches[0]['key']) or !isset($matches[0]['value'])) {
            return false;
        }

        if (strlen($matches[0]['key']) === 0 or strlen($matches[0]['value']) === 0) {
            return false;
        }

        putenv($matches[0]['key'] . '=' . $matches[0]['value']);

        return true;
    }

    private static function parseContent($content) {
        $lines = explode(PHP_EOL, $content);
        foreach ($lines as $line) {
            static::parseEnvLine($line);
        }

        return true;
    }
}