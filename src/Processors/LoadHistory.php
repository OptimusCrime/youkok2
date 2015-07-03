<?php
/*
 * File: Module.php
 * Holds: Change module settings
 * Created: 11.01.15
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors;

/*
 * Define what classes to use
 */

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Utilities\Database as Database;

/*
 * The NotFound class, extending Base class
 */

class LoadHistory extends BaseProcessor {
    
    /*
     * Constructor
     */

    public function __construct($outputData = false, $returnData = false) {
        // Calling Base' constructor
        parent::__construct($outputData, $returnData);
        
        // Check database
        if (!$this->makeDatabaseConnection()) {
            $this->setError();
        }
    }
    
    /*
     * Load data
     */
    
    public function getHistory() {
        $ret = '';
        
        // Check if supplied correct data
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            $container = ElementCollection::get($_POST['id']);

            // Check if valid Element
            if ($container !== null) {
                // Get all history
                $history_get  = "SELECT h.history_text, u.nick FROM archive a" . PHP_EOL;
                $history_get .= "RIGHT JOIN history AS h ON a.id = h.file" . PHP_EOL;
                $history_get .= "RIGHT JOIN user AS u ON h.user = u.id" . PHP_EOL;
                $history_get .= "WHERE a.parent = :id" . PHP_EOL;
                $history_get .= "AND h.history_text IS NOT NULL" . PHP_EOL;
                $history_get .= "ORDER BY h.added DESC" . PHP_EOL;
                $history_get .= "LIMIT 30";
                
                $history_get_query = Database::$db->prepare($history_get);
                $history_get_query->execute(array(':id' => $_POST['id']));
                while ($row = $history_get_query->fetch(\PDO::FETCH_ASSOC)) {
                    $ret .= '<li class="list-group-item">' . str_replace('%u', (($row['nick'] == null or strlen($row['nick']) == 0) ? '<em>Anonym</em>' : $row['nick']), $row['history_text']) . '</li>';
                }

                // Check if no history
                if ($ret == '') {
                    $ret = '<li class="list-group-item"><em>Ingen historikk å vise.</em></li>';
                }
                
                // Set values
                $this->setData('code', 200);
                $this->setData('html', $ret);
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
        
        // Handle output
        if ($this->outputData) {
            $this->outputData();
        }
    }
}