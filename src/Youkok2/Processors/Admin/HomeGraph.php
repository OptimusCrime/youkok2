<?php
/*
 * File: HomeGraph.php
 * Holds: Loads the information for the home graph
 * Created: 16.12.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Admin;

use Youkok2\Processors\BaseProcessor;
use Youkok2\Models\Me;
use Youkok2\Collections\ElementCollection;
use Youkok2\Utilities\Database;

class HomeGraph extends BaseProcessor {

    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }
    
    /*
     * Override
     */

    protected function checkPermissions() {
        return $this->requireAdmin();
    }
    
    /*
     * Constructor
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }
    
    /*
     * Method ran by the processor
     */
    
    public function run() {
        // Some variables
        $response = [];
        $response['graph'] = [];
        $response['delta'] = '';
        
        // Build empty graph plots
        for ($i = 30; $i >= 0; $i--) {
            $date_offset = strtotime('-' . $i . ' days');
            
            // Create the delta
            if ($i == 30) {
                $response['delta'] = date('j. M Y', $date_offset);
            }
            else if ($i == 0) {
                $response['delta'] .= ' &mdash; ' . date('j. M Y', $date_offset);
            }
            
            $response['graph'][] = [
                'date' => date('Y-m-d', $date_offset),
                'downloads' => 0
            ];
            
        }
        
        // The query
        $get_all_downloads  = "SELECT COUNT(id) AS 'downloads', downloaded_time" . PHP_EOL;
        $get_all_downloads .= "FROM download" . PHP_EOL;
        $get_all_downloads .= "WHERE downloaded_time >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH)" . PHP_EOL;
        $get_all_downloads .= "GROUP BY TO_DAYS(downloaded_time)" . PHP_EOL;
        $get_all_downloads .= "ORDER BY downloaded_time ASC" . PHP_EOL;
        
        $get_all_downloads_query = Database::$db->query($get_all_downloads);
        while ($row = $get_all_downloads_query->fetch(\PDO::FETCH_ASSOC)) {
            $date = explode(' ', $row['downloaded_time'])[0];
            
            for ($i = 0; $i <= 30; $i++) {
                if ($date == $response['graph'][$i]['date']) {
                    $response['graph'][$i]['downloads'] = (int) $row['downloads'];
                }
            }
        }
        
        // Set result
        $this->setData('data', $response);
        
        // Set ok
        $this->setOK();
    }
} 