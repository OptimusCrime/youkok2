<?php
namespace Youkok2\Processors;

use Youkok2\Models\Element;
use Youkok2\Models\History;
use Youkok2\Models\Karma;
use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\Utilities;

class CreateFile extends BaseProcessor
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
        
        if (isset($_GET['parent']) and is_numeric($_GET['parent'])) {
            $parent = Element::get($_GET['parent']);

            if ($parent !== null) {
                if (isset($_FILES['files']) and count($_FILES['files']) > 0) {
                    $file_type_split = explode('.', $_FILES['files']['name'][0]);
                    $file_type = $file_type_split[count($file_type_split) - 1];
                    
                    $allowed_filetypes = explode(',', ACCEPTED_FILEENDINGS);
                    
                    if (in_array($file_type, $allowed_filetypes)) {
                        $request_ok = true;
                    }
                }
            }
        }
        
        if ($request_ok) {
            unset($file_type_split[count($file_type_split) - 1]);
            $file_name = implode('.', $file_type_split);
            
            $num = 2;
            $url_friendly_base = Utilities::urlSafe($file_name);
            $url_friendly = Utilities::urlSafe($file_name);
            
            while (true) {
                $get_duplicate = "SELECT id
                FROM archive 
                WHERE parent = :id
                AND url_friendly = :url_friendly";
                
                $get_duplicate_query = Database::$db->prepare($get_duplicate);
                $get_duplicate_query->execute([':id' => $parent->getId(),
                    ':url_friendly' => $url_friendly]);
                $row_duplicate = $get_duplicate_query->fetch(\PDO::FETCH_ASSOC);
                if (isset($row_duplicate['id'])) {
                    $url_friendly = Utilities::urlSafe($url_friendly_base . '-' . $num . '.' . $file_type);
                    
                    $num++;
                }
                else {
                    break;
                }
            }
            
            $num = 2;
            $name_base = $file_name;
            $name = $file_name . '.' . $file_type;
            
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
                    $name = $name_base . ' (' . $num . ')' . '.' . $file_type;
                    
                    $num++;
                }
                else {
                    break;
                }
            }
            
            // Test for missing image
            $mime_type_pretty = str_replace('/', '_', $_FILES['files']['type'][0]);
            if (file_exists(BASE_PATH . '/assets/images/icons/' . $mime_type_pretty . '.png')) {
                $has_missing_image = 0;
            }
            else {
                $has_missing_image = 1;
            }
            
            $checksum = md5_file($_FILES['files']['tmp_name'][0]) . '.' . $file_type;
            
            $element = new Element();
            $element->setName($name);
            $element->setUrlFriendly($url_friendly);
            $element->setChecksum($checksum);
            $element->setParent($parent->getId());
            $element->setMimeType($mime_type_pretty);
            $element->setMissingImage($has_missing_image);
            $element->setSize($_FILES['files']['size'][0]);
            
            if (!$this->me->isLoggedIn()) {
                $element->setVisible(false);
            }
            else {
                $element->setOwner($this->me->getId());
            }
            
            $element->save();
            
            $parent_dir = FILE_PATH . '/' . substr($checksum, 0, 1);
            if (!file_exists($parent_dir)) {
                mkdir($parent_dir);
            }
           
            $parent_dir .= '/' . substr($checksum, 1, 1);
            if (!file_exists($parent_dir)) {
                mkdir($parent_dir);
            }
            
            move_uploaded_file($_FILES['files']['tmp_name'][0], $parent_dir . '/' . $checksum);
            
            if ($this->me->isLoggedIn() and $parent->isEmpty()) {
                $parent->setEmpty(false);
                $parent->update();
                
                $parent->deleteCache();
            }
            
            MessageManager::addFileMessage($this->application, $file_name . '.' . $file_type);
            
            if ($this->me->isLoggedIn()) {
                $history = new History();
                $history->setUser($this->me->getId());
                $history->setFile($element->getId());
                $history->setHistoryText('%u lastet opp ' . $element->getName());
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
            $this->setData('code', 500);
        }
    }
}
