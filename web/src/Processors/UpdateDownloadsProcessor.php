<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;
use Youkok\Helpers\SessionHandler;
use Youkok\Models\Element;
use Youkok\Utilities\ArrayHelper;
use Youkok\Utilities\CacheKeyGenerator;

class UpdateDownloadsProcessor extends AbstractElementFactoryProcessor
{
    public static function fromElement(Element $element)
    {
        return new UpdateDownloadsProcessor($element);
    }

    public function run()
    {
        static::addDownloadToElement($this->element);
        static::addDownloadToCache($this->cache, $this->element);
    }

    private static function addDownloadToElement(Element $element)
    {
        $element->addDownload();
    }

    private static function addDownloadToCache($cache, Element $element)
    {
        if ($cache === null) {
            return;
        }

        $key = CacheKeyGenerator::keyForElementDownloads($element->id);
        $downloads = $cache->get($key);
        if ($downloads === false) {
            $existingDownloads = ElementController::getDownloadsForElement($element);
            if ($existingDownloads === null) {
                $existingDownloads = 0;
            }
            $cache->set($key, $existingDownloads);
            return;
        }

        $cache->incr($key);
    }
}
