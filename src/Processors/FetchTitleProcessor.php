<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;

class FetchTitleProcessor
{
    public static function fromUrl($url)
    {
        $url = rtrim(trim($url));

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return static::returnError();
        }

        $site_content = null;
        try {
            $site_content = @file_get_contents($_POST['url']);
        }
        catch (\Exception $e) {
            // The fuck
            return static::returnError();
        }

        if ($site_content === null and strlen($site_content) === 0) {
            return static::returnError();
        }

        preg_match("/\<title\>(.*)\<\/title\>/", $site_content, $title);

        if (count($title) > 0 and strlen($title[1]) > 0) {
            return [
                'title' => $title[1],
                'code' => 200
            ];
        }

        return static::returnError();
    }

    private static function returnError()
    {
        return [
            'code' => 400
        ];
    }
}
