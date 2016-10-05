<?php
namespace Youkok2\Processors\Tasks;

use Youkok2\Models\Element;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class LoadCourses extends BaseProcessor
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
    
    public function run() {
        $this->setData('code', 200);

        $page = 1;
        $added = [];
        $fetched = 0;
        $new = 0;
        
        $file = file_get_contents('http://www.ime.ntnu.no/api/course/-');
        $json_result = json_decode($file, true);
        
        foreach ($json_result['course'] as $v) {
            $result[] = ['code' => $v['code'],
                'name' => $v['norwegianName'],
                'url_friendly' => Utilities::urlSafe($v['code'])
            ];
        }
        
        foreach ($result as $v) {
            $search_name = $v['code'] . '||%';
            $insert_name = $v['code'] . '||' . $v['name'];

            $check_current_course  = "SELECT id" . PHP_EOL;
            $check_current_course .= "FROM archive" . PHP_EOL;
            $check_current_course .= "WHERE name LIKE :name" . PHP_EOL;
            $check_current_course .= "LIMIT 1";
            
            $check_current_course_query = Database::$db->prepare($check_current_course);
            $check_current_course_query->execute([':name' => $search_name]);
            $row = $check_current_course_query->fetch(\PDO::FETCH_ASSOC);

            if (!isset($row['id'])) {
                $element = new Element();
                $element->setname($insert_name);
                $element->setUrlFriendly($v['url_friendly']);
                $element->setParent(null);
                $element->setAccepted(true);
                $element->setDirectory(true);
                
                $element->save();
                
                $added[] = 'Added ' . $v['code'];
                
                $new++;
            }
        }
        
        $this->setData('msg', ['Fetched' => $fetched, 'New' => $new, 'Added' => $added]);
    }
}
