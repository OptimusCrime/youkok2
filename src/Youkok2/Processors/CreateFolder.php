<?php
namespace Youkok2\Processors;

use Youkok2\Models\Element;
use Youkok2\Models\History;
use Youkok2\Models\Karma;
use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\Utilities;

class CreateFolder extends BaseProcessor
{

    protected function checkPermissions() {
        return $this->requireLoggedIn();
    }

    protected function requireDatabase() {
        return true;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        $request_ok = false;
        
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            $parent = Element::get($_POST['id']);

            if ($parent->wasFound()) {
                if (isset($_POST['name']) and strlen($_POST['name']) >= 4) {
                    $request_ok = true;
                }
            }
        }
        
        if ($request_ok) {
            $num = 2;
            $url_friendly_base = Utilities::urlSafe($_POST['name']);
            $url_friendly = Utilities::urlSafe($_POST['name']);
            
            while (true) {
                $get_duplicate  = "SELECT id" . PHP_EOL;
                $get_duplicate .= "FROM archive" . PHP_EOL;
                $get_duplicate .= "WHERE parent = :id" . PHP_EOL;
                $get_duplicate .= "AND url_friendly = :url_friendly";
                
                $get_duplicate_query = Database::$db->prepare($get_duplicate);
                $get_duplicate_query->execute([':id' => $parent->getId(),
                    ':url_friendly' => $url_friendly]);
                $row_duplicate = $get_duplicate_query->fetch(\PDO::FETCH_ASSOC);
                
                if (isset($row_duplicate['id'])) {
                    $url_friendly = Utilities::urlSafe($url_friendly_base . '-' . $num);
                    
                    $num++;
                }
                else {
                    break;
                }
            }
            
            $num = 2;
            $name_base = $_POST['name'];
            $name = $_POST['name'];
            
            while (true) {
                $get_duplicate2  = "SELECT id" . PHP_EOL;
                $get_duplicate2 .= "FROM archive" . PHP_EOL;
                $get_duplicate2 .= "WHERE parent = :id" . PHP_EOL;
                $get_duplicate2 .= "AND name = :name";
                
                $get_duplicate2_query = Database::$db->prepare($get_duplicate2);
                $get_duplicate2_query->execute([':id' => $parent->getId(),
                    ':name' => $name]);
                $row_duplicate2 = $get_duplicate2_query->fetch(\PDO::FETCH_ASSOC);
                
                if (isset($row_duplicate2['id'])) {
                    $name = $name_base . ' (' . $num . ')';
                    
                    $num++;
                }
                else {
                    break;
                }
            }
            
            $element = new Element();
            $element->setName($name);
            $element->setUrlFriendly($url_friendly);
            $element->setParent($parent->getId());
            $element->setDirectory(true);
            $element->setOwner($this->me->getId());
            
            $element->save();
            
            if ($parent->isEmpty()) {
                $parent->setEmpty(false);
                $parent->update();
                
                $parent->deleteCache();
            }
            
            MessageManager::addFileMessage($this->application, $_POST['name']);
            
            $history = new History();
            $history->setUser($this->me->getId());
            $history->setFile($element->getId());
            $history->setHistoryText('%u opprettet ' . $element->getName());
            $history->save();
            
            $karma = new Karma();
            $karma->setUser($this->me->getId());
            $karma->setFile($element->getId());
            $karma->setValue(2);
            $karma->save();
            
            $this->me->increaseKarmaPending(2);
            $this->me->update();
            
            $this->setData('code', 200);
        }
        else {
            if (isset($_POST['name']) and strlen($_POST['name']) < 4) {
                $this->setData('code', 400);
            }
            else {
                $this->setData('code', 500);
            }
        }
    }
}
