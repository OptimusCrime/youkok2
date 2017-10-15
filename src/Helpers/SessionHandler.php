<?php
namespace Youkok\Helpers;

use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;
use Youkok\Models\Session;
use Youkok\Utilities\CookieHelper;

class SessionHandler
{
    const MODE_ADD = 0;
    const MODE_OVERWRITE = 1;

    const SESSION_TOKEN_LENGTH = 100;
    const SESSION_LIFE_TIME = 60 * 60 * 24 * 120;

    private $data;
    private $hash;
    private $dirty;

    public function __construct()
    {
        // This is the default session data array
        $this->data = [
            'latest_course_visited' => [],
            'favorites' => [],
            'frontpage' => [
                'most_popular_element' => MostPopularElement::MONTH,
                'most_popular_course' => MostPopularCourse::MONTH,
            ],
            'admin' => false
        ];

        $this->hash = null;
        $this->dirty = false;

        $this->loadSession();
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function loadSession()
    {
        $cookie = CookieHelper::getCookie('youkok2');
        if ($cookie === null) {
            return $this->createSession();
        }

        $currentSession = $this->getSession($cookie);

        if ($currentSession == null) {
            return $this->createSession();
        }

        $this->data = array_replace_recursive($this->data, json_decode($currentSession->data, true));
        $this->hash = $cookie;
    }

    private function getSession($hash)
    {
        return Session::where('hash', $hash)->first();
    }

    public function getData()
    {
        return $this->data;
    }

    public function getDataWithKey($key)
    {
        if (!isset($this->data[$key])) {
            return null;
        }

        return $this->data[$key];
    }

    public function setData($path, $value, $mode = self::MODE_ADD)
    {
        // Mark session as dirty
        $this->dirty = true;

        $pathFragments = explode('.', $path);
        $currentScope =& $this->data;

        // TODO split this method up in multiple smaller once
        for ($i = 0; $i < count($pathFragments); $i++) {
            $currentFragment = $pathFragments[$i];

            // If the fragment in the path does not exist, create it
            if (!isset($currentScope[$currentFragment])) {
                // Check if we are on the final element or if we should continue iterating
                if ($i === (count($pathFragments) - 1)) {
                    $this->insertData($currentScope, $currentFragment, $value, $mode);
                }

                $currentScope[$currentFragment] = [];
            }

            if ($i !== (count($pathFragments) - 1)) {
                $currentScope =& $currentScope[$currentFragment];
                continue;
            }

            $this->insertData($currentScope, $currentFragment, $value, $mode);
        }
    }

    private function insertData(array &$scope, $fragment, $value, $mode)
    {
        if (gettype($scope[$fragment]) === 'array') {
            if ($mode === static::MODE_OVERWRITE) {
                $scope[$fragment] = $value;
                return;
            }

            $scope[$fragment][] = $value;
            return;
        }

        $scope[$fragment] = $value;
    }

    public function store($force = false)
    {
        if (!$force and !$this->dirty) {
            return false;
        }

        $currentSession = $this->getSession($this->hash);
        if ($currentSession === null) {
            // This should never happen, log error
            $this->createSession();
            return false;
        }

        $currentSession->data = json_encode($this->data);
        $currentSession->save();

        return true;
    }

    public function forceSetData($path, $value, $mode = self::MODE_ADD)
    {
        $this->setData($path, $value, $mode);
        return $this->store(true);
    }

    private function createSession()
    {
        $this->hash = Utilities::randomToken(self::SESSION_TOKEN_LENGTH);

        $session = new Session();
        $session->hash = $this->hash;
        $session->data = json_encode($this->data);
        $session->save();

        CookieHelper::setCookie('youkok2', $this->hash, static::SESSION_LIFE_TIME);
    }
}
