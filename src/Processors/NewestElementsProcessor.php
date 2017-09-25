<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;

class NewestElementsProcessor
{
    public static function run()
    {
        return ElementController::getLatest(5);
    }
}
