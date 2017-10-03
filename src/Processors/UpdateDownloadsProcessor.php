<?php
namespace Youkok\Processors;

use Youkok\Helpers\SessionHandler;
use Youkok\Models\Element;
use Youkok\Utilities\ArrayHelper;

class UpdateDownloadsProcessor extends AbstractElementFactoryProcessor
{
    public static function fromElement(Element $element)
    {
        return new UpdateDownloadsProcessor($element);
    }

    public function run()
    {
        static::addDownloadToElement($this->element);

        static::addDownloadToHistory($this->element, $this->sessionHandler);
    }

    private static function addDownloadToElement(Element $element)
    {
        $element->addDownload();
    }

    private static function addDownloadToHistory(Element $element, SessionHandler $sessionHandler)
    {
        $downloads = $sessionHandler->getDataWithKey('downloads');
        if ($downloads === null or empty($downloads)) {
            $downloads = [];
        }

        $newDownloads = ArrayHelper::addToArray($downloads, $element->id);
        $sessionHandler->setData('downloads', $newDownloads, SessionHandler::MODE_OVERWRITE);
    }
}
