<?php
namespace Youkok2\Models\StaticControllers;

use Youkok2\Youkok2;
use Youkok2\Models\Element;
use Youkok2\Utilities\Database;

class ElementStaticController
{

    private static $collection = [];

    public static function get($data = null) {
        if (is_numeric($data)) {
            if (!isset(self::$collection[$data])) {
                $element = new Element($data);

                self::$collection[$data] = &$element;
            }

            return self::$collection[$data];
        }
        else {
            return new Element($data);
        }
    }
    
    public static function getNewest($limit = 15) {
        $collection = [];
        
        $get_newest  = "SELECT id" . PHP_EOL;
        $get_newest .= "FROM archive" . PHP_EOL;
        $get_newest .= "WHERE directory = 0" . PHP_EOL;
        $get_newest .= "AND pending = 0" . PHP_EOL;
        $get_newest .= "AND deleted = 0" . PHP_EOL;
        $get_newest .= "ORDER BY added DESC, name DESC" . PHP_EOL;
        $get_newest .= "LIMIT " . $limit;
        
        $get_newest_query = Database::$db->query($get_newest);
        while ($row = $get_newest_query->fetch(\PDO::FETCH_ASSOC)) {
            $collection[] = Element::get($row['id']);
        }
        
        return $collection;
    }
    
    public static function getLastVisitedElements($limit = 15) {
        $collection = [];
        
        $get_last_visited  = "SELECT id" . PHP_EOL;
        $get_last_visited .= "FROM archive" . PHP_EOL;
        $get_last_visited .= "WHERE last_visited IS NOT NULL" . PHP_EOL;
        $get_last_visited .= "AND pending = 0" . PHP_EOL;
        $get_last_visited .= "AND deleted = 0" . PHP_EOL;
        $get_last_visited .= "ORDER BY last_visited DESC" . PHP_EOL;
        $get_last_visited .= "LIMIT " . $limit;
        
        $get_last_visited_query = Database::$db->query($get_last_visited);
        while ($row = $get_last_visited_query->fetch(\PDO::FETCH_ASSOC)) {
            $collection[] = Element::get($row['id']);
        }
        
        return $collection;
    }
}
