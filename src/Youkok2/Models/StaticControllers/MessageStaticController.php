<?php
namespace Youkok2\models\StaticControllers;

use Youkok2\Models\Message;
use Youkok2\Utilities\Database;

class MessageStaticController
{
    
    public static function getMessages($pattern) {
        $regex = [
            '*' => '(.*)',
            '/' => '\/',
        ];
        
        $collection = [];
        
        $get_messages  = "SELECT *" . PHP_EOL;
        $get_messages .= "FROM message" . PHP_EOL;
        $get_messages .= "WHERE time_start <= CURRENT_TIMESTAMP" . PHP_EOL;
        $get_messages .= "AND time_end >= CURRENT_TIMESTAMP" . PHP_EOL;
        $get_messages .= "ORDER BY id DESC" . PHP_EOL;
        
        $get_messages_query = Database::$db->query($get_messages);
        while ($row = $get_messages_query->fetch(\PDO::FETCH_ASSOC)) {
            if ($row['pattern'] == '*') {
                $collection[] = new Message($row);
            }
            else {
                if ($row['pattern'] === $pattern) {
                    $collection[] = new Message($row);
                }
                else {
                    $regex_pattern = '/^';
                    $regex_pattern .= str_replace(
                        array_keys($regex),
                        array_values($regex),
                        $row['pattern']
                    );
                    $regex_pattern .= '/';
                    
                    if (preg_match($regex_pattern, $pattern)) {
                        $collection[] = new Message($row);
                    }
                }
            }
        }
        
        return $collection;
    }
}
