<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Collection;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidFlagCombination;
use Youkok\Biz\Pools\Containers\ElementPoolContainer;
use Youkok\Biz\Pools\ElementPool;
use Youkok\Biz\Services\CacheService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Common\Utilities\UriCleaner;

class ElementService
{
    const SORT_TYPE_ORGANIZED = 0;
    const SORT_TYPE_AGE = 1;

    const FLAG_FETCH_PARENTS = 'FLAG_FETCH_PARENTS';
    const FLAG_FETCH_COURSE = 'FLAG_FETCH_COURSE';
    const FLAG_FETCH_URI = 'FLAG_FETCH_URI';

    // This flag will force visible check on all parents, even if FLAG_FETCH_PARENTS is not used
    const FLAG_ENSURE_VISIBLE = 'FLAG_ENSURE_VISIBLE';

    // This flag will force check to ensure that all the parents are directories, and current element is file
    const FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE =
        'FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE';

    const FLAG_ENSURE_IS_COURSE = 'FLAG_ENSURE_IS_COURSE';

    const FLAG_ONLY_DIRECTORIES = 'FLAG_ONLY_DIRECTORIES';

    /** @var CacheService */
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getElement(SelectStatements $selectStatements, array $attributes = [], array $flags = []): Element
    {
        $this->validateFlags($flags);
        $attributes = static::supplementAttributesBasedOnFlags($attributes, $flags);

        $element = $this->buildQuery($selectStatements, $attributes, $flags);

        if ($element->parent === null) {
            // .parent was fetched, but was `null` indicating that the current Element is a course
            $element->setParents([]);
        }
        else {
            // If any of these flags are sat, fetch parents
            $fetchParentsFlags = [
                static::FLAG_FETCH_PARENTS,
                static::FLAG_ENSURE_VISIBLE,
                static::FLAG_FETCH_COURSE,
                static::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE
            ];
            if (static::anyFlags($fetchParentsFlags, $flags)) {
                $parents = $this->fetchParents($element, $attributes, $flags);

                $element->setParents($parents);
            }
        }

        if (in_array(static::FLAG_FETCH_URI, $flags) && $element->uri === null && $element->getType() !== Element::LINK) {
            // If the current type is course, we can just use the slug
            if ($element->getType() === Element::COURSE) {
                $element->uri = $element->slug;
            }
            else {
                $element->uri = $this->getUriForElement($element);
            }
        }

        if (in_array(static::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE, $flags)) {
            if (count($element->getParents()) === 0) {
                throw new GenericYoukokException('No parents loaded for verification.');
            }

            foreach ($element->getParents() as $index => $parent) {
                // First child will be identified as a COURSE
                if ($index > 0 && !$parent->isDirectory()) {
                    throw new GenericYoukokException(
                        'Parent of ' . $element->id . ' should be all directories, but is ' . $parent->getType() . '.'
                    );
                }

                if ($index === 0 && !$parent->isCourse()) {
                    throw new GenericYoukokException(
                        'First parent of ' . $element->id . ' should be COURSE, but is ' . $parent->getType() . '.'
                    );
                }
            }

            if ($element->getType() !== Element::FILE) {
                throw new GenericYoukokException(
                    'Element ' . $element->id . ' should be FILE, but is ' . $element->getType()
                );
            }
        }

        if (in_array(static::FLAG_ENSURE_IS_COURSE, $flags)) {
            if (!$element->isCourse()) {
                throw new GenericYoukokException(
                    'Element ' . $element->id . ' should be COURSE, but is ' . $element->getType()
                );
            }
        }

        return $element;
    }

    public function getUriForElement(Element $element): string
    {
        if ($element->uri !== null) {
            return $element->uri;
        }

        if ($element->getType() === Element::COURSE && $element->slug !== null) {
            return $element->slug;
        }

        if (count($element->getParents()) == 0) {
            $element->setParents(
                    $this->fetchParents(
                    $element,
                    ['id', 'slug', 'parent', 'directory'],
                    []
                )
            );
        }

        $fragments = [];
        foreach ($element->getParents() as $parent) {
            $fragments[] = $parent->slug;
        }

        return implode('/', $fragments) . '/' . $element->slug;
    }

    public function getElementFromUri(string $uri, array $attributes = [], array $flags = []): Element
    {
        $this->validateFlags($flags);
        $attributes = static::supplementAttributesBasedOnFlags($attributes, $flags);

        // First, try to fetch using cache
        try {
            return $this->getElementFromUriCache($uri, $attributes, $flags);
        }
        catch (ElementNotFoundException $ex) {
            // To be expected
        }

        // Secondly, try to fetch, using the entire uri
        try {
            return $this->getElementFromOriginalUri($uri, $attributes, $flags);
        }
        catch (ElementNotFoundException $ex) {
            // To be expected
        }

        // Looks like we have to do this the hard way, e.i. looking up each fragment
        return $this->getElementFromUriFragments($uri, $attributes, $flags);
    }

    public function getVisibleParentForElement(Element $element): Element
    {
        $parent = Element
            ::where('id', $element->parent)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->first();

        if ($parent === null) {
            throw new ElementNotFoundException();
        }

        return $parent;
    }

    public function getNumberOfVisibleFiles(): int
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->count();
    }

    public function getNumberOfFilesThisMonth(): int
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->whereDate('added', '>=', Carbon::now()->subMonth())
            ->count();
    }

    public function getNewestElements(int $limit = 10): array
    {
        $elements = Element::select('id')
            ->where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->limit($limit)
            ->get();

        $newest = [];
        foreach ($elements as $element) {
            $newest[] = $this->getElement(
                new SelectStatements('id', $element->id),
                ['id', 'name', 'slug', 'uri', 'parent', 'checksum', 'link', 'added', 'directory', 'deleted'],
                [
                    static::FLAG_ENSURE_VISIBLE,
                    static::FLAG_FETCH_URI,
                    static::FLAG_FETCH_PARENTS
                ]
            );
        }

        return $newest;
    }

    public function getAllPending(): int
    {
        return Element::where('pending', 1)
            ->where('deleted', 0)
            ->whereNotNull('parent')
            ->orderBy('name')
            ->count();
    }

    public function getVisibleChildren(Element $element, int $order = self::SORT_TYPE_ORGANIZED): Collection
    {
        $query = Element::where('parent', $element->id)
            ->where('deleted', 0)
            ->where('pending', 0);

        if ($order === static::SORT_TYPE_ORGANIZED) {
            $query = $query->orderBy('directory', 'DESC')->orderBy('name', 'ASC');
        } else {
            $query = $query->orderBy('added', 'DESC');
        }

        return $query->get();
    }

    private function getElementFromUriCache(string $uri, array $attributes, array $flags): Element
    {
        $key = static::generateUriCacheKey($uri, $flags);

        $elementId = $this->cacheService->get($key);

        if ($elementId === null) {
            throw new ElementNotFoundException('No cache found for key ' . $key);
        }

        return $this->getElement(
            new SelectStatements('id', (int) $elementId),
            $attributes,
            $flags
        );
    }

    private function getElementFromOriginalUri(string $uri, array $attributes, array $flags): Element
    {
        // We only need to fetch the id there, the rest of the information is fetched in the second call
        $elementFromUriAttributes = static::supplementAttributesBasedOnFlags(['id'], $flags);

        $elementFromUri = $this->buildQuery(
            new SelectStatements('uri', UriCleaner::cleanUri($uri)),
            $elementFromUriAttributes,
            $flags
        );

        if ($elementFromUri === null) {
            throw new ElementNotFoundException('No element found with uri ' . $uri);
        }

        $element = $this->getElement(
            new SelectStatements('id', $elementFromUri->id),
            $attributes,
            $flags
        );

        // Store the uri lookup in the cache for later, important to do this _after_ all the security checks
        $key = static::generateUriCacheKey($uri, $flags);
        $this->cacheService->set($key, $element->id);

        return $element;
    }

    private function getElementFromUriFragments(string $uri, array $attributes, array $flags): Element
    {
        $fragments = UriCleaner::cleanFragments(explode('/', $uri));
        $currentParentId = null;
        $fragmentElement = null;

        foreach ($fragments as $fragment) {
            $selectStatements = new SelectStatements();
            $selectStatements->addStatement('slug', $fragment);
            $selectStatements->addStatement('parent', $currentParentId);

            $fragmentElementAttributes = static::supplementAttributesBasedOnFlags(['id'], $flags);
            $fragmentElement = $this->buildQuery($selectStatements, $fragmentElementAttributes, $flags);

            $currentParentId = $fragmentElement->id;
        }

        // Generate the cache key before messing with the list of flags
        $key = static::generateUriCacheKey($uri, $flags);

        // We can remove the flag FLAG_ENSURE_VISIBLE, because this is already checked. No point in validating this
        // twice
        if (in_array(static::FLAG_ENSURE_VISIBLE, $flags)) {
            unset($flags[static::FLAG_ENSURE_VISIBLE]);
        }

        $element = $this->getElement(
            new SelectStatements('id', $fragmentElement->id),
            $attributes,
            $flags
        );

        // Store the uri lookup in the cache for later, important to do this _after_ all the security checks
        $this->cacheService->set($key, $element->id);

        return $element;
    }

    private function fetchParents(Element $element, array $attributes, array $flags): array
    {
        $currentParentId = $element->parent;
        $parents = [];

        while (true) {
            $selectStatements = new SelectStatements('id', $currentParentId);

            $parent = null;
            try {
                $parent = $this->fetchElement($attributes, $selectStatements);
            }
            catch (ElementNotFoundException $ex) {
                // Rethrow exception
                throw new ElementNotFoundException(
                    'Could not find parent for Element id' . $element->id . ', parent id ' . $element->parent
                );
            }

            if ($parent->directory !== 1) {
                throw new ElementNotFoundException();
            }

            if (in_array(static::FLAG_ENSURE_VISIBLE, $flags)) {
                if (!$parent->isVisible()) {
                    throw new ElementNotFoundException();
                }
            }

            $parents[] = $parent;
            $currentParentId = $parent->parent;

            if ($currentParentId === null) {
                break;
            }
        }

        // Note: We need to change the order of the parents
        return array_reverse($parents);
    }

    private function buildQuery(SelectStatements $selectStatements, array $attributes, array $flags): ?Element
    {
        $element = $this->fetchElement($attributes, $selectStatements);

        // Can not fetch both visible and/or specific
        if (in_array(static::FLAG_ENSURE_VISIBLE, $flags)) {
            if (!$element->isVisible()) {
                throw new ElementNotFoundException();
            }
        }

        if (in_array(static::FLAG_ONLY_DIRECTORIES, $flags)) {
            if ($element->directory !== 1) {
                throw new ElementNotFoundException();
            }
        }

        return $element;
    }

    private function fetchElement(array $attributes, SelectStatements $selectStatements): Element
    {
        $element = null;
        if (ElementPool::contains($attributes, $selectStatements)) {
            $element = ElementPool::get($attributes, $selectStatements);
        }
        else {
            $query = Element::select($attributes);

            foreach ($selectStatements as $key => $value) {
                $query = $query->where($key, $value);
            }

            $element = $query->first();

            // Add to pool here, no need to do that later in this methdo, as it would re-add already existing
            // pool elements.
            if ($element !== null) {
                ElementPool::add(
                    new ElementPoolContainer(
                        $attributes,
                        $selectStatements,
                        $element
                    )
                );
            }
        }

        if ($element === null) {
            // Todo, better message here?
            throw new ElementNotFoundException();
        }

        return $element;
    }

    private function validateFlags(array $flags): void
    {
        if (in_array(static::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE, $flags)
            && in_array(static::FLAG_ONLY_DIRECTORIES, $flags)) {
            throw new InvalidFlagCombination('Can not fetch only files AND directories at the same time.');
        }

        // TODO validate more
    }

    private static function anyFlags(array $any, array $flags): bool
    {
        if (count($flags) === 0) {
            return false;
        }

        foreach ($any as $anyFlag) {
            if (in_array($anyFlag, $flags)) {
                return true;
            }
        }

        return false;
    }

    private static function supplementAttributesBasedOnFlags(array $attributes, array $flags): array
    {
        if (!in_array('uri', $attributes) && in_array(static::FLAG_FETCH_URI, $flags)) {
            $attributes[] = 'uri';
        }

        $flagsRequireParent = [
            static::FLAG_FETCH_PARENTS,
            static::FLAG_FETCH_URI,
            static::FLAG_ENSURE_VISIBLE,
            static::FLAG_FETCH_COURSE
        ];

        if (static::anyFlags($flagsRequireParent, $flags)) {
            if (!in_array('parent', $attributes)) {
                $attributes[] = 'parent';
            }

            if (!in_array('link', $attributes)) {
                $attributes[] = 'link';
            }

            if (!in_array('directory', $attributes)) {
                $attributes[] = 'directory';
            }
        }

        if (in_array(static::FLAG_ENSURE_VISIBLE, $flags)) {
            if (!in_array('deleted', $attributes)) {
                $attributes[] = 'deleted';
            }

            if (!in_array('pending', $attributes)) {
                $attributes[] = 'pending';
            }
        }

        if (in_array(static::FLAG_ONLY_DIRECTORIES, $flags)) {
            if (!in_array('directory', $attributes)) {
                $attributes[] = 'directory';
            }
        }

        if (in_array(static::FLAG_ENSURE_IS_COURSE, $flags)) {
            if (!in_array('directory', $attributes)) {
                $attributes[] = 'directory';
            }

            if (!in_array('parent', $attributes)) {
                $attributes[] = 'parent';
            }
        }

        return $attributes;
    }

    private static function generateUriCacheKey(string $uri, array $flags): string
    {
        if (in_array(static::FLAG_ONLY_DIRECTORIES, $flags)) {
            return CacheKeyGenerator::keyForVisibleUriDirectory($uri);
        }

        if (in_array(static::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE, $flags)) {
            return CacheKeyGenerator::keyForAllParentsAreDirectoriesExceptCurrentIsFile($uri);
        }

        throw new GenericYoukokException('Not implemented yet, sry');
    }

    // TODO: used by admin stuff
    public static function getAllCourses(): Collection
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('name')
            ->get();
    }

    // TODO: used by admin stuff
    public static function getAllNoneEmptyCourses(): Collection
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'deleted', 'pending')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('empty', 0)
            ->orderBy('name')
            ->get();
    }
}
