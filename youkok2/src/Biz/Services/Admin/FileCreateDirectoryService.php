<?php

namespace Youkok\Biz\Services\Admin;

use Carbon\Carbon;
use Youkok\Biz\Exceptions\CreateException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class FileCreateDirectoryService
{
    const MIN_VALID_NAME_LENGTH = 2;

    private $adminFilesService;
    private $elementService;

    public function __construct(FilesService $adminFilesService, ElementService $elementService) {
        $this->adminFilesService = $adminFilesService;
        $this->elementService = $elementService;
    }

    public function createDirectory(int $courseId, int $directoryId, string $value): array
    {
        if (mb_strlen($value) < static::MIN_VALID_NAME_LENGTH) {
            throw new CreateException('Directory name too short. Found: ' . $value);
        }

        $course = $this->getElement($courseId);
        if (!$course->isCourse()) {
            throw new CreateException('Invalid courseId value: ' . $courseId);
        }

        $directory = $courseId === $directoryId ? $course : $this->getElement($directoryId);

        $slug = ElementService::createSlug($value);
        $uri = $directory->uri . '/' . $slug;

        $element = new Element();
        $element->name = $value;
        $element->slug = $slug;
        $element->uri = $uri;
        $element->parent = $directory->id;
        $element->empty = 0;
        $element->directory = 1;
        $element->pending = 0;
        $element->deleted = 0;
        $element->added = Carbon::now();

        if ($course->empty === 1) {
            $course->empty = 0;

            if (!$course->save()) {
                throw new GenericYoukokException('Failed to set course at non-empty. CourseId: ' . $courseId);
            }
        }

        if (!$element->save()) {
            throw new CreateException('Failed to create directory of ' . $directoryId . ' with name "' . $value . '".');
        }

        return $this->adminFilesService->buildTree([
            $course->id
        ]);
    }


    private function getElement(int $id): Element
    {
        return $this->elementService->getElement(
            new SelectStatements('id', $id),
            ['id', 'empty', 'parent'],
            [
                ElementService::FLAG_FETCH_URI
            ]
        );
    }
}
