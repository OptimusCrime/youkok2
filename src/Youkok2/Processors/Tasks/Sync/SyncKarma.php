<?php
namespace Youkok2\Processors\Tasks\Sync;

use Youkok2\Models\Course;
use Youkok2\Models\Element;
use Youkok2\Models\Karma;
use Youkok2\Models\User;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class SyncKarma extends BaseProcessor
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
        $update_num = 0;
        
        $get_all_users  = "SELECT id" . PHP_EOL;
        $get_all_users .= "FROM user";
        
        $get_all_users_query = Database::$db->prepare($get_all_users);
        $get_all_users_query->execute();
        
        while ($user_row = $get_all_users_query->fetch(\PDO::FETCH_ASSOC)) {
            $user = new User($user_row['id']);
            $new_karma = 5;
            $new_karma_pending = 0;
            
            $get_user_karma  = "SELECT id, user, file, value, pending, state, added" . PHP_EOL;
            $get_user_karma .= "FROM karma" . PHP_EOL;
            $get_user_karma .= "WHERE user = :user";
            
            $get_user_karma_query = Database::$db->prepare($get_user_karma);
            $get_user_karma_query->execute([':user' => $user->getId()]);
            
            while ($karma_row = $get_all_users_query->fetch(\PDO::FETCH_ASSOC)) {
                $karma = new Karma($karma_row);
                
                if ($karma->isPending()) {
                    $new_karma_pending += $karma->getValue();
                }
                else {
                    if ($karma->getState() == 1) {
                        $new_karma += $karma->getValue();
                    }
                    else {
                        $new_karma -= $karma->getValue();
                    }
                }
            }

            if ($user->getKarma() != $new_karma or $user->getKarmaPending() != $new_karma_pending) {
                $update_num++;
                
                if ($user->getKarma() != $new_karma) {
                    $user->setKarma($new_karma);
                }
                if ($user->getKarmaPending() != $new_karma_pending) {
                    $user->setKarmaPending($new_karma_pending);
                }
                
                $user->update();
            }
        }
        
        $this->setData('data', ['updated' => $update_num]);
    }
}
