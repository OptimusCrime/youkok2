<?php
namespace Youkok\Biz\Services\Post\Create;

use Carbon\Carbon;
use Youkok\Biz\Exceptions\CreateException;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class CreateLinkService
{
    // Mirrored from frontend
    const MIN_VALID_URL_LENGTH = 4;
    const MIN_VALID_TITLE_LENGTH = 2;

    private $elementService;

    public function __construct(ElementService $elementService) {
        $this->elementService = $elementService;
    }

    public function run(int $parent, string $url, string $title): void
    {
        if (mb_strlen($url) < static::MIN_VALID_URL_LENGTH || mb_strlen($title) < static::MIN_VALID_TITLE_LENGTH) {
            throw new CreateException('Url or title is too short. Url: ' . $url . ', title: ' . $title);
        }

        $course = $this->elementService->getElement(
            new SelectStatements('id', $parent),
            ['id'],
            [
                ElementService::FLAG_ENSURE_VISIBLE,
                ElementService::FLAG_ENSURE_IS_COURSE
            ]
        );

        $newElement = new Element();
        $newElement->parent = $course->id;
        $newElement->name = $title;
        $newElement->link = $url;
        $newElement->pending = 1;
        $newElement->deleted = 0;
        $newElement->added = Carbon::now();

        $success = $newElement->save();

        if (!$success) {
            throw new CreateException('Failed to create a new link element');
        }
    }
}
