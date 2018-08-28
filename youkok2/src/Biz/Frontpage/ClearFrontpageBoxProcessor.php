<?php
namespace Youkok\Biz\Frontpage;

use \Psr\Http\Message\Request as Request;
use Youkok\Biz\Services\SessionService;

class ClearFrontpageBoxProcessor
{
    const PARAM_KEY_TYPE = 'type';
    const PARAM_VALUE_FAVORITE = 'favorites';
    const PARAM_VALUE_HISTORY = 'history';

    private $request;
    private $sessionHandler;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function fromRequest(Request $request)
    {
        return new ClearFrontpageBoxProcessor($request);
    }

    public function withSessionHandler(SessionService $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
        return $this;
    }

    public function run()
    {
        if (!static::isValidRequest($this->request)) {
            return [
                'code' => 500
            ];
        }

        if (static::clearFrontpageBox($this->sessionHandler, $this->request->getParams()[static::PARAM_KEY_TYPE])) {
            return [
                'code' => 200
            ];
        }

        // Unknown error (?)
        return [
            'code' => 500
        ];
    }

    private static function isValidRequest(Request $request)
    {
        if (!isset($request->getParams()[static::PARAM_KEY_TYPE])) {
            return false;
        }

        return in_array($request->getParams()[static::PARAM_KEY_TYPE], [
            static::PARAM_VALUE_FAVORITE,
            static::PARAM_VALUE_HISTORY
        ]);
    }

    private static function clearFrontpageBox(SessionService $sessionHandler, $type)
    {
        if ($type === static::PARAM_VALUE_HISTORY) {
            $sessionHandler->setData('latest_course_visited', [], SessionService::MODE_OVERWRITE);
        } else {
            $sessionHandler->setData('favorites', [], SessionService::MODE_OVERWRITE);
        }

        return $sessionHandler->store(true);
    }
}
