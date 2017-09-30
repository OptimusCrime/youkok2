<?php
namespace Youkok\Views\Processors\Create;

use Youkok\Views\Processors\BaseProcessorView;

class CreateLink extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        return $this->output($response, ['foo' => 'bar']);
    }
}
