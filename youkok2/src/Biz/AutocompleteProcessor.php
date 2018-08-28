<?php
namespace Youkok\Biz;

use Youkok\Controllers\ElementController;

class AutocompleteProcessor
{
    public static function run()
    {
        return ElementController::getAllCourses();
    }
}
