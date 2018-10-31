<?php
namespace Youkok\Biz\Services;

use Carbon\Carbon;

use Youkok\Common\Controllers\SessionController;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;
use Youkok\Common\Models\Session;
use Youkok\Common\Utilities\CookieHelper;
use Youkok\Helpers\Utilities;

class SessionService
{
    const MODE_ADD = 0;
    const MODE_OVERWRITE = 1;

    const SESSION_TOKEN_LENGTH = 100;
    const SESSION_LIFE_TIME = 60 * 60 * 24 * 120; // 120 days

    private $data;
    private $hash;
    private $dirty;

    public function init($loadSessions = true) {
        // This is the default session data array
        $this->data = [
            'most_popular_element' => MostPopularElement::MONTH,
            'most_popular_course' => MostPopularCourse::MONTH,
            'admin' => false
        ];

        $this->hash = null;
        $this->dirty = false;

        if ($loadSessions) {
            return $this->loadSession();
        }

        return true;
    }

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

        return true;
    }

    public function getSessionDataFromHash($hash)
    {
        $currentSession = $this->getSession($hash);
        if ($currentSession === null) {
            return $this->data;
        }

        return array_replace_recursive($this->data, json_decode($currentSession->data, true));
    }

    private function getSession($hash)
    {
        return Session::where('hash', $hash)->first();
    }

    public function getAllData()
    {
        return $this->data;
    }

    public function getData($key, $default = null)
    {
        if (!isset($this->data[$key])) {
            return $default;
        }

        return $this->data[$key];
    }

    public function isAdmin()
    {
        return isset($this->data['admin']) and $this->data['admin'];
    }

    public function setData($key, $value)
    {
        // Mark session as dirty
        $this->dirty = true;

        $this->data[$key] = $value;
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
        $currentSession->last_updated = Carbon::now();
        return $currentSession->save();
    }

    public function update()
    {
        $currentSession = $this->getSession($this->hash);
        if ($currentSession === null) {
            // It is more or less impossible that this happens...
            return false;
        }

        $currentSession->last_updated = Carbon::now();
        return $currentSession->save();
    }

    public function forceSetData($key, $value)
    {
        $this->setData($key, $value);

        return $this->store(true);
    }

    public function deleteExpiredSessions()
    {
        return SessionController::deleteExpiredSessions();
    }

    private function createSession()
    {
        $this->hash = Utilities::randomToken(self::SESSION_TOKEN_LENGTH);

        CookieHelper::setCookie('youkok2', $this->hash, static::SESSION_LIFE_TIME);

        $session = new Session();
        $session->hash = $this->hash;
        $session->data = json_encode($this->data);
        $session->last_updated = Carbon::now();
        $session->expire = Carbon::createFromTimestamp(time() + static::SESSION_LIFE_TIME);
        return $session->save();
    }
}
