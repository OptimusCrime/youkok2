<?php
namespace Youkok\Models;

use Carbon\Carbon;

use Youkok\Helpers\Utilities;
use Youkok\Utilities\UriCleaner;

class Element extends BaseModel
{
    const ELEMENT_TYPE_DIRECTORIES = 0;
    const ELEMENT_TYPE_FILES = 1;
    const ELEMENT_TYPE_BOTH = 2;

    public $timestamps = false;

    protected $table = 'element';
    protected $fillable = array('*');
    protected $guarded = array('');

    private $parents;

    public static function fromId($id)
    {
        if (!isset($id) or !is_numeric($id)) {
            return null;
        }

        return Element::select('id', 'link', 'checksum')
            ->where('id', $id)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->first();
    }

    private static function handleElementType($element, $type)
    {
        switch($type) {
            case self::ELEMENT_TYPE_DIRECTORIES:
                $element->where('directory', 1);
                break;
            case self::ELEMENT_TYPE_FILES:
                $element->where('directory', 0);
                break;
            default:
                break;
        }

        return $element;
    }

    public static function fromUri($uri, $type = self::ELEMENT_TYPE_DIRECTORIES)
    {
        $query = Element::select('id', 'parent', 'name', 'checksum', 'link')
            ->where('uri', UriCleaner::clean($uri))
            ->where('deleted', 0)
            ->where('pending', 0);

        $query = static::handleElementType($query, $type);

        $element = $query->first();

        if ($element === null) {
            return self::fromUriFragments($uri, $type);
        }

        return $element;
    }

    public static function fromUriFragments($uri, $type = self::ELEMENT_TYPE_DIRECTORIES)
    {
        $fragments = UriCleaner::cleanFragments(explode('/', $uri));
        $parent = null;
        $element = null;

        foreach ($fragments as $fragment) {
            $query = Element::select('id', 'parent', 'name', 'checksum', 'link')
                ->where('slug', $fragment)
                ->where('parent', $parent)
                ->where('deleted', 0)
                ->where('pending', 0);

            $query = static::handleElementType($query, $type);

            $element = $query->first();

            if ($element === null) {
                return null;
            }

            $parent = $element->id;
        }

        return $element;
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->parents = null;
    }

    private function getCourseCode()
    {
        $courseArr = $this->getCourseArray();
        return $courseArr[0];
    }

    private function getCourseName()
    {
        $courseArr = $this->getCourseArray();

        if (count($courseArr) > 1) {
            return $courseArr[1];
        }

        return '';
    }

    private function getCourseArray()
    {
        if ($this->name == null) {
            return [''];
        }

        return explode('||', $this->name);
    }

    private function getFullUri()
    {
        if ($this->uri !== null and strlen($this->uri) > 0) {
            return $this->uri;
        }

        return $this->createUri();
    }

    public function isLink()
    {
        return $this->link !== null and strlen($this->link) > 0;
    }

    public function updateRootParent()
    {
        $rootParent = $this->getRootParent();
        if ($rootParent === null) {
            return null;
        }

        $rootParent->last_visited = Carbon::now();
        $rootParent->save();
    }

    private function getParents()
    {
        if ($this->parents !== null) {
            return $this->parents;
        }

        $parents = [$this];
        $currentObject = $this;
        while($currentObject->parent !== null and $currentObject->parent !== 0) {
            $currentObject = Element::select('id', 'name', 'slug', 'uri', 'parent')
                ->where('id', $currentObject->parent)
                ->where('deleted', 0)
                ->where('pending', 0)
                ->where('directory', 1)
                ->first();

            $parents[] = $currentObject;
        }

        $this->parents = array_reverse($parents);

        return $this->parents;
    }

    private function getRootParent()
    {
        $parents = $this->getParents();
        if ($parents === null or count($parents) === 0) {
            return null;
        }

        return $parents[0];
    }

    // Can be removed?
    private function createUri()
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
        $cleanFragments = UriCleaner::cleanFragments($fragments);

        // Set the uri for this object
        $this->uri = implode('/', array_reverse($cleanFragments));
        $this->save();

        return $this->uri;
    }

    private function getIcon()
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

    private function getAddedPretty()
    {
        return Utilities::prettifySQLDate($this->added);
    }

    private function getAddedPrettyAll()
    {
        return Utilities::prettifySQLDate($this->added, false);
    }

    public function addDownload()
    {
        $download = new Download();
        $download->resource = $this->id;
        $download->ip = $_SERVER['REMOTE_ADDR']; // TODO
        $download->agent = $_SERVER['HTTP_USER_AGENT']; // TODO
        $download->save();
    }

    public function __isset($name)
    {
        if (parent::__isset($name)) {
            return true;
        }

        return in_array($name, [
            'courseCode',
            'courseName',
            'fullUri',
            'icon',
            'parents',
            'rootParent',
            'addedPretty',
            'addedPrettyAll'
        ]);
    }

    public function __get($key)
    {
        $value = parent::__get($key);
        if ($value !== null) {
            return $value;
        }

        switch ($key) {
            case 'courseCode':
                return $this->getCourseCode();
            case 'courseName':
                return $this->getCourseName();
            case 'fullUri':
                return $this->getFullUri();
            case 'icon':
                return $this->getIcon();
            case 'parents':
                return $this->getParents();
            case 'rootParent':
                return $this->getRootParent();
            case 'addedPretty':
                return $this->getAddedPretty();
            case 'addedPrettyAll':
                return $this->getAddedPrettyAll();
            default:
                return null;
        }
    }
}
