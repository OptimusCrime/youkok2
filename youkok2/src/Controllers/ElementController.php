<?php
namespace Youkok\Controllers;

use Illuminate\Database\Capsule\Manager as DB;

use Youkok\Models\Download;
use Youkok\Models\Element;

class ElementController
{
    const SORT_TYPE_ORGANIZED = 0;
    const SORT_TYPE_AGE = 1;

    // TODO add parameter to fetch whatever attributes we'd like for all methods
    // builder pattern?

    public static function getAllPending()
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'deleted', 'directory')
            ->where('pending', 1)
            ->where('deleted', 0)
            ->whereNotNull('parent')
            ->orderBy('name')
            ->get();
    }

    public static function getElementsFromSearch($search = null)
    {
        if ($search === null or count($search) === 0) {
            return [];
        }

        $query = Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0);

        $query->where(function ($query) use ($search) {
            foreach ($search as $v) {
                $query->orWhere('name', 'LIKE', $v);
            }
        });

        $query->orderBy('empty');
        $query->orderBy('name');

        return $query->get();
    }

    public static function getAllCourses()
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('name')
            ->get();
    }

    public static function getAllNoneEmptyCourses()
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'deleted', 'pending')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('empty', 0)
            ->orderBy('name')
            ->get();
    }

    public static function getLastVisitedCourses($limit = 10)
    {
        return Element::select('id', 'name', 'slug', 'uri', 'last_visited')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('last_visited', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function getLatest($limit = 10)
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'added', 'checksum')
            ->where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function getAllElements($columns = ['id', 'parent'])
    {
        return Element::select($columns)->get();
    }

    public static function getAllChildren($id)
    {
        return Element::select([
            'id', 'name', 'slug', 'uri', 'parent', 'empty',
            'directory', 'link', 'checksum',
            'added', 'deleted'
        ])
            ->where('parent', $id)
            ->where('pending', 0)
            ->orderBy('deleted', 'ASC')
            ->orderBy('directory', 'DESC')
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getVisibleChildren($id, $order = self::SORT_TYPE_ORGANIZED)
    {
        $query = Element::select([
            'id', 'name', 'slug', 'uri', 'parent', 'empty',
            'directory', 'link',
            'checksum', 'added'
        ])
            ->where('parent', $id)
            ->where('deleted', 0)
            ->where('pending', 0);

        if ($order === static::SORT_TYPE_ORGANIZED) {
            $query = $query->orderBy('directory', 'DESC')->orderBy('name', 'ASC');
        } else {
            $query = $query->orderBy('added', 'DESC');
        }

        return $query->get();
    }

    // TODO
    public static function getDownloadsForElement(Element $element, $foo = 1)
    {
        return Download::select(DB::raw("COUNT(`id`) as `result`"))
            ->where('resource', $element->id)
            ->count();
    }
}
