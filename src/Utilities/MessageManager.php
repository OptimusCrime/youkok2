<?php
/*
 * File: MessageManager.php
 * Holds: Keeps track of messages used in the system
 * Created: 28.11.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

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
     * Returns true if any message is queued
     */
    
    public static function hasMessages() {
        // Check if any messages
        if ((isset($_SESSION['youkok2_files']) and count($_SESSION['youkok2_files']) > 0) or 
            (isset($_SESSION['youkok2_message']) and count($_SESSION['youkok2_message']) > 0)) {
            return true;
        }
        
        // No messages
        return false;
    }
} 