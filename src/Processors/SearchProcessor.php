<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;
use Youkok\Enums\MostPopularElement;
use Youkok\Helpers\SessionHandler;

class SearchProcessor
{
    public static function run($query = null)
    {
        return ElementController::getElementsFromSearchQuery($query);
    }
}
