<?php

namespace Youkok\Common\Models;

use phpDocumentor\Reflection\Types\Boolean;
use Youkok\Biz\Exceptions\GenericYoukokException;

class Element extends BaseModel
{
    const LINK = 'LINK';
    const COURSE = 'COURSE';
    const DIRECTORY = 'DIRECTORY';
    const NON_DIRECTORY = 'NON_DIRECTORY';
    const FILE = 'FILE';

    const VALID_FILE_ICONS = ['htm', 'html', 'java', 'pdf', 'py', 'sql', 'txt'];

    public $timestamps = false;

    protected $table = 'element';
    protected $fillable = ['*'];
    protected $guarded = [''];

    private $downloads;
    private $parents;
    private $children;
    private $downloadedTime;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

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

    public function getCourseCode(): string
    {
        $courseArr = $this->getCourseArray();
        return $courseArr[0];
    }

    public function getCourseName(): string
    {
        $courseArr = $this->getCourseArray();

        return $courseArr[1];
    }

    private function getCourseArray(): array
    {
        if ($this->name === null) {
            throw new GenericYoukokException('Element does not have a name');
        }

        $split = explode('||', $this->name);

        if (count($split) !== 2) {
            throw new GenericYoukokException('Element does not have valid course name: ' . $this->name);
        }

        return $split;
    }

    public function isLink(): bool
    {
        return $this->link !== null && mb_strlen($this->link) > 0;
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
