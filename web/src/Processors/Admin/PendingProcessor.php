<?php
namespace Youkok\Processors\Admin;

use Youkok\Models\Element;

class PendingProcessor
{
    public static function run()
    {
        return Element::where('pending', 1)->count();
    }
}
