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

class MessageStaticController 
{
    
    /*
     * Get all messages that matches a pattern
     */
    
    public static function getMessages($pattern) {
        // Regex expressions
        $regex = [
            '*' => '(.*)',
            '/' => '\/',
        ];
        
        // For storing the final collection of messages
        $collection = [];
        
        // Get current messages
        $get_messages  = "SELECT *" . PHP_EOL;
        $get_messages .= "FROM message" . PHP_EOL;
        $get_messages .= "WHERE time_start <= CURRENT_TIMESTAMP" . PHP_EOL;
        $get_messages .= "AND time_end >= CURRENT_TIMESTAMP" . PHP_EOL;
        $get_messages .= "ORDER BY id DESC" . PHP_EOL;
        
        $get_messages_query = Database::$db->query($get_messages);
        while ($row = $get_messages_query->fetch(\PDO::FETCH_ASSOC)) {
            // If the pattern is simply * it is universal
            if ($row['pattern'] == '*') {
                $collection[] = new Message($row);
            }
            else {
                // Check if we can just match directly
                if ($row['pattern'] === $pattern) {
                    $collection[] = new Message($row);
                }
                else {
                    // Change pattern into regex expression
                    $regex_pattern = '/^' . str_replace(array_keys($regex), array_values($regex), $row['pattern']) . '/';
                    
                    // Check if the pattern matches
                    if (preg_match($regex_pattern, $pattern)) {
                        $collection[] = new Message($row);
                    }
                }
            }
        }
        
        // Return collection
        return $collection;
    }
}
