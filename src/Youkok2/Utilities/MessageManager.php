<?php
/*
 * File: MessageManager.php
 * Holds: Keeps track of messages used in the system
 * Created: 28.11.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

use Youkok2\Models\Message;

class MessageManager
{
    
    /*
     * Adds a message to the queue
     */
    
    public static function addMessage($app, $msg, $type = 'danger', $prioritise = false) {
        if ($app->getSession('youkok2_message') === null) {
            $app->setSession('youkok2_message', []);
        }

        // By default we do append the message to the current messages
        $append = true;

        // Get the messages
        $messages = $app->getSession('youkok2_message');
        
        // Handle priorities
        if (count($_SESSION['youkok2_message']) > 0 and $prioritise) {
            // Don't append
            $append = false;
            
            // Variables
            $added = false;
            $message_array = [];
            
            // Find out at what index to add the message
            foreach ($messages as $message) {
                // Check if we should store for later or do our insertion now
                if (!$message['prioritise'] and !$added) {
                    $message_array[] = ['text' => $msg, 'type' => $type, 'prioritise' => $prioritise];
                    
                    // Set added to true
                    $added = true;
                }
                
                // Add the current element
                $message_array[] = $message;
            }
            
            // Switch arrays
            $messages = $message_array;
        }
        
        // Check if we should just append the message
        if ($append) {
            // Append the message at the end of the message array
            $messages[] = ['text' => $msg, 'type' => $type, 'prioritise' => $prioritise];
        }

        // Update the session
        $app->setSession('youkok2_message', $messages);
    }
    
    /*
     * Adds a file message to the queue
     */
   
    public static function addFileMessage($app, $name) {
        if ($app->getSession('youkok2_files') === null) {
            $app->setSession('youkok2_files', []);
        }

        // Get the current messages;
        $messages = $app->getSession('youkok2_files');

        // Append the new message
        $messages[] = $name;

        // Store the new content
        $app->setSession('youkok2_files', $messages);
    }
    
    /*
     * Return the full array of messages
     */
    
    public static function get($app, $query) {
        // Get messages
        $messages = Message::getMessages($query);

        // Get the current file messages
        $file_messages = $app->getSession('youkok2_files');
        if (!is_array($file_messages)) {
            $file_messages = [];
        }

        // File message
        if (count($file_messages) > 0) {
            $message_text = '';

            // Loop all files and make the message "pretty"
            foreach ($file_messages as $k => $v) {
                if (count($file_messages) == 1) {
                    $message_text .= $v;
                }
                elseif (count($file_messages) == 2 and $k == 1) {
                    $message_text .= ' og ' . $v;
                }
                else {
                    if ((count($file_messages) - 1) == $k) {
                        $message_text .= ' og ' . $v;
                    }
                    else {
                        $message_text .= ', ' . $v;
                    }
                }
            }
            
            // Remove the ugly part
            if (count($file_messages) > 1) {
                $message_text = substr($message_text, 2);
            }
            
            // New message
            $message = new Message();
            $message->setMessage($message_text . ' ble lagt til. Takk for ditt bidrag!');
            $message->setType('success');
            
            // Unset the session variable
            $app->clearSession('youkok2_files');
            
            // Add to message array
            $messages[] = $message;
        }

        // Get the current file messages
        $regular_messages = $app->getSession('youkok2_files');
        if (!is_array($regular_messages)) {
            $regular_messages = [];
        }
        
        // Check for normal messages
        if (count($regular_messages) > 0) {
            // Loop the message array
            foreach ($regular_messages as $v) {
                // New message
                $message = new Message();
                $message->setMessage($v['text']);
                $message->setType($v['type']);
                
                // Add to message array
                $messages[] = $message;
            }
            
            // Unset the session variable
            $app->clearSession('youkok2_message');
        }
        
        // Return the final array
        return $messages;
    }
}
