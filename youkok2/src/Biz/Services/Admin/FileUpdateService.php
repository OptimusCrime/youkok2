<?php
namespace Youkok\Biz\Services\Admin;

use Carbon\Carbon;

use Exception;
use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\Admin\AdminElementService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;

class FileUpdateService
{
    private FilesService $fileService;
    private AdminElementService $adminElementService;
    private ElementService $elementService;
    private CacheService $cacheService;

    public function __construct(
        FilesService $fileService,
        AdminElementService $adminElementService,
        ElementService $elementService,
        CacheService $cacheService
    ) {
        $this->fileService = $fileService;
        $this->adminElementService = $adminElementService;
        $this->elementService = $elementService;
        $this->cacheService = $cacheService;
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function put(RouteParserInterface $routeParser, int $courseId, int $elementId, array $data): array
    {
        $course = $this->getCourse($courseId);

        if (!$course->isCourse()) {
            throw new Exception('Invalid courseId value: ' . $courseId);
        }

        // These keys needs to be present
        if (!array_key_exists('parent', $data)
            || !array_key_exists('name', $data)
            || !array_key_exists('slug', $data)
            || !array_key_exists('uri', $data)
        ) {
            throw new Exception('Invalid data posted, missing one of: parent, name, slug or uri.');
        }

        $element = $this->getElement($elementId);

        // Let's avoid this one, shall we...
        if ($element->parent === intval($data['id'])) {
            throw new Exception('Can not assign self as parent!');
        }

        $oldElement = clone $element;

        // Check if the parent was changed during update
        $newParent = static::evaluateParentWasUpdated($oldElement, intval($data['parent']));

        $element->parent = $this->validateNewParent($data['parent'] === null ? null : intval($data['parent']));
        $element->empty = static::evaluateAndSetNumericBoolean('empty', $data);
        $element->directory = static::evaluateAndSetNumericBoolean('directory', $data);
        $element->pending = static::evaluateAndSetNumericBoolean('pending', $data);
        $element->deleted = static::evaluateAndSetNumericBoolean('deleted', $data);
        $element->requested_deletion = static::evaluateAndSetNumericBoolean('requested_deletion', $data);

        $element->checksum = static::evaluateAndSetNullSafeString('checksum', $data);
        $element->size = static::evaluateAndSetNullSafeString('size', $data);
        $element->link = static::evaluateAndSetNullSafeString('link', $data);

        if ($oldElement->pending && !$element->pending) {
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

        return $this->fileService->buildTreeFromId($routeParser, $course->id);
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     */
    private function getCourse(int $id): Element
    {
        $element = $this->elementService->getElement(
            new SelectStatements('id', $id),
            [
                ElementService::FLAG_FETCH_URI
            ]
        );

        if (!$element->isCourse()) {
            throw new Exception('Invalid courseId value: ' . $id);
        }

        return $element;
    }

    /**
     * @throws RedisException
     */
    private function deleteOutdatedCaches(Element $oldElement, Element $updatedElement): void
    {
        if ($this->anyChanged($oldElement, $updatedElement)) {
            $this->cacheService->delete(CacheKeyGenerator::keyForBoxesNumberOfFiles());
            $this->cacheService->delete(CacheKeyGenerator::keyForBoxesNumberOfCoursesWithContent());
            $this->cacheService->delete(CacheKeyGenerator::keyForBoxesNumberOfFilesThisMonth());
            $this->cacheService->delete(CacheKeyGenerator::keyForNewestElementsPayload());
        }
    }

    /**
     * @throws RedisException
     */
    private function deleteUriCacheForElement(?string $uri): void
    {
        if ($uri === null) {
            return;
        }

        $this->cacheService->delete(CacheKeyGenerator::keyForVisibleUriDirectory($uri));
        $this->cacheService->delete(CacheKeyGenerator::keyForAllParentsAreDirectoriesExceptCurrentIsFile($uri));
        $this->cacheService->delete(CacheKeyGenerator::keyForVisibleUriFile($uri));
    }

    private function anyChanged(Element $oldElement, Element $updatedElement): bool
    {
        foreach (['pending', 'deleted']  as $key) {
            if ($oldElement->$key !== $updatedElement->$key) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws ElementNotFoundException
     * @throws RedisException
     */
    private function updateParentAndChildren(Element $oldElement, Element $newElement): void
    {
        if ($oldElement->parent === null) {
            return;
        }

        $oldParent = $this->getElementWithUri($oldElement->parent);

        // If old parent is now empty, update that flag
        if ($this->oldParentIsNowEmpty($oldParent->id, $newElement)) {
            if (!$oldParent->empty) {
                $oldParent->empty = true;
                $oldParent->save();
            }
        } else {
            if ($oldParent->empty) {
                $oldParent->empty = false;
                $oldParent->save();
            }
        }

        if ($newElement->getType() !== Element::COURSE && $newElement->getType() !== Element::DIRECTORY) {
            return;
        }

        $this->recursivelyUpdateChildrenUris($oldElement->id);
    }

    /**
     * @throws ElementNotFoundException
     * @throws RedisException
     */
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

    /**
     * @throws ElementNotFoundException
     */
    private function getElement(int $elementId): Element
    {
        return $this->elementService->getElement(
            new SelectStatements('id', $elementId),
            [
                ElementService::FLAG_FETCH_URI
            ]
        );
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     */
    private function regenerateNameSlugAndURI(array $data, Element $element): array
    {
        if ($element->getType() === Element::COURSE) {
            $courseSplit = explode('||', $data['name']);
            if (count($courseSplit) !== 2) {
                throw new Exception('Encountered invalid course name: ' . $data['name']);
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

    /**
     * @throws ElementNotFoundException
     */
    private function getElementWithUri(int $id): Element
    {
        return $this->elementService->getElement(
            new SelectStatements('id', $id),
            [
                ElementService::FLAG_FETCH_URI
            ]
        );
    }

    /**
     * @throws ElementNotFoundException
     */
    private function validateNewParent(?int $parentId): ?int
    {
        if ($parentId === null) {
            return null;
        }

        $parent = $this->elementService->getElement(
            new SelectStatements('id', $parentId),
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

            if ($oldParentChild->deleted || $oldParentChild->pending) {
                continue;
            }

            $visibleOrEmptyCounter++;
        }

        // If no visible files remain for this parent, it is now empty, and should be marked as such
        return $visibleOrEmptyCounter === 0;
    }

    /**
     * @throws Exception
     */
    private static function evaluateAndSetNumericBoolean(string $key, array $data): int
    {
        if (!array_key_exists($key, $data)) {
            return 0;
        }

        if (in_array(strval($data[$key]), [true, false])) {
            return intval($data[$key]);
        }

        throw new Exception('Unexpected value for key "' . $key . '". Value = ' . $data[$key]);
    }

    private static function evaluateAndSetNullSafeString(string $key, array $data): ?string
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        if (mb_strlen($data[$key] ?? '') === 0) {
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
