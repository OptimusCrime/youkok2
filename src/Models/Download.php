<?php
namespace Youkok\Models;

use Carbon\Carbon;

use Youkok\Enums\MostPopularElement;

class Download extends BaseModel
{
    protected $table = 'download';
    public $timestamps = false;

    public static function getMostPopularElementQueryFromDelta($delta = MostPopularElement::ALL)
    {
        switch ($delta) {
            case MostPopularElement::TODAY:
                return Carbon::now()->subDay();
            case MostPopularElement::WEEK:
                return Carbon::now()->subWeek();
            case MostPopularElement::MONTH:
                return Carbon::now()->subMonth();
            case MostPopularElement::YEAR:
                return Carbon::now()->subYear();
            case MostPopularElement::ALL:
            default:
                return null;
        }
    }
}
