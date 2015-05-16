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
    private static $password;
    private static $nick;
    private static $mostPopularDelta;
    private static $lastSeen;
    private static $karma;
    private static $karmaPending;
    private static $banned;
    private static $initialData = [];

    // Other variables
    private static $loggedIn;
    private static $favorites;
    private static $inited;

    /*
     * Init the user
     */

    public static function init() {
        // Only run if not already inited
        if (self::$inited === null or !self::$inited) {
            // Set initial
            self::$inited = true;
            self::$loggedIn = false;
            self::$nick = null;
            self::$favorites = null;

            // Check if we have anything stored
            if (isset($_SESSION['youkok2']) or isset($_COOKIE['youkok2'])) {
                if (isset($_COOKIE['youkok2'])) {
                    $hash = $_COOKIE['youkok2'];

                    // Set session as well
                    $_SESSION['youkok2'] = $_COOKIE['youkok2'];
                }
                else {
                    $hash = $_SESSION['youkok2'];
                }

                // Try to split
                $hash_split = explode('asdkashdsajheeeeehehdffhaaaewwaddaaawww', $hash);
                if (count($hash_split) == 2) {
                    // Fetch from database to see if online
                    $get_current_user  = "SELECT id, email, password, nick, most_popular_delta, last_seen, karma, karma_pending, banned" . PHP_EOL;
                    $get_current_user .= "FROM user " . PHP_EOL;
                    $get_current_user .= "WHERE email = :email" . PHP_EOL;
                    $get_current_user .= "AND password = :password";
                    
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
                        self::$password = $row['password'];
                        self::$nick = (($row['nick'] == null or strlen($row['nick']) == 0) ? '<em>Anonym</em>' : $row['nick']);
                        self::$mostPopularDelta = (int) $row['most_popular_delta'];
                        self::$lastSeen = (int)  $row['last_seen'];
                        self::$karma = (int) $row['karma'];
                        self::$karmaPending = (int) $row['karma_pending'];
                        self::$banned = (boolean) $row['banned'];


                        // Set to initial data
                        self::$initialData = [
                            'id' => array('value' => self::$id),
                            'email' => array('value' => self::$email),
                            'password' => array('value' => self::$password),
                            'nick' => array('value' => self::$nick),
                            'mostPopularDelta' => array('value' => self::$mostPopularDelta, 'db' => 'most_popular_delta'),
                            'lastSeen' => array('value' => self::$lastSeen, 'db' => 'last_seen'),
                            'karma' => array('value' => self::$karma),
                            'karmaPending' => array('value' => self::$karmaPending, 'db' => 'karma_pending'),
                            'banned' => array('value' => self::$banned),
                        ];

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
    public static function getPassword() {
        return self::$password;
    }
    public static function getNick() {
        return self::$nick;
    }
    public static function getNickReal() {
        if (self::$nick == '<em>Anonym</em>') {
            return '';
        }
        else {
            return self::$nick;
        }
    }
    public static function getMostPopularDelta() {
        return self::$mostPopularDelta;
    }
    public static function getLastSeen() {
        return self::$lastSeen;
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
     * Setters for the database information
     */

    public static function setId($id) {
        self::$id = $id;
    }
    public static function setEmail($email) {
        self::$email = $email;
    }
    public static function setPassword($password) {
        self::$password = $password;
    }
    public static function setNick($nick) {
        if ($nick == '') {
            $nick = '<em>Anonym</em>';
        }

        // Set
        self::$nick = $nick;
    }
    public static function setMostPopularDelta($delta) {
        self::$mostPopularDelta = $delta;
    }
    public static function increaseKarma($karma) {
        self::$karma += $karma;
    }
    public static function increaseKarmaPending($pending) {
        self::$karmaPending += $pending;
    }
    public static function setBanned($banned) {
        self::$banned = $banned;
    }

    /*
     * Save
     */

    public static function save() {
        $create_user  = "INSERT INTO user" . PHP_EOL;
        $create_user .= "(email, password, nick)" . PHP_EOL;
        $create_user .= "VALUES (:email, :password, :nick)";

        $create_user_query = Database::$db->prepare($create_user);
        $create_user_query->execute([':email' => self::$email,
            ':password' => self::$password,
            ':nick' => self::$nick]);
    }

    /*
     * Update
     */

    public static function update() {
        // Check what should be updated
        $updated = [];

        foreach (self::$initialData as $k => $v) {
            if (self::$$k != $v['value']) {
                // Get data
                $value = self::$$k;
                if ($k == 'nick') {
                    $value = self::getNickReal();
                }

                // Find database field
                if (isset($v['db'])) {
                    $updated[] = array('field' => $v['db'], 'value' => $value);
                }
                else {
                    $updated[] = array('field' => $k, 'value' => $value);
                }
            }
        }

        // Build sub query
        $subquery = [];
        $binds = [];
        
        // Check if anything was updated
        if (count($updated) > 0) {
            // Loop the updated and build sub query
            foreach ($updated as $v) {
                $subquery[] = $v['field'] . ' = :' . $v['field'];
                $binds[':' . $v['field']] = $v['value'];
            }

            // Add user id
            $binds[':id'] = self::$id;

            // Build query
            try {
                $query = 'UPDATE user SET ' . implode(', ', $subquery) . ' WHERE id = :id';
                
                // Run query
                $create_user_query = Database::$db->prepare($query);
                $create_user_query->execute($binds);
            }
            catch (\PDOException $e) {
                print_r($e->getMessage());
                die();
            }
        }
    }
    
    /*
     * Other getters
     */
    
    public static function isLoggedIn() {
        return self::$loggedIn;
    }
    public static function isAdmin() {
        return (self::$id == 10000 or self::$id == 1);
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
            $get_favorites .= "AND a.is_visible = 1" . PHP_EOL;
            $get_favorites .= "ORDER BY f.id ASC";
            
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
     * Login
     */

    public static function logIn() {
        // Check if logged in
        if (!self::isLoggedIn()) {
            // Okey
            if (isset($_POST['login-email']) and isset($_POST['login-pw'])) {
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
                        MessageManager::addMessage('Du er nå logget inn.', 'success');

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
                        MessageManager::addMessage('Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');
                        
                        // Set session
                        $_SESSION['login_correct_email'] = $row['email'];
                        
                        // Redirect
                        Redirect::send('logg-inn');
                    }
                }
                else {
                    // Message
                    MessageManager::addMessage('Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');

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

    public static function logOut() {
        // Check if logged in
        if (self::isLoggedIn()) {
            unset($_SESSION['youkok2']);
            setcookie('youkok2', null, time() - (60 * 60 * 24), '/');

            // Set message
            MessageManager::addMessage('Du har nå logget ut.', 'success');
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
     * Get latest downloads
     */
    
    public static function loadLastDownloads() {
        // Declear variable for storing content
        $ret = '';
        
        // Load all favorites
        $get_last_downloads  = "SELECT d.file" . PHP_EOL;
        $get_last_downloads .= "FROM download AS d" . PHP_EOL;
        $get_last_downloads .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_last_downloads .= "WHERE d.user = :user" . PHP_EOL;
        $get_last_downloads .= "AND a.is_visible = 1" . PHP_EOL;
        $get_last_downloads .= "AND d.id = (" . PHP_EOL;
        $get_last_downloads .= "    SELECT dd.id" . PHP_EOL;
        $get_last_downloads .= "    FROM download dd" . PHP_EOL;
        $get_last_downloads .= "    WHERE d.file = dd.file" . PHP_EOL;
        $get_last_downloads .= "    ORDER BY dd.downloaded_time" . PHP_EOL;
        $get_last_downloads .= "    DESC LIMIT 1)" . PHP_EOL;
        $get_last_downloads .= "ORDER BY d.downloaded_time DESC" . PHP_EOL;
        $get_last_downloads .= "LIMIT 15";
        
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

    /*
     * Set user status
     */

    public static function setUserStatus(&$scope, $prefix) {
        // User status
        $scope->template->assign($prefix . '_USER_BANNED', Me::isBanned());
        $scope->template->assign($prefix . '_USER_HAS_KARMA', Me::hasKarma());
        $scope->template->assign($prefix . '_USER_CAN_CONTRIBUTE', Me::canContribute());
        $scope->template->assign($prefix . '_USER_ONLINE', Me::isLoggedIn());
    }

    /*
     * Get user karma elements
     */

    public static function getKarmaElements() {
        $elements = [];

        // Run query
        $get_user_karma_elements  = "SELECT id" . PHP_EOL;
        $get_user_karma_elements .= "FROM history" . PHP_EOL;
        $get_user_karma_elements .= "WHERE user = :user" . PHP_EOL;
        $get_user_karma_elements .= "ORDER BY added DESC";

        $get_user_karma_elements_query = Database::$db->prepare($get_user_karma_elements);
        $get_user_karma_elements_query->execute(array(':user' => Me::getId()));
        while ($row = $get_user_karma_elements_query->fetch(\PDO::FETCH_ASSOC)) {
            $elements[] = $row['id'];
        }

        // Return elements
        return $elements;
    }
}