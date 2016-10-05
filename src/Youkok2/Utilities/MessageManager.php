<?php
namespace Youkok2\Utilities;

use Youkok2\Models\Message;

class MessageManager
{

    public static function addMessage($app, $msg, $type = 'danger', $prioritise = false) {
        if ($app->getSession('youkok2_message') === null) {
            $app->setSession('youkok2_message', []);
        }

        $append = true;

        $messages = $app->getSession('youkok2_message');
        
        // Handle priorities
        if (count($messages) > 0 and $prioritise) {
            $append = false;
            
            $added = false;
            $message_array = [];
            
            foreach ($messages as $message) {
                // Check if we should store for later or do our insertion now
                if (!$message['prioritise'] and !$added) {
                    $message_array[] = ['text' => $msg, 'type' => $type, 'prioritise' => $prioritise];
                    $added = true;
                }
                
                $message_array[] = $message;
            }
            
            $messages = $message_array;
        }
        
        if ($append) {
            $messages[] = ['text' => $msg, 'type' => $type, 'prioritise' => $prioritise];
        }

        $app->setSession('youkok2_message', $messages);
    }

    public static function addFileMessage($app, $name) {
        if ($app->getSession('youkok2_files') === null) {
            $app->setSession('youkok2_files', []);
        }

        $messages = $app->getSession('youkok2_files');
        $messages[] = $name;

        $app->setSession('youkok2_files', $messages);
    }

    public static function get($app, $query) {
        $messages = Message::getMessages($query);

        $file_messages = $app->getSession('youkok2_files');
        if (!is_array($file_messages)) {
            $file_messages = [];
        }

        if (count($file_messages) > 0) {
            $message_text = '';
            
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
            
            if (count($file_messages) > 1) {
                $message_text = substr($message_text, 2);
            }
            
            $message = new Message();
            $message->setMessage($message_text . ' ble lagt til. Takk for ditt bidrag!');
            $message->setType('success');
            
            $app->clearSession('youkok2_files');
            
            $messages[] = $message;
        }

        $regular_messages = $app->getSession('youkok2_message');
        if (!is_array($regular_messages)) {
            $regular_messages = [];
        }
        
        if (count($regular_messages) > 0) {
            foreach ($regular_messages as $v) {
                $message = new Message();
                $message->setMessage($v['text']);
                $message->setType($v['type']);
                
                $messages[] = $message;
            }
            
            $app->clearSession('youkok2_message');
        }
        
        return $messages;
    }
}
