<?php
namespace Youkok\Mappers;

use Youkok\Processors\ToggleFavoriteProcessor;

class ToggleFavoriteMapper implements Mapper
{
    public static function map($obj)
    {
        if (static::isInvalidResponse($obj)) {
            return [
                'code' => 400
            ];
        }

        $messageText = 'Lagt til som favoritt.';
        if ($obj['mode'] === ToggleFavoriteProcessor::REMOVE) {
            $messageText = 'Fjernet fra favoritter.';
        }

        return [
            'code' => 200,
            'msg' => [
                'type' => 'success',
                'text' => $messageText
            ]
        ];
    }

    private static function isInvalidResponse($response)
    {
        if ($response === null or empty($response)) {
            return false;
        }

        if (!isset($response['mode'])) {
            return false;
        }

        return in_array($response['mode'], [ToggleFavoriteProcessor::ADD, ToggleFavoriteProcessor::REMOVE]);
    }
}
