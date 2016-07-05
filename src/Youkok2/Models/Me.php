<?php
/*
 * File: Me.php
 * Holds: Static class for the current user
 * Created: 06.11.2014
 * Project: Youkok2
 *
 */

namespace Youkok2\Models;

use Youkok2\Utilities\CsrfManager;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\TemplateHelper;

class Me
{
    
    /*
     * Variables for the user
     */

    private $app;
    private $user;
    private $favorites;

    /*
     * Init the user
     */

    public function __construct($app) {
        // Set user and favorites to null
        $this->user = null;
        $this->favorites = null;

        // Store app in this object
        $this->app = $app;

        // Check if we have anything stored
        if ($this->app->getSession('youkok2') !== null or $this->app->getCookie('youkok2') !== null) {
            if ($this->app->getCookie('youkok2') !== null) {
                $hash = $this->app->getCookie('youkok2');

                // Set session as well
                $this->app->setSession('youkok2', $hash);
            } else {
                $hash = $this->app->getSession('youkok2');
            }

            // Try to split
            $hash_split = explode('asdkashdsajheeeeehehdffhaaaewwaddaaawww', $hash);
            if (count($hash_split) == 2) {
                // Fetch from database to see if online
                $get_current_user = "SELECT id, email, password, nick, module_settings, last_seen, " . PHP_EOL;
                $get_current_user .= "karma, karma_pending, banned" . PHP_EOL;
                $get_current_user .= "FROM user " . PHP_EOL;
                $get_current_user .= "WHERE email = :email" . PHP_EOL;
                $get_current_user .= "AND password = :password";

                $get_current_user_query = Database::$db->prepare($get_current_user);
                $get_current_user_query->execute([':email' => $hash_split[0],
                    ':password' => $hash_split[1]]);
                $row = $get_current_user_query->fetch(\PDO::FETCH_ASSOC);

                // Check if anything want returned
                if (isset($row['id'])) {
                    // Create a new instace of the user object
                    $this->user = new User($row);
                }
            }

            if ($this->user === null) {
                // Unset all
                $this->app->clearSession('youkok2');
                $this->app->clearCookie('youkok2');
            }
        }
    }
    
    /*
     * Create new instance of the User object (used for registration)
     */
    
    public function create() {
        $this->user = new User();
    }

    public function getUser() {
        return $this->user;
    }

    /*
     * Getters (override for storing information in the User object)
     */
    
    public function getModuleSettings($key = null) {
        // Check what to return
        $settings_data = null;
        if ($this->user !== null) {
            // Return the actual delta
            $settings_data = $this->user->getModuleSettings();
        }
        else {
            // Check if cookie is set
            if ($this->app->getCookie('module_settings') !== null and
                strlen($this->app->getCookie('module_settings')) > 0) {
                $settings_data = $this->app->getCookie('module_settings');
            }
        }
        
        // Check if we returned anything
        if ($settings_data != null) {
            $settings_data_decoded = json_decode($settings_data, true);
            
            // Make sure we have a array
            if (is_array($settings_data_decoded)) {
                // Check if we should fetch all the settings
                if ($key == null) {
                    // Just return all the settings
                    return $settings_data_decoded;
                }
                else {
                    // Try to fetch the one settings
                    if (isset($settings_data_decoded[$key])) {
                        return $settings_data_decoded[$key];
                    }
                }
            }
        }
        
        // Last resort, return default values
        if ($key == null) {
            return null;
        }
        elseif ($key == 'module1_delta' or $key == 'module2_delta') {
            return 3;
        }

        // If we got this far, we should just return null
        return null;
        
    }

    /*
     * Setters (override for storing information in the User object)
     */

    public function setNick($nick) {
        if ($nick == '') {
            $nick = null;
        }

        // Set
        $this->user->setNick($nick);
    }
    public function setModuleSettings($key, $value) {
        // Get the current settings
        $settings = $this->getModuleSettings();
        
        // Make sure we have a array
        if ($settings == null) {
            $settings = [];
        }
        
        // Apply the new settings
        $settings[$key] = $value;
        
        // Check if we should set cookie for later too
        if ($this->user === null) {
            // Set cookie
            $this->app->setCookie('module_settings', json_encode($settings));
        }
        else {
            $this->user->setModuleSettings(json_encode($settings));
        }
    }
    public function increaseKarma($karma) {
        $this->user->setKarma($this->user->getKarma() + $karma);
    }
    public function increaseKarmaPending($pending) {
        $this->user->setKarmaPending($this->user->getKarmaPending() + $pending);
    }
    
    /*
     * Conditional stuff
     */
    
    public function isLoggedIn() {
        return $this->user !== null;
    }
    public function isAdmin() {
        return ($this->user !== null and ($this->user->getId() == 10000 or $this->user->getId() == 1));
    }
    public function hasKarma() {
        return $this->user !== null and $this->user->getKarma() > 0;
    }
    public function canContribute() {
        return ($this->hasKarma() and !$this->isBanned());
    }
    
    /*
     * Login
     */

    public function logIn() {
        // Check if logged in
        if ($this->user === null) {
            // Okey
            if ($this->app->getPost('login-email') !== null and $this->app->getPost('login-pw') !== null and
                $this->app->getPost('_token') !== null) {
                // Check CSRF token
                try {
                    CsrfManager::validateSignature($this->app->getPost('_token'));
                }
                catch (\Exception $e) {
                    $this->app->setStatus(400);
                    return;
                }
                
                // Try to fetch email
                $get_login_user  = "SELECT id, email, password, nick, module_settings, last_seen, " . PHP_EOL;
                $get_login_user .= "karma, karma_pending, banned" . PHP_EOL;
                $get_login_user .= "FROM user" . PHP_EOL;
                $get_login_user .= "WHERE email = :email";

                $get_login_user_query = Database::$db->prepare($get_login_user);
                $get_login_user_query->execute([':email' => $this->app->getPost('login-email')]);
                $row = $get_login_user_query->fetch(\PDO::FETCH_ASSOC);

                // Check result
                if (isset($row['id'])) {
                    $hash = Utilities::reverseFuckup($row['password']);
                    // Try to match with password from the database
                    if (password_verify($this->app->getPost('login-pw'), $hash)) {
                        // Check remember me

                        if ($this->app->getPost('login-remember') == 'remember') {
                            $remember_me = true;
                        }
                        else {
                            $remember_me = false;
                        }

                        // Set login
                        $this->setLogin($row['password'], $this->app->getPost('login-email'), $remember_me);

                        // Set user
                        $this->user = new User($row);

                        // Add message
                        MessageManager::addMessage($this->app, 'Du er nå logget inn.', 'success');

                        // Check if we should redirect the user back to the previous page
                        if ($this->app->getServer('HTTP_REFERER') !== null and
                            strpos($this->app->getServer('HTTP_REFERER'), URL) !== false) {
                            // Has referer, remove base
                            $clean_referer = str_replace(URL_FULL, '', $this->app->getServer('HTTP_REFERER'));

                            // Check if anything left
                            if (strlen($clean_referer) > 0 and $clean_referer != 'logg-inn') {
                                // Refirect to whatever we have left
                                $this->app->send($clean_referer);
                            }
                            else {
                                // Send to frontpage
                                $this->app->send('');
                            }
                        }
                        else {
                            // Does not have referer
                            $this->app->send('');
                        }
                    }
                    else {
                        // Message
                        MessageManager::addMessage(
                            $this->app,
                            'Oiisann. Feil brukernavn og/eller passord. Prøv igjen.',
                            'danger'
                        );
                        
                        // Set session
                        $this->app->setSession('login_correct_email', $row['email']);
                        
                        // Redirect
                        $this->app->send(TemplateHelper::urlFor('auth_login'));
                    }
                }
                else {
                    // Message
                    MessageManager::addMessage(
                        $this->app,
                        'Oiisann. Feil brukernavn og/eller passord. Prøv igjen.',
                        'danger'
                    );

                    // Redirect
                    $this->app->send(TemplateHelper::urlFor('auth_login'));
                }
            }
            else {
                // Not submitted or anything, just redirect
                $this->app->send('');
            }
        }
    }
    
    /*
     * Set the login information
     */
    
    public function setLogin($hash, $email, $cookie = false) {
        // Remove old login (just in case)
        $this->app->clearSession('youkok2');
        
        // Unset the cookie
        $this->app->clearCookie('youkok2');
        
        // Generate the new login token
        $strg = Me::generateLoginString($hash, $email);

        // Set the new login details
        if ($cookie) {
            $this->app->setCookie('youkok2', $strg);
        }
        else {
            $this->app->setSession('youkok2', $strg);
        }
    }

    public static function generateLoginString($hash, $email) {
        return $email . 'asdkashdsajheeeeehehdffhaaaewwaddaaawww' . $hash;
    }

    /*
     * Logout
     */

    public function logOut() {
        // Check if logged in
        if ($this->user !== null and $this->app->getGet('_token')) {
            // Unset session
            $this->app->clearSession('youkok2');
            
            // Unset token
            $this->app->clearCookie('youkok2');

            // Set message
            MessageManager::addMessage($this->app, 'Du har nå logget ut.', 'success');
        }
        else {
            // Simply redirect home
            $this->app->send('');
        }

        // Check if we should redirect the user back to the previous page
        if ($this->app->getServer('HTTP_REFERER') !== null and
            strstr($this->app->getServer('HTTP_REFERER'), URL) !== false) {
            // Has referer, remove base
            $clean_referer = str_replace(URL_FULL, '', $this->app->getServer('HTTP_REFERER'));
            
            // Check if anything left
            if (strlen($clean_referer) > 0) {
                // Refirect to whatever we have left
                $this->app->send($clean_referer);
            }
            else {
                // Send to frontpage
                $this->app->send('');
            }
        }
        else {
            // Does not have referer
            $this->app->send('');
        }
    }

    /*
     * Favorites
     */

    public function getFavorites() {
        // Check if already loaded
        if ($this->favorites === null) {
            // Set favorites to array
            $this->favorites = [];

            // Check if we are logged in
            if ($this->user === null) {
                return $this->favorites;
            }

            // Run query
            $get_favorites  = "SELECT f.file" . PHP_EOL;
            $get_favorites .= "FROM favorite AS f" . PHP_EOL;
            $get_favorites .= "LEFT JOIN archive AS a ON a.id = f.file" . PHP_EOL;
            $get_favorites .= "WHERE f.user = :user" . PHP_EOL;
            $get_favorites .= "AND pending = 0" . PHP_EOL;
            $get_favorites .= "AND deleted = 0" . PHP_EOL;
            $get_favorites .= "ORDER BY f.id ASC";

            $get_favorites_query = Database::$db->prepare($get_favorites);
            $get_favorites_query->execute([':user' => $this->user->getId()]);
            while ($row = $get_favorites_query->fetch(\PDO::FETCH_ASSOC)) {
                $this->favorites[] = Element::get($row['file']);
            }
        }

        // Return entire list of elements
        return $this->favorites;
    }
    
    /*
     * Check if one Element is favorite
     */
    
    public function isFavorite($id) {
        // Check if we should load
        if ($this->favorites === null) {
            $this->getFavorites();
        }

        foreach ($this->favorites as $v) {
            if ($v->getId() == $id) {
                return true;
            }
        }
        
        // If we came all this was, it is not a favorite
        return false;
    }

    /*
     * Get user karma elements
     */

    public function getKarmaElements() {
        $collection = [];

        // Run query
        $get_user_karma_elements  = "SELECT id, user, file, value, pending, state, added" . PHP_EOL;
        $get_user_karma_elements .= "FROM karma" . PHP_EOL;
        $get_user_karma_elements .= "WHERE user = :user" . PHP_EOL;
        $get_user_karma_elements .= "ORDER BY added DESC";

        $get_user_karma_elements_query = Database::$db->prepare($get_user_karma_elements);
        $get_user_karma_elements_query->execute([':user' => $this->user->getId()]);
        while ($row = $get_user_karma_elements_query->fetch(\PDO::FETCH_ASSOC)) {
            $collection[] = new Karma($row);
        }

        // Return elements
        return $collection;
    }
    
    /*
     * Override save and update because the __callStatic method does not work for these calls
     */
    
    public function update() {
        if ($this->user !== null) {
            $this->user->update();
        }
    }
    public function save() {
        if ($this->user !== null) {
            $this->user->save();
        }
    }
    
    /*
     * Static functions overload
     */
    
    public function __call($name, $arguments) {
        // Check if method exists
        if ($this->user != null and method_exists($this->user, $name)) {
            // Call method and return response
            return call_user_func_array([$this->user,
                $name], $arguments);
        }
    }
}
