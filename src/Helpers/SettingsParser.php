<?php
namespace Youkok\Helpers;

class SettingsParser
{
    private $settings;

    public function __construct()
    {
        $this->settings = [];
    }

    public function parse(array $files)
    {
        foreach ($files as $file) {
            $this->handleFile($file);
        }
    }

    private function handleFile($file)
    {
        if (file_exists($file) and is_readable($file)) {
            try {
                $newSettings = require $file;

                if ($newSettings === null or gettype($newSettings) !== 'array') {
                    return;
                }

                $this->settings = array_replace_recursive($this->settings, $newSettings);
            } catch (\Exception $e) {
                // Log error here
            }
        }

        // Log error here
    }

    public function getSettings()
    {
        return $this->settings;
    }
}
