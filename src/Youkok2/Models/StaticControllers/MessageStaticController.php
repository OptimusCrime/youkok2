<?php
/*
 * File: MessageStaticController.php
 * Holds: Holds methods for the static Message class
 * Created: 10.05.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\models\StaticControllers;

use Youkok2\Models\Message;
use Youkok2\Utilities\Database;

class MessageStaticController {
    
    /*
     * Get all messages that matches a pattern
     */
    
    public static function getMessages($pattern) {
        $collection = [];
        
        // Get current messages
        $get_messages  = "SELECT *" . PHP_EOL;
        $get_messages .= "FROM message" . PHP_EOL;
        $get_messages .= "WHERE (" . PHP_EOL;
        $get_messages .= "    pattern = :pattern" . PHP_EOL;
        $get_messages .= "    OR pattern = '*')" . PHP_EOL;
        $get_messages .= "AND time_start <= NOW()" . PHP_EOL;
        $get_messages .= "AND time_end >= NOW()" . PHP_EOL;
        $get_messages .= "ORDER BY id DESC" . PHP_EOL;
        
        $get_messages_query = Database::$db->prepare($get_messages);
        $get_messages_query->execute(array(':pattern' => $pattern));
        while ($row = $get_messages_query->fetch(\PDO::FETCH_ASSOC)) {
            $collection[] = new Message($row);
        }
        
        // Return collection
        return $collection;
    }
}