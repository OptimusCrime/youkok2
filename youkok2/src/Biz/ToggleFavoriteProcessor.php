<?php
namespace Youkok\Biz;

use Youkok\Biz\Services\SessionService;
use Youkok\Models\Element;
use Youkok\Utilities\ArrayHelper;

class ToggleFavoriteProcessor
{
    const ADD = 'add';
    const REMOVE = 'remove';

    private $id;
    private $mode;
    private $sessionHandler;

    private function __construct($id, $mode)
    {
        $this->id = $id;
        $this->mode = $mode;

        return $this;
    }

    public function withSessionHandler(SessionService $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
        return $this;
    }

    public function run()
    {
        if (!in_array($this->mode, [static::ADD, static::REMOVE])) {
            return false;
        }

        $element = static::getElementFromId($this->id);
        if ($element === null) {
            return false;
        }

        if ($this->mode === static::ADD) {
            return static::addFavorite($element, $this->sessionHandler);
        }

        return static::removeFavorite($element, $this->sessionHandler);
    }

    private static function addFavorite(Element $element, SessionService $sessionHandler)
    {
        $favorites = $sessionHandler->getDataWithKey('favorites');
        if (in_array($element->id, $favorites)) {
            return false;
        }

        $newFavorites = ArrayHelper::addToArray($favorites, $element->id);

        $sessionHandler->setData('favorites', $newFavorites, SessionService::MODE_OVERWRITE);

        return [
            'mode' => static::ADD
        ];
    }

    private static function removeFavorite(Element $element, SessionService $sessionHandler)
    {
        $favorites = $sessionHandler->getDataWithKey('favorites');
        if (!in_array($element->id, $favorites)) {
            return false;
        }

        $newFavorites = ArrayHelper::removeFromArray($favorites, $element->id);

        $sessionHandler->setData('favorites', $newFavorites, SessionService::MODE_OVERWRITE);

        return [
            'mode' => static::REMOVE
        ];
    }

    private static function getElementFromId($id)
    {
        if ($id === null or !is_numeric($id)) {
            return null;
        }

        $element = Element::fromId($id);
        if ($element === null) {
            return null;
        }

        return $element;
    }

    public static function fromData($id, $mode)
    {
        return new ToggleFavoriteProcessor($id, $mode);
    }
}
