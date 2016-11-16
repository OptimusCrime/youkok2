<?php
namespace Youkok2\Processors;

class NotFound extends BaseProcessor
{

    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        $this->setData('msg', 'Processor not found');
        $this->setData('code', 500);
    }
}
