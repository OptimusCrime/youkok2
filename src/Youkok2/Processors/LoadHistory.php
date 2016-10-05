<?php
namespace Youkok2\Processors;

use Youkok2\Models\Element;
use Youkok2\Models\History;
use Youkok2\Utilities\Database;

class LoadHistory extends BaseProcessor
{

    protected function requireDatabase() {
        return true;
    }

    protected function encodeData($data) {
        $new_data = [];

        if (count($data['data']) > 0) {
            foreach ($data['data'] as $v) {
                $new_data[] = $v->toArray();
            }
        }

        $data['data'] = $new_data;

        return $data;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        $collection = [];
        
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            $element = Element::get($_POST['id']);

            if ($element->wasFound()) {
                $history_get  = "SELECT h.history_text, h.added, u.nick" . PHP_EOL;
                $history_get .= "FROM archive a" . PHP_EOL;
                $history_get .= "RIGHT JOIN history AS h ON a.id = h.file" . PHP_EOL;
                $history_get .= "RIGHT JOIN user AS u ON h.user = u.id" . PHP_EOL;
                $history_get .= "WHERE a.parent = :id" . PHP_EOL;
                $history_get .= "AND h.history_text IS NOT NULL" . PHP_EOL;
                $history_get .= "ORDER BY h.added DESC" . PHP_EOL;
                
                $history_get_query = Database::$db->prepare($history_get);
                $history_get_query->execute([':id' => $_POST['id']]);
                while ($row = $history_get_query->fetch(\PDO::FETCH_ASSOC)) {
                    $history = new History($row);
                    $history->setHistoryText(str_replace('%u', (($row['nick'] == null or
                        strlen($row['nick']) == 0) ? '<em>Anonym</em>' : $row['nick']), $row['history_text']));
                    
                    $collection[] = $history;
                }
                
                $this->setData('data', $collection);
                
                $this->setOK();
            }
            else {
                $this->setError();
            }
        }
        else {
            $this->setError();
        }
    }
}
