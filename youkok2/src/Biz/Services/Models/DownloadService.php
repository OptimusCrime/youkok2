<?php
namespace Youkok\Biz\Services\Models;

use DateTime;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;

use Illuminate\Support\Collection;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class DownloadService
{
    private ElementService $elementService;

    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    public function getNumberOfDownloads(): int
    {
        return Element::whereNotNull('parent')->sum('downloads_all');
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getLatestDownloads(int $limit): array
    {
        $downloads = Element::select(Element::ALL_FIELDS)
            ->where('deleted', false)
            ->where('pending', false)
            ->where('requested_deletion', false)
            ->whereNotNull('parent')
            ->whereNotNull('last_downloaded')
            ->orderBy('last_downloaded', 'DESC')
            ->limit($limit)
            ->get();

        $response = [];
        foreach ($downloads as $download) {
            $element = $this->elementService->getElement(
                new SelectStatements('id', $download->id),
                [
                    ElementService::FLAG_FETCH_COURSE
                ]
            );

            $response[] = $element;
        }

        return $response;
    }

    /**
     * @throws Exception
     */
    public function getMostPopularElementsFromDelta(MostPopularElement $delta): Collection
    {
        $query = Element::select(Element::ALL_FIELDS)
            ->where('deleted', false)
            ->where('pending', false)
            ->where('requested_deletion', false)
            ->whereNotNull('parent');

        switch ($delta->getValue()) {
            case MostPopularElement::DAY()->getValue():
                $query = $query->orderBy('downloads_today', 'DESC');
                break;
            case MostPopularElement::WEEK()->getValue():
                $query = $query->orderBy('downloads_week', 'DESC');
                break;
            case MostPopularElement::MONTH()->getValue():
                $query = $query->orderBy('downloads_month', 'DESC');
                break;
            case MostPopularElement::YEAR()->getValue():
                $query = $query->orderBy('downloads_year', 'DESC');
                break;
            case MostPopularElement::ALL()->getValue():
            default:
                $query = $query->orderBy('downloads_all', 'DESC');
                break;

        }

        return $query->get();
    }

    /**
     * @throws Exception
     */
    public function getMostPopularCursesFromDelta(MostPopularCourse $delta, int $limit): Collection
    {
        $query = Element::select(Element::ALL_FIELDS)
            ->where('deleted', false)
            ->where('pending', false)
            ->where('requested_deletion', false)
            ->whereNull('parent');

        switch ($delta->getValue()) {
            case MostPopularCourse::DAY()->getValue():
                $query = $query->orderBy('downloads_today', 'DESC');
                break;
            case MostPopularCourse::WEEK()->getValue():
                $query = $query->orderBy('downloads_week', 'DESC');
                break;
            case MostPopularCourse::MONTH()->getValue():
                $query = $query->orderBy('downloads_month', 'DESC');
                break;
            case MostPopularCourse::YEAR()->getValue():
                $query = $query->orderBy('downloads_year', 'DESC');
                break;
            case MostPopularCourse::ALL()->getValue():
            default:
                $query = $query->orderBy('downloads_all', 'DESC');
                break;
        }

        return $query
            ->limit($limit)
            ->get();
    }

    /**
     * @throws Exception
     */
    public function addDatabaseDownload(Element $element): void
    {
        $date = (new DateTime())->format('Y-m-d');

        DB::statement("
        INSERT INTO download
          (element, date, downloads)
        VALUES (" . $element->id . ", '" . $date . "', 1)
        ON CONFLICT (element, date) DO
        UPDATE SET
          downloads=download.downloads + 1
        ");
    }
}
