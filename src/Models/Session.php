<?php
declare(strict_types=1);

namespace Youkok\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'session';
    public $timestamps = false;
}