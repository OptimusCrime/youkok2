<?php
namespace Youkok\Biz\Services;

use Monolog\Logger;
use Youkok\Biz\Exceptions\CookieNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Biz\Services\Models\SessionService;
use Youkok\Common\Models\Session;
use Youkok\Common\Utilities\CookieHelper;

class UserSessionService
{
    private $sessionService;
    private $logger;

    /** @var Session */
    private $session;

    public function __construct(SessionService $sessionService, Logger $logger)
    {
        $this->sessionService = $sessionService;
        $this->logger = $logger;

        $this->session = $this->loadSession();
    }

    private function loadSession(): ?Session
    {
        try {
            $hash = CookieHelper::getCookie('youkok2');
            return $this->sessionService->get($hash);
        } catch (CookieNotFoundException $exception) {
            // There is no need for a session if the script is called from the command line
            if (php_sapi_name() === 'cli') {
                return null;
            }

            return $this->createSession();
        } catch (SessionNotFoundException $exception) {
            return $this->createSession();
        }
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function isAdmin(): bool
    {
        return $this->session->isAdmin();
    }

    public function store(): bool
    {
        return $this->session->save();
    }

    private function createSession(): Session
    {
        try {
            return $this->sessionService->create();
        } catch (GenericYoukokException $ex) {
            $this->logger->warning('Failed to create session.', $ex->getTrace());

            // Use a dummy session
            return new Session();
        }
    }
}
