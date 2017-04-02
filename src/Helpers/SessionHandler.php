<?php
declare(strict_types=1);

namespace Youkok\Helpers;

use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;
use Youkok\Models\Session;
use Youkok\Helpers\Utilities;

class SessionHandler
{
    const SESSION_TOKEN_LENGTH = 100;
    const SESSION_LIFE_TIME = (60 * 60 * 24 * 120);

    private $data;
    private $hash;
    private $dirty;

    public function __construct()
    {
        // This is the default session data array
        $this->data = [
            'downloads' => [],
            'favorites' => [],
            'frontpage' => [
                'most_popular_element' => MostPopularElement::MONTH,
                'most_popular_course' => MostPopularCourse::MONTH,
            ]
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
        if (!isset($_COOKIE['youkok2']) or strlen($_COOKIE['youkok2']) == 0) {
            return $this->createSession();
        }

        $currentSession = Session::where('hash', $_COOKIE['youkok2'])->first();

        if ($currentSession == null) {
            return $this->createSession();
        }

        // TODO merge the default array or something to make sure we have all the default values in case some values
        // were added after the session was created
        $this->data = json_decode($currentSession->data, true);
        $this->hash = $_COOKIE['youkok2'];
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(string $path, $value)
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
                    $this->insertData($currentScope, $currentFragment, $value);
                }

                $currentScope[$currentFragment] = [];
            }

            if ($i !== (count($pathFragments) - 1)) {
                $currentScope =& $currentScope[$currentFragment];
                continue;
            }

            $this->insertData($currentScope, $currentFragment, $value);
        }
    }

    private function insertData(array &$scope, string $fragment, $value)
    {
        if (gettype($scope[$fragment]) === 'array') {
            $scope[$fragment][] = $value;
            return;
        }

        $scope[$fragment] = $value;
    }

    public function store()
    {
        if (!$this->dirty) {
            return;
        }

        // Store the session
    }

    private function createSession(): array
    {
        $this->hash = Utilities::randomToken(self::SESSION_TOKEN_LENGTH);

        $session = new Session();
        $session->hash = $this->hash;
        $session->data = json_encode($this->data);
        $session->save();

        setcookie('youkok2', $this->hash, time() + self::SESSION_LIFE_TIME);
    }
}
