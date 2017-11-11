<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;

class AutocompleteProcessor
{
    public static function run()
    {
        return ElementController::getAllCourses();
    }
}
