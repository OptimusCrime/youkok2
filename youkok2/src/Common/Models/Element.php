<?php
namespace Youkok\Common\Models;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
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

    // TODO remove this you lazy slob
    const ATTRIBUTES_ALL = 'all';

    const FETCH_ONLY_VISIBLE = 1;
    const FETCH_ALL = 2;

    const ELEMENT_TYPE_DIRECTORIES = 0;
    const ELEMENT_TYPE_FILES = 1;
    const ELEMENT_TYPE_BOTH = 2;
    const ELEMENT_TYPE_FILE_LAST = 3;

    const VALID_FILE_ICONS = ['htm', 'html', 'java', 'pdf', 'py', 'sql', 'txt'];

    public $timestamps = false;

    protected $table = 'element';
    protected $fillable = ['*'];
    protected $guarded = [''];

    private $fullUri;
    private $parentsVisible;
    private $parentsAll;
    private $downloads;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fullUri = null;
        $this->parentsVisible = null;
        $this->parentsAll = null;
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
            throw new GenericYoukokException('Element does not have a name');
        }

        return explode('||', $this->name);
    }

    public function getFullUri(): string
    {
        if ($this->fullUri !== null) {
            return $this->fullUri;
        }

        // Transfer content of URI to self::fullUri, in case we have to populate it. This way, we avoid polluting the
        // database columns
        if ($this->uri !== null and strlen($this->uri) > 0) {
            $this->fullUri = $this->uri;
        }
        else {
            $this->fullUri = ElementHelper::constructUri($this->id);
        }

        return $this->fullUri;
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

    public function getParentsVisible(array $columns = ['id', 'name', 'slug', 'uri', 'parent', 'directory']): array
    {
        if ($this->parentsVisible !== null) {
            return $this->parentsVisible;
        }

        $this->parentsVisible = $this->getParents($columns, static::FETCH_ONLY_VISIBLE);

        return $this->parentsVisible;
    }

    public function getParentsAll(array $columns = ['id', 'parent']): array
    {
        if ($this->parentsAll !== null) {
            return $this->parentsAll;
        }

        $this->parentsAll = $this->getParents($columns, static::FETCH_ALL);

        return $this->parentsAll;
    }

    public function getRootParentVisible(array $columns = ['id', 'name', 'slug', 'uri', 'parent', 'directory']): Element
    {
        $parents = $this->getParentsVisible($columns);

        return $parents[0];
    }

    public function getRootParentAll(array $columns = ['id', 'parent']): Element
    {
        $parents = $this->getParentsAll($columns);

        return $parents[0];
    }

    private function getParents(array $columns, int $fetchDeleted): array
    {
        $parents = [$this];
        $currentObject = $this;
        while ($currentObject->parent !== null and $currentObject->parent !== 0) {
            $currentObject = Element::select($columns)
                ->where('id', $currentObject->parent);

            if ($fetchDeleted) {
                $currentObject = $currentObject
                    ->where('deleted', 0)
                    ->where('pending', 0)
                    ->where('directory', 1);
            }

            $currentObject = $currentObject->first();

            $parents[] = $currentObject;
        }

        if (count($parents) === 0) {
            throw new ElementNotFoundException();
        }

        return array_reverse($parents);
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

    public function setDownloads(int $downloads): void
    {
        $this->downloads = $downloads;
    }

    public function getDownloads(): int
    {
        return $this->downloads;
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
