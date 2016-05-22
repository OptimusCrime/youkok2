<?php
/*
 * File: Me.php
 * Holds: Static class for the current user
 * Created: 06.11.2014
 * Project: Youkok2
 *
 */

namespace Youkok2\Models;

use Youkok2\Models\User;
use Youkok2\Utilities\CsrfManager;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\Redirect;

class Me 
{
    
    /*
     * Variables for the user
     */
    
    private static $user;

    // Other variables
    private static $favorites;
    private static $inited;

    /*
     * Init the user
     */

    public static function init($app) {
        // Only run if not already inited
        if (self::$inited === null or !self::$inited) {
            // Set initial
            self::$inited = true;
            self::$favorites = null;

            // Check if we have anything stored
            if ($app->getSession('youkok2') !== null or $app->getCookie('youkok2') !== null) {
                if ($app->getCookie('youkok2') !== null) {
                    $hash = $app->getCookie('youkok2');

                    // Set session as well
                    $app->setSession('youkok2', $hash);
                }
                else {
                    $hash = $app->getSession('youkok2');
                }

                // Try to split
                $hash_split = explode('asdkashdsajheeeeehehdffhaaaewwaddaaawww', $hash);
                if (count($hash_split) == 2) {
                    // Fetch from database to see if online
                    $get_current_user  = "SELECT id, email, password, nick, module_settings, last_seen, karma, karma_pending, banned" . PHP_EOL;
                    $get_current_user .= "FROM user " . PHP_EOL;
                    $get_current_user .= "WHERE email = :email" . PHP_EOL;
                    $get_current_user .= "AND password = :password";
                    
                    $get_current_user_query = Database::$db->prepare($get_current_user);
                    $get_current_user_query->execute(array(':email' => $hash_split[0], 
                                                           ':password' => $hash_split[1]));
                    $row = $get_current_user_query->fetch(\PDO::FETCH_ASSOC);

                    // Check if anything want returned
                    if (isset($row['id'])) {                        
                        // Create a new instace of the user object
                        self::$user = new User($row);
                    }
                    else {
                        // Unset all
                        $app->clearSession('youkok2');
                        $app->clearCookie('youkok2');
                    }
                }
            }
        }
    }
    
    /*
     * Create new instance of the User object (used for registration)
     */
    
    public static function create() {
        self::$user = new User();
    }
    
    /*
     * Getters (override for storing information in the User object)
     */
    
    public static function getModuleSettings($app, $key = null) {
        // Check what to return
        $settings_data = null;
        if (self::$user !== null) {
            // Return the actual delta
            $settings_data = self::$user->getModuleSettings();
        }
        else {
            // Check if cookie is set

            if ($app->getCookie('module_settings') !== null and strlen($app->getCookie('module_settings') !== null) > 0) {
                $settings_data = $app->getCookie('module_settings') !== null;
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
        else {
            return null;
        }
        
    }

    /*
     * Setters (override for storing information in the User object)
     */

    public static function setNick($nick) {
        if ($nick == '') {
            $nick = null;
        }

        // Set
        self::$user->setNick($nick);
    }
    public static function setModuleSettings($app, $key, $value) {
        // Get the current settings
        $settings = self::getModuleSettings($app);
        
        // Make sure we have a array
        if ($settings == null) {
            $settings = [];
        }
        
        // Apply the new settings
        $settings[$key] = $value;
        
        // Check if we should set cookie for later too
        if (self::$user === null) {
            // Set cookie
            $app->setCookie('module_settings', json_encode($settings));
        }
        else {
            self::$user->setModuleSettings($app, json_encode($settings));
        }
    }
    public static function increaseKarma($karma) {
        self::$user->setKarma(self::$user->getKarma() + $karma);
    }
    public static function increaseKarmaPending($pending) {
        self::$user->setKarmaPending(self::$user->getKarmaPending() + $pending);
    }
    
    /*
     * Conditional stuff
     */
    
    public static function isLoggedIn() {
        return self::$user !== null;
    }
    public static function isAdmin() {
        return (self::$user !== null and (self::$user->getId() == 10000 or self::$user->getId() == 1));
    }
    public static function hasKarma() {
        return self::$user !== null and self::$user->getKarma() > 0;
    }
    public static function canContribute() {
        return (self::hasKarma() and !self::isBanned());
    }
    
    /*
     * Login
     */

    public static function logIn($app) {
        // Check if logged in
        if (!self::$user !== null) {
            // Okey
            if (isset($_POST['login-email']) and isset($_POST['login-pw']) and isset($_POST['_token'])) {
                // Check CSRF token
                if (!CsrfManager::validateSignature($_POST['_token'])) {
                    $app->setStatus(400);
                    return;
                }
                
                // Try to fetch email
                $get_login_user  = "SELECT id, email, password" . PHP_EOL;
                $get_login_user .= "FROM user" . PHP_EOL;
                $get_login_user .= "WHERE email = :email";

                $get_login_user_query = Database::$db->prepare($get_login_user);
                $get_login_user_query->execute(array(':email' => $_POST['login-email']));
                $row = $get_login_user_query->fetch(\PDO::FETCH_ASSOC);

                // Check result
                if (isset($row['id'])) {
                    $hash = Utilities::reverseFuckup($row['password']);
                    // Try to match with password from the database
                    if (password_verify($_POST['login-pw'], $hash)) {
                        // Check remember me
                        if (isset($_POST['login-remember']) and $_POST['login-remember'] == 'remember') {
                            $remember_me = true;
                        }
                        else {
                            $remember_me = true;
                        }

                        // Set login
                        self::setLogin($row['password'], $_POST['login-email'], $remember_me);

                        // Add message
                        MessageManager::addMessage($app, 'Du er nå logget inn.', 'success');

                        // Check if we should redirect the user back to the previous page
                        if (strstr($_SERVER['HTTP_REFERER'], URL) !== false) {
                            // Has referer, remove base
                            $clean_referer = str_replace(URL_FULL, '', $_SERVER['HTTP_REFERER']);

                            // Check if anything left
                            if (strlen($clean_referer) > 0 and $clean_referer != 'logg-inn') {
                                // Refirect to whatever we have left
                                Redirect::send($clean_referer);
                            }
                            else {
                                // Send to frontpage
                                Redirect::send('');
                            }
                        }
                        else {
                            // Does not have referer
                            Redirect::send('');
                        }
                    }
                    else {
                        // Message
                        MessageManager::addMessage($app, 'Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');
                        
                        // Set session
                        $_SESSION['login_correct_email'] = $row['email'];
                        
                        // Redirect
                        Redirect::send('logg-inn');
                    }
                }
                else {
                    // Message
                    MessageManager::addMessage($app, 'Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');

                    // Redirect
                    Redirect::send('logg-inn');
                }
            }
            else {
                // Not submitted or anything, just redirect
                Redirect::send('');
            }
        }
        
        exit();
    }
    
    /*
     * Set the login information
     */
    
    public static function setLogin($hash, $email, $cookie = false) {
        // Remove old login (just in case)
        unset($_SESSION['youkok2']);
        
        // Unset the cookie
        setcookie('youkok2', null, time() - (60 * 60 * 24), '/');
        
        // Set new login
        $strg = $email . 'asdkashdsajheeeeehehdffhaaaewwaddaaawww' . $hash;
        if ($cookie) {
            setcookie('youkok2', $strg, time() + (60 * 60 * 24 * 31), '/');
        }
        else {
            $_SESSION['youkok2'] = $strg;
        }
    }

    /*
     * Logout
     */

    public static function logOut($app) {
        // Check if logged in
        if (self::$user !== null and $_GET['_token']) {
            // Unset session
            $app->clearSession('youkok2');
            
            // Unset token
            $app->clearCookie('youkok2');

            // Set message
            MessageManager::addMessage($app, 'Du har nå logget ut.', 'success');
        }
        else {
            // Simply redirect home
            Redirect::send('');
        }

        // Check if we should redirect the user back to the previous page
        if (strstr($_SERVER['HTTP_REFERER'], URL) !== false) {
            // Has referer, remove base
            $clean_referer = str_replace(URL_FULL, '', $_SERVER['HTTP_REFERER']);
            
            // Check if anything left
            if (strlen($clean_referer) > 0) {
                // Refirect to whatever we have left
                Redirect::send($clean_referer);
            }
            else {
                // Send to frontpage
                Redirect::send('');
            }
        }
        else {
            // Does not have referer
            Redirect::send('');
        }
    }

    /*
     * Favorites
     */

    public static function getFavorites() {
        // Check if already loaded
        if (self::$favorites === null) {
            // Set favorites to array
            self::$favorites = array();

            // Run query
            $get_favorites  = "SELECT f.file" . PHP_EOL;
            $get_favorites .= "FROM favorite AS f" . PHP_EOL;
            $get_favorites .= "LEFT JOIN archive AS a ON a.id = f.file" . PHP_EOL;
            $get_favorites .= "WHERE f.user = :user" . PHP_EOL;
            $get_favorites .= "AND pending = 0" . PHP_EOL;
            $get_favorites .= "AND deleted = 0" . PHP_EOL;
            $get_favorites .= "ORDER BY f.id ASC";

            $get_favorites_query = Database::$db->prepare($get_favorites);
            $get_favorites_query->execute(array(':user' => self::$user->getId()));
            while ($row = $get_favorites_query->fetch(\PDO::FETCH_ASSOC)) {
                self::$favorites[] = Element::get($row['file']);
            }
        }

        // Return entire list of elements
        return self::$favorites;
    }
    
    /*
     * Check if one Element is favorite
     */
    
    public static function isFavorite($id) {
        // Check if we should load
        if (self::$favorites === null) {
            self::getFavorites();
        }
        
        if (in_array($id, self::$favorites)) {
            return true;
        }
        
        // If we came all this was, it is not a favorite
        return false;
    }

    /*
     * Get user karma elements
     */

    public static function getKarmaElements() {
        $collection = [];

        // Run query
        $get_user_karma_elements  = "SELECT id, user, file, value, pending, state, added" . PHP_EOL;
        $get_user_karma_elements .= "FROM karma" . PHP_EOL;
        $get_user_karma_elements .= "WHERE user = :user" . PHP_EOL;
        $get_user_karma_elements .= "ORDER BY added DESC";

        $get_user_karma_elements_query = Database::$db->prepare($get_user_karma_elements);
        $get_user_karma_elements_query->execute(array(':user' => self::$user->getId()));
        while ($row = $get_user_karma_elements_query->fetch(\PDO::FETCH_ASSOC)) {
            $collection[] = new Karma($row);
        }

        // Return elements
        return $collection;
    }
    
    /*
     * Override save and update because the __callStatic method does not work for these calls
     */
    
    public static function update() {
        if (self::$user !== null) {
            self::$user->update();
        }
    }
    public static function save() {
        if (self::$user !== null) {
            self::$user->save();
        }
    }
    
    /*
     * Static functions overload
     */
    
    public static function __callStatic($name, $arguments) {
        // Check if method exists
        if (self::$user != null and method_exists(self::$user, $name)) {
            // Call method and return response
            return call_user_func_array([self::$user,
                $name], $arguments);
        }
    }
}
