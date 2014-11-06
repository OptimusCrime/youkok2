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

use Youkok2\Utilities\Database;

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
    
    public static function init() {
        // Set initial
        self::$loggedIn = false;
        self::$nick = null;

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
        return (self::$id == 10000);
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
}