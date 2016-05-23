<?php
/*
 * File: LoadDownloads.php
 * Holds: Loads the actual position for the last downloads
 * Created: 16.05.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Admin;

use Youkok2\Processors\BaseProcessor;
use Youkok2\Models\Me;
use Youkok2\Collections\ElementCollection;
use Youkok2\Utilities\Database;

class LoadDownloads extends BaseProcessor
{

    /*
     * Constructor
     */

    public function __construct($outputData = false, $returnData = false) {
        // Calling Base' constructor
        parent::__construct($outputData, $returnData);
        
        // Check database
        if ($this->makeDatabaseConnection()) {
            // Init user
            Me::init();
            
            // Check if online
            if (!Me::isLoggedIn() or (Me::isLoggedIn() and Me::isAdmin())) {
                $this->process();
            }
            else {
                $this->setError();
            }
        }
        else {
            $this->setError();
        }
        
        // Handle output
        if ($this->outputData) {
            $this->outputData();
        }
        if ($this->returnData) {
            return $this->returnData();
        }
    }
    
    /*
     * Process the request
     */
    
    private function process() {
        $this->setData('code', 200);
        
        $output = [];
        
        // Load downloads past 24 hours
        $get_last_downloads  = "SELECT a.id, d.ip, d.downloaded_time, d.agent, d.user" . PHP_EOL;
        $get_last_downloads .= "FROM archive AS a" . PHP_EOL;
        $get_last_downloads .= "LEFT JOIN download AS d ON a.id = d.file" . PHP_EOL;
        $get_last_downloads .= "WHERE d.downloaded_time >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY) AND a.is_visible = 1";
        
        $get_last_downloads_query = Database::$db->query($get_last_downloads);
        while ($row = $get_last_downloads_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $element = ElementCollection::get($row['id'], ['root']);

            // Check if valid Element
            if ($element !== null) {
                // Create temp array
                $temp_array = [
                    'element_id' => $element->getId(),
                    'element_name' => $element->getName(),
                    'url' => $element->controller->generateUrl('/emner'),
                    'download_ip' => $row['ip'],
                    'download_time' => $row['downloaded_time'],
                    'download_agent' => $row['agent'],
                    'download_user' => $row['user'],
                ];
                
                // Get location
                $ch = curl_init('http://api.ipinfodb.com/v3/ip-city/?key=d7f600a26086d65ee1417f9b62cc4533b226ca8a157b3e9b249ab3a02ea9cf8f&ip=' . $row['ip'] . '&format=json');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($ch);
                curl_close($ch);
                
                // Check if any data was returned
                if ($data !== null and strlen($data) > 0) {
                    $json_data = json_decode($data, true);
                    
                    if (isset($json_data['latitude']) and isset($json_data['longitude'])) {
                        // Add lat and lng to array
                        $temp_array['lat'] = (float) $json_data['latitude'];
                        $temp_array['lng'] = (float) $json_data['longitude'];
                    }
                }
                
                
                
                // Add to array
                $output[] = $temp_array;
            }
        }
        
        // Set data
        $this->setData('data', $output);
    }
}
