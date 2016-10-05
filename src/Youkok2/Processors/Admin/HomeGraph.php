<?php
namespace Youkok2\Processors\Admin;

use Youkok2\Processors\BaseProcessor;
use Youkok2\Models\Me;
use Youkok2\Collections\ElementCollection;
use Youkok2\Utilities\Database;

class HomeGraph extends BaseProcessor
{

    protected function requireDatabase() {
        return true;
    }

    protected function checkPermissions() {
        return $this->requireAdmin();
    }

    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        $response = [];
        $response['graph'] = [];
        $response['delta'] = '';
        
        for ($i = 30; $i >= 0; $i--) {
            $date_offset = strtotime('-' . $i . ' days');
            
            if ($i == 30) {
                $response['delta'] = date('j. M Y', $date_offset);
            }
            elseif ($i == 0) {
                $response['delta'] .= ' &mdash; ' . date('j. M Y', $date_offset);
            }
            
            $response['graph'][] = [
                'date' => date('Y-m-d', $date_offset),
                'downloads' => 0
            ];
        }
        
        $get_all_downloads  = "SELECT COUNT(id) AS 'downloads', downloaded_time" . PHP_EOL;
        $get_all_downloads .= "FROM download" . PHP_EOL;
        $get_all_downloads .= "WHERE downloaded_time >= (CURRENT_TIMESTAMP - (60 * 60 * 24 * 30))" . PHP_EOL;
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
        
        $this->setData('data', $response);
        
        $this->setOK();
    }
}
