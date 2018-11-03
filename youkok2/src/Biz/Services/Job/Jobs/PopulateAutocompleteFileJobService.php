<?php
namespace Youkok\Biz\Services\Job\Jobs;

use Youkok\Biz\Services\AutocompleteService;

class PopulateAutocompleteFileJobService implements JobServiceInterface
{
    private $autocompleteService;

    public function __construct(AutocompleteService $autocompleteService)
    {
        $this->autocompleteService = $autocompleteService;
    }

    public function run()
    {
        $this->autocompleteService->refresh();
    }
}