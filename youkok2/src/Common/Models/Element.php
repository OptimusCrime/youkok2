<?php
namespace Youkok\Common\Models;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Helpers\ElementHelper;
use Youkok\Helpers\Utilities;
use Youkok\Common\Utilities\UriCleaner;

class Element extends BaseModel
{
    const LINK = 'LINK';
    const COURSE = 'COURSE';
    const DIRECTORY = 'DIRECTORY';
    const NON_DIRECTORY = 'NON_DIRECTORY';
    const FILE = 'FILE';

    const ATTRIBUTES_ALL = 'all';

    const ELEMENT_TYPE_DIRECTORIES = 0;
    const ELEMENT_TYPE_FILES = 1;
    const ELEMENT_TYPE_BOTH = 2;
    const ELEMENT_TYPE_FILE_LAST = 3;

    const VALID_FILE_ICONS = ['htm', 'html', 'java', 'pdf', 'py', 'sql', 'txt'];

    public $timestamps = false;

    protected $table = 'element';
    protected $fillable = ['*'];
    protected $guarded = [''];

    private $parents;
    private $downloads;
    private $childrenObjects;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->parents = null;
        $this->downloads = null;
    }

    public function getType(): string
    {
        if ($this->isLink()) {
            return Element::LINK;
        }
        if ($this->isCourse()) {
            return Element::COURSE;
        }
        if ($this->isDirectory()) {
            return Element::DIRECTORY;
        }

        return Element::FILE;
    }

    public function getCourseCode(): string
    {
        $courseArr = $this->getCourseArray();
        return $courseArr[0];
    }

    public function getCourseName(): string
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

    public function getFullUri(): string
    {
        if ($this->uri !== null and strlen($this->uri) > 0) {
            return $this->uri;
        }

        $this->uri = ElementHelper::constructUri($this->id);

        return $this->uri;
    }

    public function isLink(): bool
    {
        return $this->link !== null && strlen($this->link) > 0;
    }

    public function isCourse(): bool
    {
        return $this->parent === null && $this->directory === 1;
    }

    public function isDirectory(): bool
    {
        return !$this->isCourse() && $this->directory === 1;
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
    private function getRootParent($all = false): ?Element
    {
        $parents = $this->getParents($all);
        if ($parents === null or count($parents) === 0) {
            return null;
        }

        return $parents[0];
    }

    public function getIcon(): string
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

        if (in_array($ext, static::VALID_FILE_ICONS)) {
            return $ext . '.png';
        }

        return 'unknown.png';
    }

    private function getAddedPretty(): string
    {
        return Utilities::prettifySQLDate($this->added);
    }

    private function getAddedPrettyAll(): string
    {
        return Utilities::prettifySQLDateTime($this->added);
    }

    public function getChildrenObjects(): array
    {
        if ($this->childrenObjects === null) {
            return [];
        }

        return $this->childrenObjects;
    }

    public function setDownloads(int $downloads): void
    {
        $this->downloads = $downloads;
    }

    public function getDownloads(): int
    {
        return $this->downloads;
    }

    public function __set($name, $value): void
    {
        if ($name === 'childrenObjects') {
            $this->childrenObjects = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function __isset($name): bool
    {
        if (parent::__isset($name)) {
            return true;
        }

        return in_array($name, [
            'fullUri',
            'icon',
            'parents',
            'downloads',
            'rootParent',
            'addedPretty',
            'addedPrettyAll',
            'childrenObjects'
        ]);
    }


    // TODO: remove this?
    public function __get($key)
    {
        switch ($key) {
            case 'icon':
                return $this->getIcon();
            case 'parents':
                return $this->getParents();
            case 'downloads':
                return $this->getDownloads();
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
        }

        return parent::__get($key);
    }

    public static function fromIdVisible($id, $attributes = ['id', 'link', 'checksum']): Element
    {
        if (!isset($id) or !is_numeric($id)) {
            throw new ElementNotFoundException();
        }

        $query = null;
        if ($attributes == Element::ATTRIBUTES_ALL) {
            $query = Element
                ::where('id', $id);
        }
        else {
            $query = Element
                ::select($attributes)
                ->where('id', $id);
        }

        $element = $query
            ->where('deleted', 0)
            ->where('pending', 0)
            ->first();

        if ($element === null) {
            throw new ElementNotFoundException();
        }

        return $element;
    }

    public static function fromIdAll($id, $attributes = ['id', 'link', 'checksum']): Element
    {
        if (!isset($id) or !is_numeric($id)) {
            throw new ElementNotFoundException();
        }

        $query = null;
        if ($attributes == Element::ATTRIBUTES_ALL) {
            $element = Element
                ::where('id', $id)
                ->first();

            if ($element === null) {
                throw new ElementNotFoundException();
            }

            return $element;
        }

        $element = Element
            ::select($attributes)
            ->where('id', $id)
            ->first();

        if ($element === null) {
            throw new ElementNotFoundException();
        }

        return $element;
    }

    public static function fromUriFileVisible($uri, $attributes = ['id', 'parent', 'name', 'checksum', 'link', 'directory'])
    {
        return self::fromUriFragments($uri, $attributes, self::ELEMENT_TYPE_FILE_LAST);
    }

    public static function fromUriDirectoryVisible($uri, $attributes = ['id', 'parent', 'name', 'checksum', 'link', 'directory'])
    {
        return self::fromUriFragments($uri, $attributes, self::ELEMENT_TYPE_DIRECTORIES);
    }

    // https://laracasts.com/discuss/channels/general-discussion/save-updated-model-dosnt-work/replies/32779
    public static function newFromStd($stdClass)
    {
        $instance = new Element();
        $instance->setRawAttributes(get_object_vars($stdClass), true);

        return $instance;
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
}
