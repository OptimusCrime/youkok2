<?php
namespace Youkok\Biz\Services\Download;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Common\Models\Element;
use Youkok\Helpers\Configuration\Configuration;

class DownloadFileInfoService
{
    private UpdateDownloadsService $updateDownloadsService;

    public function __construct(UpdateDownloadsService $updateDownloadsService)
    {
        $this->updateDownloadsService = $updateDownloadsService;
    }

    public function fileExists(Element $element): bool
    {
        $filePath = $this->getFilePath($element);
        return file_exists($filePath) && is_readable($filePath);
    }

    /**
     * @param Element $element
     * @return string
     * @throws ElementNotFoundException
     */
    public function getFileInfo(Element $element): string
    {
        $filePath = $this->getFilePath($element);
        $info = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);

        if ($info === false) {
            throw new ElementNotFoundException();
        }

        return $info;
    }

    /**
     * @param Element $element
     * @return int
     * @throws ElementNotFoundException
     */
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
        $checksum = $element->checksum;
        $folder1 = substr($checksum, 0, 1);
        $folder2 = substr($checksum, 1, 1);

        return Configuration::getInstance()->getDirectoryFiles()
            . $folder1
            . DIRECTORY_SEPARATOR
            . $folder2
            . DIRECTORY_SEPARATOR
            . $checksum;
    }
}
