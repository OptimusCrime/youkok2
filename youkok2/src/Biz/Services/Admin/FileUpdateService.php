<?php
namespace Youkok\Biz\Services\Admin;

use Carbon\Carbon;
use Youkok\Biz\Exceptions\UpdateException;
use Youkok\Biz\Pools\ElementPool;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\Admin\AdminElementService;
use Youkok\Biz\Services\Models\ElementService;

use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;

class FileUpdateService
{
    private $adminFilesService;
    private $adminElementService;
    private $elementService;
    private $cacheService;

    public function __construct(
        FilesService $adminFilesService,
        AdminElementService $adminElementService,
        ElementService $elementService,
        CacheService $cacheService
    ) {
        $this->adminElementService = $adminElementService;
        $this->adminFilesService = $adminFilesService;
        $this->elementService = $elementService;
        $this->cacheService = $cacheService;
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

        // These keys needs to be present
        if (!array_key_exists('parent', $data)
            || !array_key_exists('name', $data)
            || !array_key_exists('slug', $data)
            || !array_key_exists('uri', $data)
        ) {
            throw new UpdateException('Invalid data posted, missing one of: parent, name, slug or uri.');
        }

        $element = $this->getElement($elementId);

        // Let's avoid this one, shall we...
        if ($element->parent === intval($data['id'])) {
            throw new UpdateException('Can not assign self as parent!');
        }

        $oldElement = clone $element;

        // Check if the parent was changed during update
        $newParent = static::evaluateParentWasUpdated($oldElement, intval($data['parent']));

        $element->parent = $this->validateNewParent(intval($data['parent']));
        $element->empty = static::evaluateAndSetNumericBoolean('empty', $data);
        $element->directory = static::evaluateAndSetNumericBoolean('directory', $data);
        $element->pending = static::evaluateAndSetNumericBoolean('pending', $data);
        $element->deleted = static::evaluateAndSetNumericBoolean('deleted', $data);

        $element->checksum = static::evaluateAndSetNullSafeString('checksum', $data);
        $element->size = static::evaluateAndSetNullSafeString('size', $data);
        $element->link = static::evaluateAndSetNullSafeString('link', $data);

        if ($oldElement->pending === 1 && $element->pending === 0) {
            $element->added = Carbon::now();
        }

        if ($element->getType() === Element::LINK) {
            // If the type is link, update the name and ignore slug and URI
            if ($data['name'] !== $oldElement->name) {
                $element->name = $data['name'];
            }
        } else {
            if ($newParent
                || $element->name !== $data['name']
                || $element->slug !== $data['slug']
                || $element->uri !== $data['uri']
            ) {
                $updatedData = $this->regenerateNameSlugAndURI($data, $element);

                $element->name = $updatedData['name'];
                $element->slug = $updatedData['slug'];
                $element->uri = $updatedData['uri'];
            }
        }

        // All other changes are applied to parents and/or children, which depends on us saving this element right here
        $element->save();

        if ($newParent || $oldElement->pending !== $element->pending || $oldElement->deleted !== $element->deleted) {
            $this->updateParentAndChildren($oldElement, $element);
            $this->deleteUriCacheForElement($oldElement->uri);
        }

        $this->deleteOutdatedCaches($oldElement, $element);

        return $this->adminFilesService->buildTreeFromId($course->id);
    }

    private function deleteOutdatedCaches(Element $oldElement, Element $updatedElement): void
    {
        if ($this->anyChanged(['pending', 'deleted'], $oldElement, $updatedElement)) {
            $this->cacheService->delete(CacheKeyGenerator::keyForBoxesNumberOfFiles());
            $this->cacheService->delete(CacheKeyGenerator::keyForBoxesNumberOfCoursesWithContent());
            $this->cacheService->delete(CacheKeyGenerator::keyForBoxesNumberOfFilesThisMonth());
            $this->cacheService->delete(CacheKeyGenerator::keyForNewestElementsPayload());
        }
    }

    private function deleteUriCacheForElement(?string $uri): void
    {
        if ($uri === null) {
            return;
        }

        $this->cacheService->delete(CacheKeyGenerator::keyForVisibleUriDirectory($uri));
        $this->cacheService->delete(CacheKeyGenerator::keyForAllParentsAreDirectoriesExceptCurrentIsFile($uri));
        $this->cacheService->delete(CacheKeyGenerator::keyForVisibleUriFile($uri));
    }

    private function anyChanged(array $keys, Element $oldElement, Element $updatedElement): bool
    {
        foreach ($keys as $key) {
            if ($oldElement->$key !== $updatedElement->$key) {
                return true;
            }
        }

        return false;
    }

    private function updateParentAndChildren(Element $oldElement, Element $newElement): void
    {
        $oldParent = $this->getElementWithUri($oldElement->parent);

        // If old parent is now empty, update that flag
        if ($this->oldParentIsNowEmpty($oldParent->id, $newElement)) {
            if ($oldParent->empty === 0) {
                $oldParent->empty = 1;
                $oldParent->save();
            }
        } else {
            if ($oldParent->empty === 1) {
                $oldParent->empty = 0;
                $oldParent->save();
            }
        }

        if ($newElement->getType() !== Element::COURSE && $newElement->getType() !== Element::DIRECTORY) {
            return;
        }

        $this->recursivelyUpdateChildrenUris($oldElement->id);
    }

    private function recursivelyUpdateChildrenUris(int $id): void
    {
        $children = $this->adminElementService->getAllChildren($id);
        if (count($children) === 0) {
            return;
        }

        foreach ($children as $child) {
            $childObj = $this->getElementWithUri($child->id);

            $uriFromDatabase = $childObj->uri;
            $uriFromSlugFragments = $this->elementService->getUriForElement($child, true);

            if ($uriFromDatabase !== $uriFromSlugFragments) {
                $childObj->uri = $uriFromSlugFragments;
                $childObj->save();

                // Also clear any URI related cache
                $this->deleteUriCacheForElement($uriFromDatabase);
            }

            // The child might have other children that also rely on the parent (or grandparent or whatever)'s slug...
            $this->recursivelyUpdateChildrenUris($childObj->id);
        }
    }

    private function getElement(int $elementId): Element
    {
        return $this->elementService->getElement(
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
            ],
            [
                ElementService::FLAG_FETCH_URI
            ]
        );
    }

    private function regenerateNameSlugAndURI(array $data, Element $element): array
    {
        if ($element->getType() === Element::COURSE) {
            $courseSplit = explode('||', $data['name']);
            if (count($courseSplit) !== 2) {
                throw new UpdateException('Encountered invalid course name: ' . $data['name']);
            }

            $slug = ElementService::createSlug($courseSplit[0]);

            return[
                'name' => $data['name'],
                'slug' => $slug,
                'uri' => $slug, // URIs for courses are just the slugs
            ];
        }

        $parent = $this->getElementWithUri($element->parent);
        $slug = ElementService::createSlug($data['name']);

        return [
            'name' => $data['name'],
            'slug' => $slug,
            'uri' => $parent->uri . '/' . $slug,
        ];
    }

    private function getElementWithUri(int $id): Element
    {
        return $this->elementService->getElement(
            new SelectStatements('id', $id),
            ['id', 'empty', 'parent'],
            [
                ElementService::FLAG_FETCH_URI
            ]
        );
    }

    private function validateNewParent(int $parentId): int
    {
        $parent = $this->elementService->getElement(
            new SelectStatements('id', $parentId),
            ['id', 'parent'],
            []
        );

        return $parent->id;
    }

    private function oldParentIsNowEmpty(int $parentId, Element $updatedElement): bool
    {
        $oldParentChildren = $this->adminElementService->getAllChildren($parentId);
        $visibleOrEmptyCounter = 0;

        foreach ($oldParentChildren as $oldParentChild) {
            if ($oldParentChild->id === $updatedElement->id && !$updatedElement->isVisible()) {
                continue;
            }

            if ($oldParentChild->deleted === 1 || $oldParentChild->pending === 1) {
                continue;
            }

            $visibleOrEmptyCounter++;
        }

        // If no visible files remain for this parent, it is now empty, and should be marked as such
        return $visibleOrEmptyCounter === 0;
    }

    private static function evaluateAndSetNumericBoolean(string $key, array $data): int
    {
        if (!array_key_exists($key, $data)) {
            return 0;
        }

        if (in_array(strval($data[$key]), ['1', '0'])) {
            return intval($data[$key]);
        }

        throw new UpdateException('Unexpected value for key "' . $key . '". Value = ' . $data[$key]);
    }

    private static function evaluateAndSetNullSafeString(string $key, array $data): ?string
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        if (mb_strlen($data[$key]) === 0) {
            return null;
        }

        return strval($data[$key]);
    }

    private static function evaluateParentWasUpdated(Element $oldElement, ?int $newParent): bool
    {
        if ($oldElement->parent === null && $newParent === null) {
            return false;
        }

        return $oldElement->parent !== $newParent;
    }
}
