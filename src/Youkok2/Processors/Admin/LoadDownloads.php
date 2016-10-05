<?php
namespace Youkok2\Processors\Admin;

use Youkok2\Processors\BaseProcessor;
use Youkok2\Models\Me;
use Youkok2\Collections\ElementCollection;
use Youkok2\Utilities\Database;

class LoadDownloads extends BaseProcessor
{

    protected function checkPermissions() {
        return $this->requireAdmin();
    }

    protected function requireDatabase() {
        return true;
    }

    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        $this->setData('code', 200);
        
        $output = [];
        
        $get_last_downloads  = "SELECT a.id, d.ip, d.downloaded_time, d.agent, d.user" . PHP_EOL;
        $get_last_downloads .= "FROM archive AS a" . PHP_EOL;
        $get_last_downloads .= "LEFT JOIN download AS d ON a.id = d.file" . PHP_EOL;
        $get_last_downloads .= "WHERE d.downloaded_time >= (CURRENT_TIMESTAMP - (60 * 60 * 24))" . PHP_EOL;
        $get_last_downloads .= "AND a.is_visible = 1";
        
        $get_last_downloads_query = Database::$db->query($get_last_downloads);
        while ($row = $get_last_downloads_query->fetch(\PDO::FETCH_ASSOC)) {
            $element = ElementCollection::get($row['id'], ['root']);

            if ($element !== null) {
                $temp_array = [
                    'element_id' => $element->getId(),
                    'element_name' => $element->getName(),
                    'url' => $element->controller->generateUrl('/emner'),
                    'download_ip' => $row['ip'],
                    'download_time' => $row['downloaded_time'],
                    'download_agent' => $row['agent'],
                    'download_user' => $row['user'],
                ];

                $url = 'http://api.ipinfodb.com/v3/ip-city/?key=' . IPINFODB . '&ip=' . $row['ip'] . '&format=json';
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($ch);
                curl_close($ch);
                
                if ($data !== null and strlen($data) > 0) {
                    $json_data = json_decode($data, true);
                    
                    if (isset($json_data['latitude']) and isset($json_data['longitude'])) {
                        $temp_array['lat'] = (float) $json_data['latitude'];
                        $temp_array['lng'] = (float) $json_data['longitude'];
                    }
                }
                
                $output[] = $temp_array;
            }
        }
        
        $this->setData('data', $output);
    }
}
