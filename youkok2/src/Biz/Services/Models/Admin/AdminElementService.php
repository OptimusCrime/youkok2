<?php
namespace Youkok\Biz\Services\Models\Admin;

use Illuminate\Database\Eloquent\Collection;
use Youkok\Common\Models\Element;

class AdminElementService
{
    public function getAllChildren(int $id): Collection
    {
        return Element
            ::where('parent', $id)
            ->get();
    }
}
