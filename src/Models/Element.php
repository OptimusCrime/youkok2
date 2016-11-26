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
        if ($key == 'fullUrl') {
            return 'not-yet.html';
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $key .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
}
