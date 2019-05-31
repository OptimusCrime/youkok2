<?php
namespace Youkok\Common\Models;

use Illuminate\Database\Eloquent\Builder;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Helpers\ElementHelper;
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
    private $downloads;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fullUri = null;
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
        } else {
            $this->fullUri = ElementHelper::constructUri($this->id);
        }

        return $this->fullUri;
    }

    public function isLink(): bool
    {
        return $this->link !== null && strlen($this->link) > 0;
    }

    public function isFile(): bool
    {
        return $this->checksum !== null;
    }

    public function isCourse(): bool
    {
        return $this->parent === null && $this->directory === 1;
    }

    public function isDirectory(): bool
    {
        return !$this->isCourse() && $this->directory === 1;
    }

    public function getParentsVisible(array $columns = ['id', 'name', 'slug', 'uri', 'link', 'parent', 'directory']): array
    {
        return $this->getParents($columns, static::FETCH_ONLY_VISIBLE);
    }

    public function getParentsAll(array $columns = ['id', 'parent']): array
    {
        return $this->getParents($columns, static::FETCH_ALL);
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

            if ($currentObject === null) {
                break;
            }

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

    // TODO: This needs to check that parents are also visible!
    // automatically add parent to the list of attributes if it is missing
    public static function fromIdVisible(
        int $id,
        array $attributes = ['id', 'link', 'checksum', 'parent']
    ): Element {
        if (!isset($id) or !is_numeric($id)) {
            throw new ElementNotFoundException();
        }

        $query = null;
        if ($attributes == Element::ATTRIBUTES_ALL) {
            $query = Element
                ::where('id', $id);
        } else {
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

    public static function fromIdAll(
        int $id,
        array $attributes = ['id', 'link', 'checksum']
    ): Element {
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

    public static function fromUriFileVisible(
        string $uri,
        array $attributes = ['id', 'parent', 'name', 'slug', 'uri', 'checksum', 'link', 'directory']
    ): Element {
        return self::fromUriFragments($uri, $attributes, self::ELEMENT_TYPE_FILE_LAST);
    }

    public static function fromUriDirectoryVisible(
        string $uri,
        array $attributes = ['id', 'parent', 'name', 'slug', 'uri', 'empty', 'checksum', 'link', 'directory']
    ): Element {
        return self::fromUriFragments($uri, $attributes, self::ELEMENT_TYPE_DIRECTORIES);
    }

    // https://laracasts.com/discuss/channels/general-discussion/save-updated-model-dosnt-work/replies/32779
    public static function newFromStd($stdClass)
    {
        $instance = new Element();
        $instance->setRawAttributes(get_object_vars($stdClass), true);

        return $instance;
    }

    private static function fromUriFragments(
        string $uri,
        array $attributes = ['id', 'parent', 'name', 'checksum', 'link', 'directory'],
        int $type = self::ELEMENT_TYPE_DIRECTORIES
    ) : Element {
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
                } else {
                    $query = static::handleElementType($query, Element::ELEMENT_TYPE_DIRECTORIES);
                }
            } else {
                $query = static::handleElementType($query, $type);
            }

            $element = $query->first();

            if ($element === null) {
                throw new ElementNotFoundException();
            }

            $parent = $element->id;
        }

        return $element;
    }

    private static function handleElementType(Builder $element, string $type): Builder
    {
        switch ($type) {
            case self::ELEMENT_TYPE_DIRECTORIES:
                $element->where('directory', 1);
                break;
            case self::ELEMENT_TYPE_FILES:
                $element->where('directory', 0);
                break;
            default:
                throw new GenericYoukokException('Invalid value passed to method');
        }

        return $element;
    }
}
