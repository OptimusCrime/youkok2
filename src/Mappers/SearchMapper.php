<?php
namespace Youkok\Mappers;

use Youkok\Models\Element;

class SearchMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        if (count($obj) === 0) {
            return [];
        }

        foreach ($obj as $element) {
            $element->name = static::highlightElementName($element, $data);
        }

        return $obj;
    }

    private static function highlightElementName(Element $element, array $regexPermutations)
    {
        $matchingPattern = static::findMatchingPatternForHighlighting($element, $regexPermutations);
        if ($matchingPattern === null) {
            return null;
        }

        return static::applySearchHighlightForName($element->name, $matchingPattern);
    }


    private static function applySearchHighlightForName($name, $matchingPattern)
    {
        $expressions = static::cleanRegexPatternForHighlight($matchingPattern);
        foreach ($expressions as $expression) {
            $completeRegexExpression = static::convertToCompleteRegexExpression($expression);

            preg_match_all($completeRegexExpression, $name, $matches, PREG_SET_ORDER, 0);

            if (empty($matches)) {
                continue;
            }

            foreach ($matches as $match) {
                if (!isset($match['match'])) {
                    continue;
                }

                $highlightExpression = static::convertToHighlight($match['match']);
                $name = str_replace($match['match'], $highlightExpression, $name);
            }
        }

        return static::cleanAndConvertToHighlight($name);
    }

    private static function cleanAndConvertToHighlight($name)
    {
        $name = static::cleanNestedTokens($name);
        $name = str_replace('$', '<strong>', $name);
        $name = str_replace('#', '</strong>', $name);
        return $name;
    }

    private static function cleanNestedTokens($name)
    {
        $newName = [];
        $index = 0;
        $closures = 0;
        while ($index < strlen($name)) {
            $token = substr($name, $index, 1);
            if ($token === '$') {
                $closures++;
                if ($closures === 1) {
                    $newName[] = '$';
                }
                $index += 1;
                continue;
            }
            if ($token === '#') {
                $closures--;
                if ($closures === 0) {
                    $newName[] = '#';
                }
                $index += 1;
                continue;
            }

            $newName[] = $token;

            $index++;
        }

        return implode('', $newName);
    }

    private static function convertToHighlight($expression)
    {
        return '$' . $expression . '#';
    }

    private static function convertToCompleteRegexExpression($expression)
    {
        return '/(?P<match>' . $expression . ')/i';
    }

    private static function cleanRegexPatternForHighlight($pattern)
    {
        $patternSplit = explode('||', $pattern);
        return static::iterateRegexPattern($patternSplit);
    }

    private static function iterateRegexPattern(array $patterns)
    {
        $cleaned = [];
        foreach ($patterns as $v) {
            $splitWildcard = explode('%', $v);
            if (count($splitWildcard) === 0) {
                continue;
            }

            foreach ($splitWildcard as $iv) {
                if (strlen($iv) > 0) {
                    $cleaned[] = $iv;
                }
            }
        }

        return $cleaned;
    }

    private static function findMatchingPatternForHighlighting(Element $element, array $regexPermutations)
    {
        foreach ($regexPermutations as $permutation) {
            $regex = static::applySearchHighlightForExpression($element, $permutation);
            preg_match_all($regex, $element->name, $matches);
            if (isset($matches[0]) and isset($matches[0])) {
                return $permutation;
            }

        }

        return null;
    }

    private static function applySearchHighlightForExpression(Element $element, $expression)
    {
        $expressionSplit = explode('||', $expression);
        if (count($expressionSplit) !== 2) {
            return null;
        }

        return '/^' . static::replaceWildcardToken($expressionSplit[0]) . '\|\|' . static::replaceWildcardToken($expressionSplit[1]) . '$/i';
    }

    private static function replaceWildcardToken($string)
    {
        return str_replace('%', '.*', $string);
    }
}
