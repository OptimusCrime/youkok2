<?php
declare(strict_types=1);

namespace Youkok\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'message';
    public $timestamps = false;
}
