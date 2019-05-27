<?php
namespace Youkok\Biz\Services\Download;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Common\Models\Element;
use Youkok\Helpers\ElementHelper;

class DownloadFileInfoService
{
    private $updateDownloadsService;

    public function __construct(UpdateDownloadsService $updateDownloadsService)
    {
        $this->updateDownloadsService = $updateDownloadsService;
    }

    public function fileExists(Element $element): bool
    {
        $filePath = $this->getFilePath($element);
        return file_exists($filePath) && is_readable($filePath);
    }

    public function getFileInfo(Element $element): string
    {
        $filePath = $this->getFilePath($element);
        $info = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);

        if ($info === false) {
            throw new ElementNotFoundException();
        }

        return $info;
    }

    public function getFileSize(Element $element): int
    {
        $filePath = $this->getFilePath($element);
        $size = filesize($filePath);

        if ($size === false) {
            throw new ElementNotFoundException();
        }

        return $size;
    }

    public function getFilePath(Element $element): string
    {
        return ElementHelper::getPhysicalFileLocation($element, getenv('FILE_DIRECTORY'));
    }
}
