<?php
declare(strict_types=1);

namespace Youkok\Helpers;

use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;
use Youkok\Models\Session;
use Youkok\Helpers\Utilities;

class SessionHandler
{
    /**
     * TODO
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function loadCurrentSession() : array
    {
        if (!isset($_COOKIE['youkok2']) or strlen($_COOKIE['youkok2']) == 0) {
            return self::createSession();
        }

        $currentSession = Session::where('hash', $_COOKIE['youkok2'])->first();

        if ($currentSession == null) {
            return self::createSession();
        }

        return json_decode($currentSession->data);
    }

    private function createSession() : array
    {
        $sessionSettings = [
            'downloads' => [],
            'favorites' => [],
            'frontpage' => [
                'most_popular_element' => MostPopularElement::MONTH,
                'most_popular_course' => MostPopularCourse::MONTH,
            ]
        ];

        $sessionHash = Utilities::randomToken(100);

        $session = new Session();
        $session->hash = $sessionHash;
        $session->data = json_encode($sessionSettings);
        $session->save();

        $this->sessionData = $sessionSettings;

        setcookie('youkok2', $sessionHash, time() + (60 * 60 * 24 * 120));

        return $sessionSettings;
    }
}
