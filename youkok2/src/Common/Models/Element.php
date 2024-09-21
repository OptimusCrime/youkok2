<?php
namespace Youkok\Common\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int id
 * @property string name
 * @property string|null slug
 * @property string|null uri
 * @property int|null parent
 * @property int empty
 * @property string|null checksum
 * @property int|null size
 * @property int directory
 * @property int pending
 * @property int deleted
 * @property string|null link
 * @property string added
 * @property int requested_deletion
 * @method static where(string $key, string $value)
 * @method static select(string|array ...$string)
 *
 */
class Element extends Model
{
    const string LINK = 'LINK';
    const string COURSE = 'COURSE';
    const string DIRECTORY = 'DIRECTORY';
    const string FILE = 'FILE';

    const array VALID_FILE_ICONS = ['htm', 'html', 'java', 'pdf', 'py', 'sql', 'txt'];

    public $timestamps = false;

    protected $table = 'element';
    protected $fillable = ['*'];
    protected $guarded = [''];

    private ?int $downloads;
    private array $parents;
    private array $children;
    private ?string $downloadedTime;

    public function __construct()
    {
        parent::__construct();

        $this->downloads = null;
        $this->parents = [];
        $this->children = [];
        $this->downloadedTime = null;
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

    /**
     * @throws Exception
     */
    public function getCourseCode(): string
    {
        $courseArr = $this->getCourseArray();
        return $courseArr[0];
    }

    /**
     * @throws Exception
     */
    public function getCourseName(): string
    {
        $courseArr = $this->getCourseArray();

        return $courseArr[1];
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getCourseArray(): array
    {
        if ($this->name === null) {
            throw new Exception('Element does not have a name');
        }

        $split = explode('||', $this->name);

        if (count($split) !== 2) {
            throw new Exception('Element does not have valid course name: ' . $this->name);
        }

        return $split;
    }

    public function isLink(): bool
    {
        return $this->link !== null && mb_strlen($this->link) > 0;
    }

    public function isCourse(): bool
    {
        return $this->parent === null && $this->directory === 1;
    }

    public function isDirectory(): bool
    {
        return !$this->isCourse() && $this->directory === 1;
    }

    public function getIcon(): string
    {
        if ($this->directory) {
            return 'folder.png';
        }

        if ($this->link !== null) {
            return 'link.png';
        }

        if (!str_contains($this->checksum, '.')) {
            return 'unknown.png';
        }

        $ext = pathinfo($this->checksum, PATHINFO_EXTENSION);

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

    public function setParents(array $parents): void
    {
        $this->parents = $parents;
    }

    public function getParents(): array
    {
        return $this->parents;
    }

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getCourse(): ?Element
    {
        if (count($this->parents) === 0) {
            return null;
        }

        return $this->parents[0];
    }

    public function setDownloadedTime(string $downloadedTime): void
    {
        $this->downloadedTime = $downloadedTime;
    }

    public function getDownloadedTime(): string
    {
        return $this->downloadedTime;
    }

    public function isVisible(): bool
    {
        return $this->deleted === 0 && $this->pending === 0;
    }
}
