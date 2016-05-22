<?php
/*
 * File: Module.php
 * Holds: Change module settings
 * Created: 11.01.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Processors;

use Youkok2\Models\Element;
use Youkok2\Models\History;
use Youkok2\Utilities\Database;

class LoadHistory extends BaseProcessor
{
    
    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }
    
    /*
     * Override
     */

    protected function encodeData($data) {
        $new_data = [];

        // Loop the data array and run method on each element
        if (count($data['data']) > 0) {
            foreach($data['data'] as $v) {
                $new_data[] = $v->toArray();
            }
        }

        // Set new value
        $data['data'] = $new_data;

        // Return the updated array
        return $data;
    }
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Load data
     */
    
    public function run() {
        // For returning content
        $collection = [];
        
        // Check if supplied correct data
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            $element = Element::get($_POST['id']);

            // Check if valid Element
            if ($element->wasFound()) {
                // Get all history
                $history_get  = "SELECT h.history_text, h.added, u.nick" . PHP_EOL;
                $history_get .= "FROM archive a" . PHP_EOL;
                $history_get .= "RIGHT JOIN history AS h ON a.id = h.file" . PHP_EOL;
                $history_get .= "RIGHT JOIN user AS u ON h.user = u.id" . PHP_EOL;
                $history_get .= "WHERE a.parent = :id" . PHP_EOL;
                $history_get .= "AND h.history_text IS NOT NULL" . PHP_EOL;
                $history_get .= "ORDER BY h.added DESC" . PHP_EOL;
                
                $history_get_query = Database::$db->prepare($history_get);
                $history_get_query->execute(array(':id' => $_POST['id']));
                while ($row = $history_get_query->fetch(\PDO::FETCH_ASSOC)) {
                    // Init new History object
                    $history = new History($row);
                    
                    // Update the history text
                    $history->setHistoryText(str_replace('%u', (($row['nick'] == null or strlen($row['nick']) == 0) ? '<em>Anonym</em>' : $row['nick']), $row['history_text']));
                    
                    // Add to collection
                    $collection[] = $history;
                }
                
                // Set the data
                $this->setData('data', $collection);
                
                // Set ok
                $this->setOK();
            }
            else {
                // Invalid Element, return error
                $this->setError();
            }
        }
        else {
            // Id not provided, return error
            $this->setError();
        }
    }
}
