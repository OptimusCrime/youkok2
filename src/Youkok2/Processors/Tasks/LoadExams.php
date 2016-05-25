<?php
/*
 * File: LoadExams.php
 * Holds: Load exam information about courses
 * Created: 15.05.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Processors\Tasks;

use Youkok2\Models\Element;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class LoadExams extends BaseProcessor
{
    
    /*
     * Override
     */

    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }
    
    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
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

    public function run() {
        // Set code to 200
        $this->setData('code', 200);
        
        // Set for later
        $updated = 0;

        // Get all exames
        $get_all_courses  = "SELECT id" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE directory = 1" . PHP_EOL;
        $get_all_courses .= "AND parent IS NULL";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $element = Element::get($row['id']);

            // Check if valid Element
            if ($element->wasFound()) {
                // Fetch API contents
                $ch = curl_init('http://www.ime.ntnu.no/api/course/' . $element->getCourseCode());
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
                            if (isset($exam_data['code']) and isset($exam_data['date']) and
                                isset($exam_data['appearanceTime'])) {
                                // Split data
                                $date_split = explode('-', $exam_data['date']);
                                $time_split = explode(':', $exam_data['appearanceTime']);
                                
                                // Parse to timestamp
                                $exam_temp = mktime(
                                    (int) $time_split[0],
                                    (int) $time_split[1],
                                    0,
                                    (int) $date_split[1],
                                    (int) $date_split[2],
                                    (int) $date_split[0]
                                );
                                
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
                            if ($this->getSetting('output')) {
                                echo '<p style="color: green;">Updated ' . $element->getCourseCode();
                                echo ', exam = ' . $element->getExam() . '</p>';
                            }
                            
                            // Increase counter
                            $updated++;
                        }
                        else {
                            if ($this->getSetting('output')) {
                                echo '<p style="color: red;">Could not find exam data for ';
                                echo $element->getCourseCode() . '</p>';
                            }
                        }
                    }
                    else {
                        if ($this->getSetting('output')) {
                            echo '<p style="color: red;">Could not find exam data for ';
                            echo $element->getCourseCode() . '</p>';
                        }
                    }
                }
                else {
                    if ($this->getSetting('output')) {
                        echo '<p style="color: red;">Got http code ' . $httpcode . ' for ';
                        echo $element->getCourseCode() . '</p>';
                    }
                }
            }
            
            // Check if we should flush the buffer
            if ($this->getSetting('output')) {
                ob_flush();
                flush();
            }
        }
        
        // Set message
        $this->setData('msg', [
            'Updated' => $updated]);
    }
}
