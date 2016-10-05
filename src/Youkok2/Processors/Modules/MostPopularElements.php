<?php
namespace Youkok2\Processors\Modules;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Controllers\ElementController;
use Youkok2\Utilities\Database;

class MostPopularElements extends ModuleProcessor
{
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function get() {
        parent::run();
        
        $collection = [];
        
        if ($this->getSetting('module1_delta') !== null and !is_array($this->getSetting('module1_delta'))) {
            $delta_numeric = $this->getSetting('module1_delta');
        }
        else {
            $delta_numeric = $this->me->getModuleSettings('module1_delta');
        }
        
        $limit = 15;
        if ($this->getSetting('limit') !== null and !is_array($this->getSetting('limit')) and
            is_numeric($this->getSetting('limit'))) {
            $limit = $this->getSetting('limit');
        }
        
        if ($delta_numeric == null or $delta_numeric < 0 or $delta_numeric > 4) {
            $delta_numeric = 3;
        }
        
        $get_most_popular  = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
        $get_most_popular .= "FROM download d" . PHP_EOL;
        $get_most_popular .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_most_popular .= ElementController::$timeIntervals[DATABASE_ADAPTER][$delta_numeric] . PHP_EOL;
        $get_most_popular .= "GROUP BY d.file" . PHP_EOL;
        $get_most_popular .= "HAVING COUNT(d.id) > 0" . PHP_EOL;
        $get_most_popular .= "ORDER BY downloaded_times DESC, a.added DESC" . PHP_EOL;
        $get_most_popular .= "LIMIT " . $limit;
        
        $get_most_popular_query = Database::$db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(\PDO::FETCH_ASSOC)) {
            $element = Element::get($row['id']);
            $element->setDownloadCount($delta_numeric, $row['downloaded_times']);
            $collection[] = $element;
        }
        
        $this->setData('data', $collection);
    }
    
    public function update() {
        parent::run();
        
        $delta_numeric = $this->getSetting('module1_delta');
        
        if ($delta_numeric < 0 or $delta_numeric > 4) {
            $delta_numeric = 3;
        }
        
        $this->me->setModuleSettings('module1_delta', $delta_numeric);
        
        if ($this->me->isLoggedIn()) {
            $this->me->update();
        }
        
        $this->get();
    }
}
