<?php
namespace Youkok\Common\Models;

use Carbon\Carbon;

use Youkok\Enums\MostPopularElement;

class Download extends BaseModel
{
    protected $table = 'download';
    public $timestamps = false;
}
