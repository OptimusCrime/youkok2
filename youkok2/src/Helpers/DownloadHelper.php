<?php
namespace Youkok\Helpers;

use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\Stream;

use Youkok\Models\Element;

class DownloadHelper
{
    public static function render(Response $response, Element $element, array $settings)
    {
        if (!ElementHelper::fileExists($element, $settings['file_directory'])) {
            return null;
        }

        return static::downloadElement($response, $element, $settings['file_directory']);
    }

    private function downloadElement(Response $response, Element $element, $directory)
    {
        $filePath = ElementHelper::getPhysicalFileLocation($element, $directory);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileInfo = finfo_file($finfo, $filePath);
        $fd = fopen($filePath, "r");

        if ($fd === false or $fd === null) {
            return null;
        }

        return $response
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Type', $fileInfo)
            ->withHeader('Content-Disposition', 'inline; filename="' . $element->name . '"')
            //->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate')
            ->withHeader('Pragm', 'public')
            ->withHeader('Content-Length', filesize($filePath))
            ->withBody(new Stream($fd));
    }
}
