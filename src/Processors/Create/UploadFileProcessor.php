<?php
namespace Youkok\Processors\Create;

use \Psr\Http\Message\ServerRequestInterface as Request;

class UploadFileProcessor extends AbstractCreateProcessor
{
    public static function fromRequest(Request $request)
    {
        return new UploadFileProcessor($request);
    }

    public function run()
    {
        return [
            'code' => 200
        ];
    }
}