<?php
namespace Youkok\Common\Models;

use Carbon\Carbon;

use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class Session extends BaseModel
{
    const DEFAULT_ADMIN = false;
    const DEFAULT_MOST_POPULAR_ELEMENT = MostPopularElement::MONTH;
    const DEFAULT_MOST_POPULAR_COURSE = MostPopularCourse::MONTH;

    const KEY_ADMIN = 'admin';
    const KEY_MOST_POPULAR_ELEMENT = 'most_popular_element';
    const KEY_MOST_POPULAR_COURSE = 'most_popular_course';

    public $timestamps = false;
    protected $table = 'session';

    private $admin;
    private $mostPopularElement;
    private $mostPopularCourse;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->admin = static::DEFAULT_ADMIN;
        $this->mostPopularElement = static::DEFAULT_MOST_POPULAR_ELEMENT;
        $this->mostPopularCourse = static::DEFAULT_MOST_POPULAR_COURSE;
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function getMostPopularElement(): string
    {
        return $this->mostPopularElement;
    }

    public function getMostPopularCourse(): string
    {
        return $this->mostPopularCourse;
    }

    public function getAllData(): array
    {
        return [
            static::KEY_ADMIN => $this->admin,
            static::KEY_MOST_POPULAR_ELEMENT => $this->mostPopularElement,
            static::KEY_MOST_POPULAR_COURSE => $this->mostPopularCourse,
        ];
    }

    public function setAdmin(bool $flag): void
    {
        $this->admin = $flag;
    }

    public function setMostPopularElement(string $delta): void
    {
        $this->mostPopularElement = $delta;
    }

    public function setMostPopularCourse(string $delta): void
    {
        $this->mostPopularCourse = $delta;
    }

    public function save(array $options = []): bool
    {
        // Package the fields back into data before saving
        $data = $this->getAllData();

        $this->data = json_encode($data);
        $this->last_updated = Carbon::now();

        return parent::save($options);
    }

    public static function boot()
    {
        parent::boot();

        Session::retrieved(function (Session $session) {
            $data = json_decode($session->data, true);
            if (!is_array($data)) {
                return $session;
            }

            if (isset($data[Session::KEY_ADMIN]) and is_bool($data[Session::KEY_ADMIN])) {
                $session->setAdmin($data[Session::KEY_ADMIN]);
            }

            if (isset($data[Session::KEY_MOST_POPULAR_ELEMENT]) and is_String($data[Session::KEY_MOST_POPULAR_ELEMENT])) {
                $session->setMostPopularElement($data[Session::KEY_MOST_POPULAR_ELEMENT]);
            }

            if (isset($data[Session::KEY_MOST_POPULAR_COURSE]) and is_String($data[Session::KEY_MOST_POPULAR_COURSE])) {
                $session->setMostPopularCourse($data[Session::KEY_MOST_POPULAR_COURSE]);
            }

            return $session;
        });
    }
}
