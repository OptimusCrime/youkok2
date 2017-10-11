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
    }

    private static function addDownloadToElement(Element $element)
    {
        $element->addDownload();
    }
}
