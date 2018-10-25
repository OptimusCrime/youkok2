<?php
namespace Youkok\Biz\Services\Download;

use Illuminate\Database\Capsule\Manager as DB;

use Youkok\Common\Models\Download;
use Youkok\Common\Models\Element;
use Youkok\Helpers\ElementHelper;

class DownloadFileInfoService
{
    private $updateDownloadsService;

    public function __construct(UpdateDownloadsService $updateDownloadsService)
    {
        $this->updateDownloadsService = $updateDownloadsService;
    }

    public function fileExists(Element $element)
    {
        $filePath = $this->getFilePath($element);
        return file_exists($filePath) && is_readable($filePath);
    }

    public function getFileContents(Element $element)
    {
        $filePath = $this->getFilePath($element);

        return fopen($filePath, 'r');
    }

    public function getFileInfo(Element $element)
    {
        $filePath = $this->getFilePath($element);
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
    }

    public function getFileSize(Element $element)
    {
        $filePath = $this->getFilePath($element);
        return filesize($filePath);
    }

    // TODO move this into the DownloadController
    public function getNumberOfDownloads()
    {
        return Download::count();
    }

    // TODO move this into the DownloadController
    public function getLatestDownloads($limit)
    {
        return DB::table('download')
            ->select(['downloaded_time', 'element.*'])
            ->leftJoin('element as element', 'element.id', '=', 'download.resource')
            ->orderBy('downloaded_time', 'DESC')
            ->limit($limit)
            ->get();
    }

    private function getFilePath(Element $element)
    {
        return ElementHelper::getPhysicalFileLocation($element, getenv('FILE_DIRECTORY'));
    }
}
