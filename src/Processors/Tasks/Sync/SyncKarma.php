<?php
/*
 * File: SyncKarma.php
 * Holds: Syncs karma from values on users with actual values in the karma table
 * Created: 19.09.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Tasks\Sync;

use \Youkok2\Models\Course as Course;
use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Karma as Karma;
use \Youkok2\Models\User as User;
use \Youkok2\Processors\BaseProcessor as BaseProcessor;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;

class SyncKarma extends BaseProcessor {

    /*
     * Override
     */

    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }
    
    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Construct
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }

    /*
     * Syncs karma between users
     */

    public function run() {
        // Set code to 200
        $this->setData('code', 200);
        $update_num = 0;
        
        $get_all_users  = "SELECT id" . PHP_EOL;
        $get_all_users .= "FROM user";
        
        $get_all_users_query = Database::$db->prepare($get_all_users);
        $get_all_users_query->execute();
        
        while ($user_row = $get_all_users_query->fetch(\PDO::FETCH_ASSOC)) {
            $user = new User($user_row['id']);
            // Get the actual amout of karma here
            $new_karma = 5;
            $new_karma_pending = 0;
            
            $get_user_karma  = "SELECT id, user, file, value, pending, state, added" . PHP_EOL;
            $get_user_karma .= "FROM karma" . PHP_EOL;
            $get_user_karma .= "WHERE user = :user";
            
            $get_user_karma_query = Database::$db->prepare($get_user_karma);
            $get_user_karma_query->execute([':user' => $user->getId()]);
            
            // Loop all karma entries
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
            
            
            
            // Evaluate the new and old karma
            if ($user->getKarma() != $new_karma or $user->getKarmaPending() != $new_karma_pending) {
                $update_num++;
                
                // Check what we should update
                if ($user->getKarma() != $new_karma) {
                    $user->setKarma($new_karma);
                }
                if ($user->getKarmaPending() != $new_karma_pending) {
                    $user->setKarmaPending($new_karma_pending);
                }
                
                // Update the user
                $user->update();
            }
        }
        
        $this->setData('data', ['updated' => $update_num]);
    }
} 