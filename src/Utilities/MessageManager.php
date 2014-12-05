<?php
/*
 * File: MessageManager.php
 * Holds: Keeps track of messages used in the system
 * Created: 28.11.2014
 * Project: Youkok2
 */

namespace Youkok2\Utilities;

/*
 * Class that keeps track of messages queued
 */

class MessageManager {
    
    /*
     * Adds a message to the queue
     */
    
    public static function addMessage($msg, $type = 'danger') {
        if (!isset($_SESSION['youkok2_message'])) {
            $_SESSION['youkok2_message'] = array();
        }
        
        $_SESSION['youkok2_message'][] = array('text' => $msg, 'type' => $type);
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