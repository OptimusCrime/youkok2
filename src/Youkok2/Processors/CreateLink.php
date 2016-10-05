<?php
namespace Youkok2\Processors;

use Youkok2\Models\Element;
use Youkok2\Models\History;
use Youkok2\Models\Karma;
use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\Utilities;

class CreateLink extends BaseProcessor
{

    protected function canBeLoggedIn() {
        return true;
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
                if (isset($_POST['url']) and filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
                    $request_ok = true;
                }
            }
        }
        
        if ($request_ok) {
            $can_post = true;
            
            $_POST['url'] = rtrim(trim($_POST['url']));
            
            if (isset($_POST['name'])) {
                if (strlen($_POST['name']) >= 4) {
                    $name = $_POST['name'];
                }
                else {
                    $can_post = false;
                }
            }
            else {
                $name = $_POST['url'];
            }
            
            if ($can_post) {
                $get_duplicate  = "SELECT id" . PHP_EOL;
                $get_duplicate .= "FROM archive " . PHP_EOL;
                $get_duplicate .= "WHERE parent = :id" . PHP_EOL;
                $get_duplicate .= "AND url = :url";
                
                $get_duplicate_query = Database::$db->prepare($get_duplicate);
                $get_duplicate_query->execute([':id' => $parent->getId(),
                    ':url' => $_POST['url']]);
                $row_duplicate = $get_duplicate_query->fetch(\PDO::FETCH_ASSOC);
                
                if (!isset($row_duplicate['id'])) {
                    $element = new Element();
                    $element->setName($name);
                    $element->setUrlFriendly('');
                    $element->setParent($parent->getId());
                    $element->setUrl($_POST['url']);
                    
                    if (!$this->me->isLoggedIn()) {
                        $element->setVisible(false);
                    }
                    else {
                        $element->setOwner($this->me->getId());
                    }
                    
                    $element->save();
                    
                    if ($this->me->isLoggedIn() and $parent->isEmpty()) {
                        $parent->setEmpty(false);
                        $parent->update();
                        
                        $parent->deleteCache();
                    }
                    
                    MessageManager::addFileMessage($this->application, $name);
                    
                    if ($this->me->isLoggedIn()) {
                        $history = new History();
                        $history->setUser($this->me->getId());
                        $history->setFile($element->getId());
                        $history->setHistoryText('%u postet ' . $element->getName());
                        $history->save();
                        
                        $karma = new Karma();
                        $karma->setUser($this->me->getId());
                        $karma->setFile($element->getId());
                        $karma->save();
                        
                        $this->me->increaseKarmaPending(5);
                        $this->me->update();
                    }
                    
                    $this->setData('code', 200);
                }
                else {
                    $this->setData('code', 400);
                }
            }
            else {
                $this->setData('code', 401);
            }
        }
        else {
            $this->setData('code', 500);
        }
    }
}
