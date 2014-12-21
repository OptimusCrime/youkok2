<?php
/*
 * File: LoadCourses.php
 * Holds: Load courses
 * Created: 17.12.14
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Course as Course;
use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\Base as Base;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * LoadCourses extending Base
 */

class MigrateChecksums extends Base {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->checkDatabase()) {
                // Fetch
                $this->collect();
                
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
     * Check if we can connect to the database
     */

    private function checkDatabase() {
        try {
            Database::connect();

            return true;
        }
        catch (Exception $e) {
            $this->setData('code', 500);
            $this->setData('msg', 'Could not connect to database');

            return false;
        }
    }

    /*
     * Fetch the actual data here
     */

    private function collect() {
        // Set code to 200
        $this->setData('code', 200);

        $get_newest  = "SELECT id" . PHP_EOL;
        $get_newest .= "FROM archive" . PHP_EOL;
        $get_newest .= "WHERE is_directory = 0" . PHP_EOL;
        
        $get_newest_query = Database::$db->query($get_newest);
        while ($row = $get_newest_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $element = ElementCollection::get($row['id'], array('location'));
            
            if ($element != null) {
                $full_path = FILE_PATH . $element->controller->getFullLocation();
                if (file_exists($full_path) and !is_dir($full_path)) {
                    $checksum = md5_file($full_path);
                    $file_ending_split = explode('.', $full_path);
                    $sql_checksum = $checksum . '.' . $file_ending_split[count($file_ending_split) - 1];
                    $new_file = FILE_PATH . '/foo/' . $sql_checksum;
                    
                    
                    $save_migrate  = "UPDATE archive" . PHP_EOL;
                    $save_migrate .= "SET checksum = :checksum" . PHP_EOL;
                    $save_migrate .= "WHERE id = :id" . PHP_EOL;
                    $save_migrate .= "LIMIT 1";
                    
                    $save_migrate_query = Database::$db->prepare($save_migrate);
                    $save_migrate_query->execute(array(':checksum' => $sql_checksum, ':id' => $element->getId()));
                    
                    copy($full_path, $new_file);
                    
                    echo $full_path . ' -> ' . $new_file . '<br />';
                }
            }
        }
        
        // Set message
        //$this->setData('msg', ['Fetched' => $fetched, 'New' => $new, 'Added' => $added]);
    }
} 