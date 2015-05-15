<?php
/*
 * File: LoadExams.php
 * Holds: Load exam information about courses
 * Created: 15.05.2015
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\Base as Base;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;
use \Youkok2\Collections\ElementCollection as ElementCollection;

/*
 * LoadExams extending Base
 */

class LoadExams extends Base {
    
    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check if we should turn on buffering
            if (!self::requireCli()) {
                ob_start();
            }
            
            // Check database
            if ($this->checkDatabase()) {
                // Reset
                $this->resetExamData();
                
                // Fetch
                $this->fetch();
                
                // Close database connection
                Database::close();
            }
            else {
                $this->setError();
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
     * Reset exam information here
     */
    
    private function resetExamData() {
        $reset_exam  = "UPDATE archive" . PHP_EOL;
        $reset_exam .= "SET exam = NULL" . PHP_EOL;
        
        Database::$db->query($reset_exam);
    }

    /*
     * Fetch the actual data here
     */

    private function fetch() {
        // Set code to 200
        $this->setData('code', 200);
        
        // Set for later
        $updated = 0;

        // Get all exames
        $get_all_courses  = "SELECT id" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE is_directory = 1" . PHP_EOL;
        $get_all_courses .= "AND parent IS NULL";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $element = ElementCollection::get($row['id']);

            // Check if valid Element
            if ($element !== null) {
                // Fetch API contents
                $ch = curl_init('http://www.ime.ntnu.no/api/course/' . $element->controller->getCourse()['code']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                
                // Store data
                $data = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                // Close connection
                curl_close($ch);
                
                // Check response
                if ($httpcode == '200' and $data !== null) {
                    // All good, continue
                    $json_result = json_decode($data, true);
                    
                    // Check if assessment was found
                    if (isset($json_result['course']) and isset($json_result['course']['assessment'])) {
                        // For later use
                        $exam = null;
                        
                        // Loop content
                        foreach ($json_result['course']['assessment'] as $exam_data) {
                            // Check if current node is written exam
                            if (isset($exam_data['code']) and $exam_data['code'] == 'S' and isset($exam_data['date']) and isset($exam_data['appearanceTime'])) {
                                // Split data
                                $date_split = explode('-', $exam_data['date']);
                                $time_split = explode(':', $exam_data['appearanceTime']);
                                
                                // Parse to timestamp
                                $exam_temp = mktime((int) $time_split[0], (int) $time_split[1], 0, (int) $date_split[1], 
                                                    (int) $date_split[2], (int) $date_split[0]);
                                
                                // Check if is in the future
                                if ($exam_temp > time()) {
                                    // Check if we should add to exam value
                                    if ($exam == null or ($exam != null and $exam_temp > $exam)) {
                                        $exam = $exam_temp;
                                    }
                                }
                            }
                        }
                        
                        // Check if we should update exam data
                        if ($exam !== null) {
                            // Update exam information
                            $element->setExam(date('Y-m-d H:i:s', $exam));
                            $element->update();
                            
                            // Check if we should output buffer
                            if ($this->mode == 'buffer') {
                                echo '<p style="color: green;">Updated ' . $element->getName() . ', exam = ' . $element->getExam() . '</p>';
                            }
                            
                            // Increase counter
                            $updated++;
                        }
                        else {
                            if ($this->mode == 'buffer') {
                                echo '<p style="color: red;">Could not find exam data for ' . $element->controller->getCourse()['code'] . '</p>';
                            }
                        }
                    }
                    else {
                        if ($this->mode == 'buffer') {
                            echo '<p style="color: red;">Could not find exam data for ' . $element->controller->getCourse()['code'] . '</p>';
                        }
                    }
                }
                else {
                    if ($this->mode == 'buffer') {
                        echo '<p style="color: red;">Got http code ' . $httpcode . ' for '  . $element->controller->getCourse()['code'] . '</p>';
                    }
                }
            }
            
            if ($this->mode == 'buffer') {
                ob_flush();
                flush();
            }
        }
        
        // Set message
        $this->setData('msg', [
            'Updated' => $updated]);
    }
} 