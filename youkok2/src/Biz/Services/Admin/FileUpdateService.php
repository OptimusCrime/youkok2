<?php
namespace Youkok\Biz\Services\Admin;

use Youkok\Biz\Exceptions\UpdateException;
use Youkok\Biz\Services\Models\ElementService;;
use Youkok\Common\Utilities\SelectStatements;

class FileUpdateService
{
    private $adminFilesService;
    private $elementService;

    public function __construct(FilesService $adminFilesService, ElementService $elementService) {
        $this->adminFilesService = $adminFilesService;
        $this->elementService = $elementService;
    }

    public function put(int $courseId, int $elementId, array $data): array
    {
        $course = $this->elementService->getElement(
            new SelectStatements('id', $courseId),
            ['id', 'empty', 'parent'],
            [
                ElementService::FLAG_FETCH_URI
            ]
        );

        if (!$course->isCourse()) {
            throw new UpdateException('Invalid courseId value: ' . $courseId);
        }

        $element = $this->elementService->getElement(
            new SelectStatements('id', $elementId),
            [
                'id',
                'name',
                'slug',
                'uri',
                'parent',
                'empty',
                'checksum',
                'size',
                'directory',
                'pending',
                'deleted',
                'link',
            ], [
            ]
        );

        $oldElement = clone $element;

        // TODO
        // Set null/provided value to safe attributes:
        // checksum, size, link

        // Validate and update bools:
        // empty, directory, pending, deleted

        // Check if name was altered
        // if name, and not slug or uri, regenerate it
        // if name, and/or uri, keep it

        // Check if parent was changed
        // if was, check if old parent should be empty
        //         also delete old cache
        //         if moved is directory, fetch children and update them as well (uri and cache)

        // Finally, check if we need to change some of the other caches... this is going to be fun.

        return $this->adminFilesService->buildTree([
            $course->id
        ]);
    }
}
