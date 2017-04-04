<?php
declare(strict_types=1);

namespace Youkok\Models;

use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    protected $table = 'element';
    public $timestamps = false;

    private function getCourseCode(): string
    {
        $courseArr = $this->getCourseArray();
        return $courseArr[0];
    }

    private function getCourseName(): string
    {
        $courseArr = $this->getCourseArray();

        if (count($courseArr) > 1) {
            return $courseArr[1];
        }

        return '';
    }

    private function getCourseArray(): array
    {
        if ($this->name == null) {
            return [''];
        }

        return explode('||', $this->name);
    }

    private function getFullUri(): string
    {
        if ($this->uri !== null and strlen($this->uri) > 0) {
            return $this->uri;
        }

        return $this->createUri();
    }

    public function isLink(): bool
    {
        return $this->link !== null and strlen($this->link) > 0;
    }

    private function createUri(): string
    {
        if ($this->isLink()) {
            return $this->link;
        }

        $fragments = [$this->slug];
        $currentParent = $this->parent;
        do {
            // Get the parent object
            $parent = Element::select('id', 'parent', 'slug', 'uri')
                ->find($currentParent);

            // If we have no valid parent object anyway we have no option but to quit (LOG ERROR)
            if ($parent === null) {
                break;
            }

            // If our parent object has their uri we can just reuse its uri
            if ($parent->uri !== null and strlen($parent->uri) > 0) {
                $fragments[] = $parent->uri;
                break;
            }

            // Just grab the slug and update parent
            $fragments[] = $parent->slug;
            $currentParent = $parent->parent;
        } while ($currentParent !== 0 and $currentParent !== null);

        // Filter the fragments
        $cleanFragments = self::cleanFragments($fragments);

        // Set the uri for this object
        $this->uri = implode('/', array_reverse($cleanFragments));
        $this->save();

        return $this->uri;
    }

    private static function cleanFragments(array $fragments): array
    {
        $clean = [];
        foreach ($fragments as $fragment) {
            if ($fragment !== null and strlen($fragment) > 0) {
                $clean[] = $fragment;
            }
        }

        return $clean;
    }

    public function __get($key)
    {
        $value = parent::__get($key);
        if ($value !== null) {
            return $value;
        }

        if ($key == 'courseCode') {
            return $this->getCourseCode();
        }
        if ($key == 'courseName') {
            return $this->getCourseName();
        }
        if ($key == 'fullUri') {
            return $this->getFullUri();
        }

        return null;
    }
}
