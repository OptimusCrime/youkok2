<?php
namespace Youkok\Biz\Services\Admin;

use Carbon\Carbon;

use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\CreateException;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class FileCreateDirectoryService
{
    const int MIN_VALID_NAME_LENGTH = 2;

    private FilesService $filesService;
    private ElementService $elementService;

    public function __construct(FilesService $filesService, ElementService $elementService)
    {
        $this->filesService = $filesService;
        $this->elementService = $elementService;
    }

    /**
     * @throws CreateException
     * @throws ElementNotFoundException
     */
    public function createDirectory(RouteParserInterface $routeParser, int $courseId, int $directoryId, string $value): array
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
        $element->empty = false;
        $element->directory = true;
        $element->pending = false;
        $element->deleted = false;
        $element->added = Carbon::now();
        $element->requested_deletion = false;

        if ($course->empty) {
            $course->empty = false;

            if (!$course->save()) {
                throw new CreateException('Failed to set course at non-empty. CourseId: ' . $courseId);
            }
        }

        if (!$element->save()) {
            throw new CreateException('Failed to create directory of ' . $directoryId . ' with name "' . $value . '".');
        }

        return $this->filesService->buildTreeFromId($routeParser, $course->id);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getElement(int $id): Element
    {
        return $this->elementService->getElement(
            new SelectStatements('id', $id),
            [
                ElementService::FLAG_FETCH_URI
            ]
        );
    }
}
