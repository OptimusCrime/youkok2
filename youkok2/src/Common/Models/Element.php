<?php
namespace Youkok\Common\Models;

use Carbon\Carbon;

use Youkok\Helpers\ElementHelper;
use Youkok\Helpers\Utilities;
use Youkok\Common\Utilities\UriCleaner;

class Element extends BaseModel
{
    const LINK = 'LINK';
    const COURSE = 'COURSE';
    const DIRECTORY = 'DIRECTORY';
    const FILE = 'FILE';

    const ELEMENT_TYPE_DIRECTORIES = 0;
    const ELEMENT_TYPE_FILES = 1;
    const ELEMENT_TYPE_BOTH = 2;
    const ELEMENT_TYPE_FILE_LAST = 3;

    public $timestamps = false;

    protected $table = 'element';
    protected $fillable = ['*'];
    protected $guarded = [''];

    private $parents;
    private $childrenObjects;

    public static function fromIdVisible($id, $attributes = ['id', 'link', 'checksum'])
    {
        if (!isset($id) or !is_numeric($id)) {
            return null;
        }

        return Element::select($attributes)
            ->where('id', $id)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->first();
    }

    public static function fromIdAll($id, $attributes = ['id', 'link', 'checksum'])
    {
        if (!isset($id) or !is_numeric($id)) {
            return null;
        }

        return Element::select($attributes)
            ->where('id', $id)
            ->first();
    }

    public static function fromUriFileVisible($uri, $attributes = ['id', 'parent', 'name', 'checksum', 'link', 'directory'])
    {
        return self::fromUriFragments($uri, $attributes, self::ELEMENT_TYPE_FILE_LAST);
    }

    private static function fromUriFragments($uri, $attributes = ['id', 'parent', 'name', 'checksum', 'link', 'directory'], $type = self::ELEMENT_TYPE_DIRECTORIES)
    {
        $fragments = UriCleaner::cleanFragments(explode('/', $uri));
        $parent = null;
        $element = null;

        foreach ($fragments as $index => $fragment) {
            $query = Element::select($attributes)
                ->where('slug', $fragment)
                ->where('parent', $parent)
                ->where('deleted', 0)
                ->where('pending', 0);

            if ($type === static::ELEMENT_TYPE_FILE_LAST) {
                if ($index === count($fragments) - 1) {
                    $query = static::handleElementType($query, Element::ELEMENT_TYPE_FILES);
                }
                else {
                    $query = static::handleElementType($query, Element::ELEMENT_TYPE_DIRECTORIES);
                }
            }
            else {
                $query = static::handleElementType($query, $type);
            }

            $element = $query->first();

            if ($element === null) {
                return null;
            }

            $parent = $element->id;
        }

        return $element;
    }

    private static function handleElementType($element, $type)
    {
        switch ($type) {
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

        return ElementHelper::constructUri($this->id);
    }

    public function isLink()
    {
        return $this->link !== null && strlen($this->link) > 0;
    }

    public function isCourse()
    {
        return $this->parent === null && $this->directory === 1;
    }

    public function isDirectory()
    {
        return !$this->isCourse() && $this->directory === 1;
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

    // TODO add parameter to fetch whatever attributes we'd like
    private function getParents($all = false)
    {
        if ($this->parents !== null) {
            return $this->parents;
        }

        $parents = [$this];
        $currentObject = $this;
        while ($currentObject->parent !== null and $currentObject->parent !== 0) {
            $currentObject = Element::select('id', 'name', 'slug', 'uri', 'parent', 'directory')
                ->where('id', $currentObject->parent);

            if (!$all) {
                $currentObject = $currentObject
                    ->where('deleted', 0)
                    ->where('pending', 0)
                    ->where('directory', 1);
            }

            $currentObject = $currentObject->first();

            $parents[] = $currentObject;
        }

        $this->parents = array_reverse($parents);

        return $this->parents;
    }

    // TODO add parameter to fetch whatever attributes we'd like
    private function getParentObj()
    {
        $parents = $this->parents;
        if ($parents === null) {
            $parents = $this->getParents();
        }

        if ($parents === null or count($parents) === 0) {
            return null;
        }

        if (count($parents) === 1) {
            return $parents[0];
        }

        return $parents[count($parents) - 2];
    }

    // TODO add parameter to fetch whatever attributes we'd like
    private function getRootParent($all = false)
    {
        $parents = $this->getParents($all);
        if ($parents === null or count($parents) === 0) {
            return null;
        }

        return $parents[0];
    }

    private function getIcon()
    {

        if ($this->directory) {
            return 'folder.png';
        }

        if ($this->link !== null) {
            return 'link.png';
        }

        if (strpos($this->checksum, '.') === false) {
            return 'unknown.png';
        }

        $ext = pathinfo($this->checksum, \PATHINFO_EXTENSION);

        return $ext . '.png';
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
        $download->ip = $_SERVER['REMOTE_ADDR'];
        $download->agent = $_SERVER['HTTP_USER_AGENT'];
        $download->save();
    }

    public function getChildrenObjects()
    {
        if ($this->childrenObjects === null) {
            return [];
        }

        return $this->childrenObjects;
    }

    public function __set($name, $value)
    {
        if ($name === 'childrenObjects') {
            $this->childrenObjects = $value;
        } else {
            parent::__set($name, $value);
        }
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
            'parentObj',
            'rootParent',
            'addedPretty',
            'addedPrettyAll',
            'childrenObjects'
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
            case 'parentObj':
                return $this->getParentObj();
            case 'rootParent':
                return $this->getRootParent();
            case 'rootParentAll':
                return $this->getRootParent(true);
            case 'addedPretty':
                return $this->getAddedPretty();
            case 'addedPrettyAll':
                return $this->getAddedPrettyAll();
            case 'childrenObjects':
                return $this->getChildrenObjects();
            default:
                return null;
        }
    }
}
