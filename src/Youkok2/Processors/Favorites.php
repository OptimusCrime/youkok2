<?php
namespace Youkok2\Processors;

use Youkok2\Models\Favorite;
use Youkok2\Models\Me;
use Youkok2\Utilities\Database;

class Favorites extends BaseProcessor
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
        if (isset($_POST['id']) and is_numeric($_POST['id']) and isset($_POST['type']) and
            ($_POST['type'] == 'add' or $_POST['type'] == 'remove')) {
            if ($_POST['type'] == 'remove') {
                $remove_favorite  = "DELETE FROM favorite" . PHP_EOL;
                $remove_favorite .= "WHERE file = :file AND user = :user";
                
                $remove_favorite_query = Database::$db->prepare($remove_favorite);
                $remove_favorite_query->execute([':file' => $_POST['id'], ':user' => $this->me->getId()]);
                
                $this->setData('msg', [['type' => 'success', 'text' => 'Favoritten er fjernet.']]);
            }
            else {
                $insert_favorite  = "INSERT INTO favorite (file, user)" . PHP_EOL;
                $insert_favorite .= "VALUES (:file, :user)";
                
                $insert_favorite_query = Database::$db->prepare($insert_favorite);
                $insert_favorite_query->execute([':file' => $_POST['id'], ':user' => $this->me->getId()]);
                
                $this->setData('msg', [['type' => 'success', 'text' => 'Lagt til som favoritt.']]);
            }
            
            $this->setData('code', 200);
        }
        else {
            $this->setData('code', 500);
            $this->setData('msg', 'Mangler enter id eller type');
        }
    }
}
