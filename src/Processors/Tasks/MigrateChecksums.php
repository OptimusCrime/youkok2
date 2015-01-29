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
            if ($this->makeDatabaseConnection()) {
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
     * Fetch the actual data here
     */

    private function collect() {
        // Set code to 200
        $this->setData('code', 200);
        
        $get_newest  = "SELECT id" . PHP_EOL;
        $get_newest .= "FROM archive" . PHP_EOL;
        $get_newest .= "WHERE is_directory = 0" . PHP_EOL;
        $get_newest .= "AND url IS NULL" . PHP_EOL;
        $get_newest .= "AND checksum IS NOT NULL" . PHP_EOL;
        
        $get_newest_query = Database::$db->query($get_newest);
        while ($row = $get_newest_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $element = ElementCollection::get($row['id'], array('location'));
            
            if ($element != null) {
                // Get location
                $location = $element->controller->getPhysicalLocation();
                if (file_exists($location)) {
                    $location_split = explode('/', $location);
                    
                    // Get name
                    $name = $location_split[count($location_split) - 1];
                    
                    // Get folders
                    $folder1 = substr($name, 0, 1);
                    $folder2 = substr($name, 1, 1);
                    echo $element->getId() . ': ';
                    // Create folders
                    if (!is_dir(FILE_PATH . '/' . $folder1)) {
                        mkdir(FILE_PATH . '/' . $folder1);
                    }
                    if (!is_dir(FILE_PATH . '/' . $folder1 . '/' . $folder2)) {
                        mkdir(FILE_PATH . '/' . $folder1 . '/' . $folder2);
                    }
                    
                    // Copy file
                    try {
                        copy($location, FILE_PATH . '/' . $folder1 . '/' . $folder2 . '/' . $name);
                    }
                    catch (\Exception $e) {
                        echo $e->getMessage();
                    }
                    
                    // Debug
                    echo $location . ' -> ' . FILE_PATH . '/' . $folder1 . '/' . $folder2 . '/' . $name . '<br />';
                }
            }
        }
    }
} 