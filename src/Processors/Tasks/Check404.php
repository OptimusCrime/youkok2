<?php
/*
 * File: Check404.php
 * Holds: Checks if an element generated 404 error
 * Created: 17.12.14
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\Base as Base;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;

/*
 * LoadCourses extending Base
 */

class Check404 extends Base {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->makeDatabaseConnection()) {
                $this->analyze();
                
                // Close database connection
                Database::close();
            }
        }
        else {
            // No access
            $this->noAccess();
        }
        
        // Return data
        $this->returnData();
    }

    /*
     * Fetch the actual data here
     */

    private function analyze() {
        $success_num = 0;
        $error_num = 0;
        
        $success = '';
        $error = '';
        
        // Search all files in the system
        $get_all_items  = "SELECT id" . PHP_EOL;
        $get_all_items .= "FROM archive" . PHP_EOL;
        $get_all_items .= "WHERE is_directory = 0" . PHP_EOL;
        $get_all_items .= "AND url IS NULL" . PHP_EOL;
        $get_all_items .= "ORDER BY id DESC";
        
        $get_all_items_query = Database::$db->prepare($get_all_items);
        $get_all_items_query->execute();
        while ($row = $get_all_items_query->fetch(\PDO::FETCH_ASSOC)) {
            // Create element
            $element = new Element();
            $element->createById($row['id']);
            
            // Generate full url
            $url = URL_FULL . $element->controller->generateUrl(Routes::DOWNLOAD) . '?donotlogthisdownload';
            
            // Init curl
            $handle = curl_init($url);
            curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
            
            // Do the actual request
            $foo = curl_exec($handle);
            
            // Gather http request info
            $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            
            // Check http code
            if ($http_code == 404) {
                // Increase counter
                $error_num++;
                
                // Message
                $error .= 'Id: ' . $row['id'] . ' @ ' . $url . '<br />';
            }
            else {
                // Increase counter
                $success_num++;
                
                // Message
                $success .= 'Id: ' . $row['id'] . ' @ ' . $url . '<br />';
            }
            
            // Close handler
            curl_close($handle);
        }
        
        // Close db
        $db = null;
        
        // Output
        echo '<h2 style="color: red;">Errors (' . number_format($error_num) . ')</h2>';
        echo $error;
        echo '<hr />';
        
        echo '<h2 style="color: green;">Success (' . number_format($success_num) . ')</h2>';
        echo $success;
    }
}