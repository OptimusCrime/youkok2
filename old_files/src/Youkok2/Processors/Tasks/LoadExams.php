<?php
namespace Youkok2\Processors\Tasks;

use Youkok2\Models\Element;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class LoadExams extends BaseProcessor
{

    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }


    protected function requireDatabase() {
        return true;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }

    private function resetExamData() {
        $reset_exam  = "UPDATE archive" . PHP_EOL;
        $reset_exam .= "SET exam = NULL" . PHP_EOL;
        
        Database::$db->query($reset_exam);
    }

    public function run() {
        $this->setData('code', 200);
        
        $updated = 0;

        $get_all_courses  = "SELECT id" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE directory = 1" . PHP_EOL;
        $get_all_courses .= "AND parent IS NULL";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $element = Element::get($row['id']);

            if ($element->wasFound()) {
                $ch = curl_init('http://www.ime.ntnu.no/api/course/' . $element->getCourseCode());
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                
                $data = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                curl_close($ch);
                
                if ($httpcode == '200' and $data !== null) {
                    $json_result = json_decode($data, true);
                    
                    if (isset($json_result['course']) and isset($json_result['course']['assessment'])) {
                        $exam = null;
                        
                        foreach ($json_result['course']['assessment'] as $exam_data) {
                            if (isset($exam_data['code']) and isset($exam_data['date']) and
                                isset($exam_data['appearanceTime'])) {
                                $date_split = explode('-', $exam_data['date']);
                                $time_split = explode(':', $exam_data['appearanceTime']);
                                
                                $exam_temp = mktime(
                                    (int) $time_split[0],
                                    (int) $time_split[1],
                                    0,
                                    (int) $date_split[1],
                                    (int) $date_split[2],
                                    (int) $date_split[0]
                                );
                                
                                if ($exam_temp > time()) {
                                    if ($exam == null or ($exam != null and $exam_temp > $exam)) {
                                        $exam = $exam_temp;
                                    }
                                }
                            }
                        }
                        
                        if ($exam !== null) {
                            $element->setExam(date('Y-m-d H:i:s', $exam));
                            $element->update();
                            
                            if ($this->getSetting('output')) {
                                echo '<p style="color: green;">Updated ' . $element->getCourseCode();
                                echo ', exam = ' . $element->getExam() . '</p>';
                            }
                            
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
            
            if ($this->getSetting('output')) {
                ob_flush();
                flush();
            }
        }
        
        $this->setData('msg', [
            'Updated' => $updated]);
    }
}