<?php
/*
 * File: MessageManager.php
 * Holds: Keeps track of messages used in the system
 * Created: 28.11.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

use \Youkok2\Models\Message as Message;

class MessageManager {
    
    /*
     * Adds a message to the queue
     */
    
    public static function addMessage($msg, $type = 'danger', $prioritise = false) {
        if (!isset($_SESSION['youkok2_message'])) {
            $_SESSION['youkok2_message'] = [];
        }
        
        $append = true;
        
        // Handle priorities
        if (count($_SESSION['youkok2_message']) > 0 and $prioritise) {
            // Don't append
            $append = false;
            
            // Variables
            $added = false;
            $message_array = [];
            
            // Find out at what index to add the message
            foreach ($_SESSION['youkok2_message'] as $message) {
                // Check if we should store for later or do our insertion now
                if (!$message['prioritise'] and !$added) {
                    $message_array[] = array('text' => $msg, 'type' => $type, 'prioritise' => $prioritise);
                    
                    // Set added to true
                    $added = true;
                }
                
                // Add the current element
                $message_array[] = $message;
            }
            
            // Switch arrays
            $_SESSION['youkok2_message'] = $message_array;
        }
        
        // Check if we should just append the message
        if ($append) {
            // Append the message at the end of the message array
            $_SESSION['youkok2_message'][] = array('text' => $msg, 'type' => $type, 'prioritise' => $prioritise);
        }
    }
    
    /*
     * Adds a file message to the queue
     */
   
    public static function addFileMessage($name) {
        if (!isset($_SESSION['youkok2_files'])) {
            $_SESSION['youkok2_files'] = array();
        }
        
        $_SESSION['youkok2_files'][] = $name;
    }
    
    /*
     * Return the full array of messages
     */
    
    public static function get($query) {
        // Get messages
        $messages = Message::getMessages($query);
        
        // File message
        if (isset($_SESSION['youkok2_files']) and count($_SESSION['youkok2_files']) > 0) {
            $message_text = '';

            // Loop all files and make the message "pretty"
            foreach ($_SESSION['youkok2_files'] as $k => $v) {
                if (count($_SESSION['youkok2_files']) == 1) {
                    $message_text .= $v;
                }
                else if (count($_SESSION['youkok2_files']) == 2 and $k == 1) {
                    $message_text .= ' og ' . $v;
                }
                else {
                    if ((count($_SESSION['youkok2_files']) - 1) == $k) {
                        $message_text .= ' og ' . $v;
                    }
                    else {
                        $message_text .= ', ' . $v;
                    }
                }
            }
            
            // Remove the ugly part
            if (count($_SESSION['youkok2_files']) > 1) {
                $message_text = substr($message_text, 2);
            }
            
            // New message
            $message = new Message();
            $message->setMessage($message_text . ' ble lagt til. Takk for ditt bidrag!');
            $message->setType('success');
            
            // Unset the session variable
            unset($_SESSION['youkok2_files']);
            
            // Add to message array
            $messages[] = $message;
        }
        
        // Check for normal messages
        if (isset($_SESSION['youkok2_message']) and count($_SESSION['youkok2_message']) > 0) {
            // Loop the message array
            foreach ($_SESSION['youkok2_message'] as $v) {
                // New message
                $message = new Message();
                $message->setMessage($v['text']);
                $message->setType($v['type']);
                
                // Add to message array
                $messages[] = $message;
            }
            
            // Unset the session variable
            unset($_SESSION['youkok2_message']);
        }
        
        // Return the final array
        return $messages;
    }
} 