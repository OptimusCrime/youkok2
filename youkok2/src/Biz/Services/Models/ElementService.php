<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use RedisException;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\CacheService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Common\Utilities\UriCleaner;

class ElementService
{
    const int SORT_TYPE_ORGANIZED = 0;
    const int SORT_TYPE_AGE = 1;

    const string FLAG_FETCH_PARENTS = 'FLAG_FETCH_PARENTS';
    const string FLAG_FETCH_COURSE = 'FLAG_FETCH_COURSE';
    const string FLAG_FETCH_URI = 'FLAG_FETCH_URI';

    // This flag will force visible check on all parents, even if FLAG_FETCH_PARENTS is not used
    const string FLAG_ENSURE_VISIBLE = 'FLAG_ENSURE_VISIBLE';

    // This flag will force check to ensure that all the parents are directories, and current element is a file
    const string FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE =
        'FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE';

    const string FLAG_ENSURE_IS_COURSE = 'FLAG_ENSURE_IS_COURSE';

    const string FLAG_ONLY_DIRECTORIES = 'FLAG_ONLY_DIRECTORIES';

    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function getElement(SelectStatements $selectStatements, array $flags = []): Element
    {
        $this->validateFlags($flags);

        $element = $this->buildQuery($selectStatements, $flags);

        if ($element->parent === null) {
            // .parent was fetched, but was `null` indicating that the current Element is a course
            $element->setParents([]);
        } else {
            // If any of these flags are sat, fetch parents
            $fetchParentsFlags = [
                static::FLAG_FETCH_PARENTS,
                static::FLAG_ENSURE_VISIBLE,
                static::FLAG_FETCH_COURSE,
                static::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE
            ];
            if (static::anyFlags($fetchParentsFlags, $flags)) {
                $parents = $this->fetchParents($element, $flags);

                $element->setParents($parents);
            }
        }

        if (in_array(static::FLAG_FETCH_URI, $flags)
            && $element->uri === null
            && $element->getType() !== Element::LINK
        ) {
            // If the current type is course, we can just use the slug
            if ($element->getType() === Element::COURSE) {
                $element->uri = $element->slug;
            } else {
                $element->uri = $this->getUriForElement($element);
            }
        }

        if (in_array(static::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE, $flags)) {
            if (count($element->getParents()) === 0) {
                throw new Exception('No parents loaded for verification.');
            }

            foreach ($element->getParents() as $index => $parent) {
                // First child will be identified as a COURSE
                if ($index > 0 && !$parent->isDirectory()) {
                    throw new Exception(
                        'Parent of ' . $element->id . ' should be all directories, but is ' . $parent->getType() . '.'
                    );
                }

                if ($index === 0 && !$parent->isCourse()) {
                    throw new (
                        'First parent of ' . $element->id . ' should be COURSE, but is ' . $parent->getType() . '.'
                    );
                }
            }

            if ($element->getType() !== Element::FILE) {
                throw new Exception(
                    'Element ' . $element->id . ' should be FILE, but is ' . $element->getType()
                );
            }
        }

        if (in_array(static::FLAG_ENSURE_IS_COURSE, $flags)) {
            if (!$element->isCourse()) {
                throw new Exception(
                    'Element ' . $element->id . ' should be COURSE, but is ' . $element->getType()
                );
            }
        }

        return $element;
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getUriForElement(Element $element, bool $forceFetchFromParentSlugFragments = false): string
    {
        if ($element->uri !== null && !$forceFetchFromParentSlugFragments) {
            return $element->uri;
        }

        if ($element->getType() === Element::COURSE && $element->slug !== null) {
            return $element->slug;
        }

        if (count($element->getParents()) == 0) {
            $element->setParents(
                $this->fetchParents(
                    $element,
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

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function getElementFromUri(string $uri, array $flags = []): Element
    {
        $this->validateFlags($flags);

        // First, try to fetch using cache
        try {
            return $this->getElementFromUriCache($uri, $flags);
        } catch (ElementNotFoundException $ex) {
            // To be expected
        }

        // Secondly, try to fetch, using the entire uri
        try {
            return $this->getElementFromOriginalUri($uri, $flags);
        } catch (ElementNotFoundException $ex) {
            // To be expected
        }

        // Looks like we have to do this the hard way, e.i. looking up each fragment
        return $this->getElementFromUriFragments($uri, $flags);
    }

    /**
     * @throws ElementNotFoundException
     * @throws RedisException
     */
    public function getVisibleParentForElement(Element $element): Element
    {
        $key = CacheKeyGenerator::keyForElementParent($element->id);
        $cache = $this->cacheService->get($key);
        if ($cache !== null) {
            return new Element(json_decode($cache, true));
        }

        $parent = Element
            ::where('id', $element->parent)
            ->where('deleted', false)
            ->where('pending', false)
            ->first();

        if ($parent === null) {
            throw new ElementNotFoundException();
        }

        $this->cacheService->set($key, $parent);

        return $parent;
    }

    public function getNumberOfVisibleFiles(): int
    {
        return Element
            ::where('directory', false)
            ->where('deleted', false)
            ->where('pending', false)
            ->count();
    }

    public function getNumberOfFilesThisMonth(): int
    {
        return Element
            ::where('directory', false)
            ->where('deleted', false)
            ->where('pending', false)
            ->whereDate('added', '>=', Carbon::now()->subMonth())
            ->count();
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getNewestElements(int $limit = 10): array
    {
        $elements = Element::select('id')
            ->where('directory', false)
            ->where('pending', false)
            ->where('deleted', false)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->limit($limit)
            ->get();

        $newest = [];
        foreach ($elements as $element) {
            $newest[] = $this->getElement(
                new SelectStatements('id', $element->id),
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
        return Element::where('pending', true)
            ->where('deleted', false)
            ->whereNotNull('parent')
            ->orderBy('name')
            ->count();
    }

    public function getVisibleChildren(Element $element, int $order = self::SORT_TYPE_ORGANIZED): Collection
    {
        $query = Element::where('parent', $element->id)
            ->where('deleted', false)
            ->where('pending', false);

        if ($order === static::SORT_TYPE_ORGANIZED) {
            $query = $query->orderBy('directory', 'DESC')->orderBy('name', 'ASC');
        } else {
            $query = $query->orderBy('added', 'DESC');
        }

        return $query->get();
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     * @throws RedisException
     */
    private function getElementFromUriCache(string $uri, array $flags): Element
    {
        $key = static::generateUriCacheKey($uri, $flags);

        $elementId = $this->cacheService->get($key);

        if ($elementId === null) {
            throw new ElementNotFoundException('No cache found for key ' . $key);
        }

        return $this->getElement(
            new SelectStatements('id', (int) $elementId),
            $flags
        );
    }

    /**
     * @throws ElementNotFoundException
     * @throws RedisException
     * @throws Exception
     */
    private function getElementFromOriginalUri(string $uri, array $flags): Element
    {
        $elementFromUri = $this->buildQuery(
            new SelectStatements('uri', UriCleaner::cleanUri($uri)),
            $flags
        );

        if ($elementFromUri === null) {
            throw new ElementNotFoundException('No element found with uri ' . $uri);
        }

        $element = $this->getElement(
            new SelectStatements('id', $elementFromUri->id),
            $flags
        );

        // Store the uri lookup in the cache for later, important to do this _after_ all the security checks
        $key = static::generateUriCacheKey($uri, $flags);
        $this->cacheService->set($key, $element->id);

        return $element;
    }

    /**
     * @throws ElementNotFoundException
     * @throws RedisException
     * @throws Exception
     */
    private function getElementFromUriFragments(string $uri, array $flags): Element
    {
        $fragments = UriCleaner::cleanFragments(explode('/', $uri));
        $currentParentId = null;
        $fragmentElement = null;

        foreach ($fragments as $fragment) {
            $selectStatements = new SelectStatements();
            $selectStatements->addStatement('slug', $fragment);
            $selectStatements->addStatement('parent', $currentParentId);

            $fragmentElement = $this->buildQuery($selectStatements, $flags);

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
            $flags
        );

        // Store the uri lookup in the cache for later, important to do this _after_ all the security checks
        $this->cacheService->set($key, $element->id);

        return $element;
    }

    /**
     * @throws ElementNotFoundException
     */
    private function fetchParents(Element $element, array $flags): array
    {
        $currentParentId = $element->parent;
        $parents = [];

        while (true) {
            $selectStatements = new SelectStatements('id', $currentParentId);

            $parent = null;
            try {
                $parent = $this->fetchElement($selectStatements);
            } catch (ElementNotFoundException $ex) {
                // Rethrow exception
                throw new ElementNotFoundException(
                    'Could not find parent for Element id' . $element->id . ', parent id ' . $element->parent
                );
            }

            if (!$parent->directory) {
                throw new ElementNotFoundException(
                    'Expected to find directory for parent, was: ' . $parent->getType() . ' for ID: ' . $parent->id
                );
            }

            if (in_array(static::FLAG_ENSURE_VISIBLE, $flags)) {
                if (!$parent->isVisible()) {
                    throw new ElementNotFoundException('Expected to find visible for parent for ID: ' . $parent->id);
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

    /**
     * @throws ElementNotFoundException
     */
    private function buildQuery(SelectStatements $selectStatements, array $flags): ?Element
    {
        $element = $this->fetchElement($selectStatements);

        // Can not fetch both visible and/or specific
        if (in_array(static::FLAG_ENSURE_VISIBLE, $flags)) {
            if (!$element->isVisible()) {
                throw new ElementNotFoundException('Expected to find visible for element for ID: ' . $element->id);
            }
        }

        if (in_array(static::FLAG_ONLY_DIRECTORIES, $flags)) {
            if (!$element->directory) {
                throw new ElementNotFoundException(
                    'Expected to find directory for element, was: ' . $element->getType() . ' for ID: ' . $element->id
                );
            }
        }

        return $element;
    }

    /**
     * @throws ElementNotFoundException
     */
    private function fetchElement(SelectStatements $selectStatements): Element
    {
        $query = Element::select(Element::ALL_FIELDS);

        foreach ($selectStatements as $key => $value) {
            $query = $query->where($key, $value);
        }

        $element = $query->first();

        if ($element === null) {
            throw new ElementNotFoundException(
                'Could not fetch element. SelectStatements: ' . $selectStatements->__toString()
            );
        }

        return $element;
    }

    /**
     * @throws Exception
     */
    private function validateFlags(array $flags): void
    {
        if (in_array(static::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE, $flags)
            && in_array(static::FLAG_ONLY_DIRECTORIES, $flags)) {
            throw new Exception('Can not fetch only files AND directories at the same time.');
        }
    }

    public static function createSlug(string $fileName): string
    {
        // Replace first here to keep "norwegian" names in a way
        $fileName = str_replace(['Æ', 'Ø', 'Å'], ['ae', 'o', 'aa'], $fileName);
        $fileName = str_replace(['æ', 'ø', 'å'], ['ae', 'o', 'aa'], $fileName);

        // Replace multiple spaces to dashes and remove special chars
        $fileName = preg_replace('!\s+!', '-', $fileName);

        // Remove all but last dot
        $fileName = preg_replace('/\.(?![^.]+$)|[^-_a-zA-Z0-9\s]$/', '-', $fileName);

        // Remove multiple dashes in a row
        $fileName = preg_replace('!-+!', '-', $fileName);

        // Remove all but these characters
        return strtolower(preg_replace('![^-_a-zA-Z0-9\s.]+!', '', $fileName));
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

    /**
     * @throws Exception
     */
    private static function generateUriCacheKey(string $uri, array $flags): string
    {
        if (in_array(static::FLAG_ONLY_DIRECTORIES, $flags)) {
            return CacheKeyGenerator::keyForVisibleUriDirectory($uri);
        }

        if (in_array(static::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE, $flags)) {
            return CacheKeyGenerator::keyForAllParentsAreDirectoriesExceptCurrentIsFile($uri);
        }

        throw new Exception('Not implemented yet, sry');
    }
}
