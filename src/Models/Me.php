<?php
/*
 * File: Me.php
 * Holds: Static class for the current user
 * Created: 06.11.2014
 * Project: Youkok2
*/

namespace Youkok2\Models;

/*
 * Define what classes to use
 */

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;
use \Youkok2\Utilities\MessageManager as MessageManager;
use \Youkok2\Utilities\Redirect as Redirect;

/*
 * The class Me, called statically
 */

class Me {
    
    /*
     * Variables for the user
     */
    
    private static $id;
    private static $email;
    private static $nick;
    private static $mostPopularDelta;
    private static $karma;
    private static $karmaPending;
    private static $banned;
    
    // Other variables
    private static $loggedIn;
    private static $favorites;
    
    public static function init() {
        // Set initial
        self::$loggedIn = false;
        self::$nick = null;
        self::$favorites = null;

        // Check if we have anything stored
        if (isset($_SESSION['youkok2']) or isset($_COOKIE['youkok2'])) {
            if (isset($_COOKIE['youkok2'])) {
                $hash = $_COOKIE['youkok2'];
            }
            else {
                $hash = $_SESSION['youkok2'];
            }

            // Try to split
            $hash_split = explode('asdkashdsajheeeeehehdffhaaaewwaddaaawww', $hash);
            if (count($hash_split) == 2) {
                // Fetch from database to see if online
                $get_current_user = "SELECT id, email, nick, karma, karma_pending, banned, most_popular_delta
                FROM user 
                WHERE email = :email
                AND password = :password";
                
                $get_current_user_query = Database::$db->prepare($get_current_user);
                $get_current_user_query->execute(array(':email' => $hash_split[0], 
                                                       ':password' => $hash_split[1]));
                $row = $get_current_user_query->fetch(\PDO::FETCH_ASSOC);

                // Check if anything want returned
                if (isset($row['id'])) {
                    // The user is logged in, gogo
                    self::$loggedIn = true;

                    // Set attributes
                    self::$id = $row['id'];
                    self::$email = $row['email'];
                    self::$nick = (($row['nick'] == null or strlen($row['nick']) == 0) ? '<em>Anonym</em>' : $row['nick']);
                    self::$karma = $row['karma'];
                    self::$karmaPending = $row['karma_pending'];
                    self::$banned = (boolean) $row['banned'];
                    self::$mostPopularDelta = $row['most_popular_delta'];

                    // Update last seen
                    self::updateLastSeen();
                }
                else {
                    // Unset all
                    unset($_SESSION['youkok2']);
                    setcookie('youkok2', null, time() - (60 * 60 * 24), '/');
                }
            }
        }
    }
    
    /*
     * Getters for the database information
     */
    
    public static function getId() {
        return self::$id;
    }
    public static function getEmail() {
        return self::$email;
    }
    public static function getNick() {
        return self::$nick;
    }
    public static function getMostPopularDelta() {
        return self::getUserDelta();
    }
    public static function getKarma() {
        return self::$karma;
    }
    public static function getKarmaPending() {
        return self::$karmaPending;
    }
    public static function isBanned() {
        return self::$banned;
    }
    
    /*
     * Other getters
     */
    
    public static function isLoggedIn() {
        return self::$loggedIn;
    }
    public static function isAdmin() {
        return self::$id == 10000;
    }
    public static function hasKarma() {
        return self::$karma > 0;
    }
    public static function canContribute() {
        return (self::hasKarma() and !self::isBanned());
    }
    
    /*
     * Other stuff
     */
    
    public static function updateLastSeen() {
        //
    }
    
    public static function getUserDelta($override = null) {
        if ($override == null) {
            if (self::isLoggedIn()) {
                return self::$mostPopularDelta;
            }
            else {
                if (isset($_COOKIE['home_popular'])) {
                    return $_COOKIE['home_popular'];
                }
                else {
                    return 1;
                }
            }
        }
        else {
            return $override;
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
            $get_favorites = "SELECT f.file
            FROM favorite AS f
            LEFT JOIN archive AS a ON a.id = f.file
            WHERE f.user = :user
            AND a.is_visible = 1
            ORDER BY f.ordering ASC";
            
            $get_favorites_query = Database::$db->prepare($get_favorites);
            $get_favorites_query->execute(array(':user' => self::$id));
            while ($row = $get_favorites_query->fetch(\PDO::FETCH_ASSOC)) {
                self::$favorites[] = $row['file'];
            }
        }
        
        // Return entire list of elements
        return self::$favorites;
    }

    /*
     * Login
     */

    public static function logIn() {
        // Check if logged in
        if (!self::isLoggedIn()) {
            // Okey
            if (isset($_POST['login-email']) and isset($_POST['login-pw'])) {
                // Try to fetch email
                $get_login_user = "SELECT id, email, salt, password
                FROM user
                WHERE email = :email";

                $get_login_user_query = Database::$db->prepare($get_login_user);
                $get_login_user_query->execute(array(':email' => $_POST['login-email']));
                $row = $get_login_user_query->fetch(\PDO::FETCH_ASSOC);

                // Check result
                if (isset($row['id'])) {
                    // Try to match password
                    $hash = Utilities::hashPassword($_POST['login-pw'], $row['salt']);

                    // Try to match with password from the database
                    if ($hash === $row['password']) {
                        // Check remember me
                        if (isset($_POST['login-remember']) and $_POST['login-remember'] == 'pizza') {
                            $remember_me = true;
                        }
                        else {
                            $remember_me = true;
                        }

                        // Set login
                        self::setLogin($hash, $_POST['login-email'], $remember_me);

                        // Add message
                        MessageManager::addMessage('Du er nå logget inn.', 'success');

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
                    else {
                        // Message
                        MessageManager::addMessage('Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');

                        $_SESSION['login_correct_email'] = $row['email'];

                        Redirect::send('logg-inn');
                    }
                }
                else {
                    // Message
                    MessageManager::addMessage('Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');

                    // Display
                    Redirect::send('logg-inn');
                }
            }
            else {
                // Not submitted or anything, just redirect
                Redirect::send('');
            }
        }
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

    public static function logOut() {
        //
    }
    
    /*
     * Get latest downloads
     */
    
    public static function loadLastDownloads() {
        // Declear variable for storing content
        $ret = '';
        
        // Load all favorites
        $get_last_downloads = "SELECT d.file
        FROM download AS d
        LEFT JOIN archive AS a ON a.id = d.file
        WHERE d.user = :user
        AND a.is_visible = 1
        AND d.id = (
            SELECT dd.id
            FROM download dd
            WHERE d.file = dd.file
            ORDER BY dd.downloaded_time
            DESC LIMIT 1)
        ORDER BY d.downloaded_time DESC
        LIMIT 15";
        
        $get_last_downloads_query = Database::$db->prepare($get_last_downloads);
        $get_last_downloads_query->execute(array(':user' => Self::getId()));
        while ($row = $get_last_downloads_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get element
            $element = ElementCollection::get($row['file']);

            // Get file if not cached
            if ($element == null) {
                $element = new Element();
                $element->controller->setLoadRootParent(true);
                $element->createById($row['file']);
                ElementCollection::add($element);
            }

            // Check if valid Element
            if ($element->controller->wasFound()) {
                ElementCollection::add($element);
                $ret .= $element->controller->getFrontpageLink('latestdownloaded');
            }
        }
        
        // Check if null
        if ($ret == '') {
            $ret .= '<li class="list-group-item"><em>Du har ikke lastet ned noen filer enda...</em></li>';
        }

        // Return the content
        return $ret;
    }
}