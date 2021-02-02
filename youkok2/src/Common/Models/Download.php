<?php
namespace Youkok\Common\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int resource
 * @property string downloaded_time
 * @property string ip
 * @property string|null agent
 * @method static count()
 * @method static select(string|array ...$string)
 * @method static selectRaw(string $string)
 */
class Download extends Model
{
    protected $table = 'download';
    public $timestamps = false;
}
