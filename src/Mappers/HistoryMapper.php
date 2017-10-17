<?php
namespace Youkok\Mappers;

use Youkok\Models\Element;

class HistoryMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        $messages = [];
        foreach ($obj as $element) {
            $messages[] = [
                'history_text' => static::getHistoryText($element),
                'added' => $element->added
            ];
        }

        return [
            'code' => 200,
            'data' => $messages
        ];
    }

    private static function getHistoryText(Element $element)
    {
        if ($element->directory === 1) {
            return $element->name . ' ble opprettet.';
        }

        if ($element->link !== null) {
            return $element->name . ' ble postet.';
        }

        return $element->name . ' ble lastet opp.';
    }
}
