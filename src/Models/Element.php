<?php
declare(strict_types=1);

namespace Youkok\Models;

use Illuminate\Database\Eloquent\Model;

use Youkok\Helpers\Utilities;

class Element extends Model
{
    public $timestamps = false;

    protected $table = 'element';
    protected $fillable = array('*');
    protected $guarded = array('');

    private $parentObject;
    private $parentRootObject;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->parentObject = null;
        $this->parentRootObject = null;
    }

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
                ->where('deleted', 0)
                ->where('pending', 0)
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

    private function getIcon(): string
    {
        if ($this->directory) {
            return 'folder.png';
        }

        if ($this->child === null) {
            return 'link.png';
        }

        if ($this->mime_type === null) {
            return 'unknown.png';
        }

        return $this->mime_type . '.png';
    }

    private function getParentObject()
    {
        if ($this->parent === null) {
            return null;
        }

        if ($this->parentObject !== null) {
            return $this->parentObject;
        }

        $this->parentObject = Element::select('id', 'name', 'parent', 'slug', 'uri', 'link')
            ->where('deleted', 0)
            ->where('pending', 0)
            ->where('id', $this->parent)
            ->first();

        return $this->parentObject;
    }

    private function getParentRootObj()
    {
        if ($this->parent === null) {
            return null;
        }

        if ($this->parentRootObject !== null) {
            return $this->parentRootObject;
        }

        $currentParent = $this->parent;

        while (true) {
            $this->parentRootObject = Element::select('id', 'name', 'parent', 'slug', 'uri', 'link')
                ->where('deleted', 0)
                ->where('pending', 0)
                ->where('id', $currentParent)
                ->first();

            $currentParent = $this->parentRootObject->parent;

            if ($currentParent === null) {
                break;
            }
        }

        return $this->parentRootObject;
    }

    private function getAddedPretty()
    {
        return Utilities::prettifySQLDate($this->added);
    }

    private function getAddedPrettyAll()
    {
        return Utilities::prettifySQLDate($this->added, false);
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

        if ($key === 'courseCode') {
            return $this->getCourseCode();
        }
        if ($key === 'courseName') {
            return $this->getCourseName();
        }
        if ($key === 'fullUri') {
            return $this->getFullUri();
        }
        if ($key === 'icon') {
            return $this->getIcon();
        }
        if ($key === 'parentObj') {
            return $this->getParentObject();
        }
        if ($key === 'parentRootObj') {
            return $this->getParentRootObj();
        }
        if ($key === 'addedPretty') {
            return $this->getAddedPretty();
        }
        if ($key === 'addedPrettyAll') {
            return $this->getAddedPrettyAll();
        }

        return null;
    }
}
