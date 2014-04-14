<?php
/*
 * File: user.php
 * Holds: Holds all the user-related stuff
 * Created: 02.10.13
 * Last updated: 12.04.14
 * Project: Youkok2
 * 
*/

//
// Class what represents the user
//

Class User {

    //
    // The internal variables
    //
    
    private $db;
    private $template;

    private $loggedIn;

    private $id;
    private $email;
    private $nick;
    private $NTNU;
    private $karma;
    private $banned;
    private $mostPopularDelta;
    
    //
    // Constructor
    //

    public function __construct($db, $template) {
        // Set pointers
        $this->db = $db;
        $this->template = $template;

        // Set initial
        $this->loggedIn = false;
        $this->nick = null;

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
                $get_current_user = "SELECT id, email, nick, ntnu_verified, karma, banned, most_popular_delta
                FROM user 
                WHERE email = :email
                AND password = :password";
                
                $get_current_user_query = $this->db->prepare($get_current_user);
                $get_current_user_query->execute(array(':email' => $hash_split[0], ':password' => $hash_split[1]));
                $row = $get_current_user_query->fetch(PDO::FETCH_ASSOC);

                // Check if anything want returned
                if (isset($row['id'])) {
                    // The user is logged in, gogo
                    $this->loggedIn = true;

                    // Set attributes
                    $this->id = $row['id'];
                    $this->email = $row['email'];
                    $this->nick = $row['nick'] == null ? '<em>Anonym</em>' : $row['nick'];
                    $this->NTNU = (boolean) $row['ntnu_verified'];
                    $this->karma = $row['karma'];
                    $this->banned = (boolean) $row['banned'];
                    $this->mostPopularDelta = $row['most_popular_delta'];
                }
                else {
                    // Unset all
                    unset($_SESSION['youkok2']);
                    unset($_COOKIE['youkok2']);
                }
            }
        }

        // Generate top menu
        $this->template->assign('BASE_USER_IS_LOGGED_IN', $this->loggedIn);
        $this->template->assign('BASE_USER_NICK', $this->nick);
        $this->template->assign('BASE_USER_KARMA', $this->karma);
    }
    
    //
    // Returning if the user is logged in or not
    //
    
    public function isLoggedIn() {
        return $this->loggedIn;
    }

    //
    // Getters
    //

    public function getId() {
        return $this->id;
    }
    
    public function getNick() {
        return $this->nick;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getKarma() {
        return $this->karma;
    }

    public function getMostPopularDelta() {
        return $this->mostPopularDelta;
    }

    //
    // Returns if the user is banned or not
    //

    public function isBanned() {
        return $this->banned;
    }
    
    //
    // Returning if the current user is NTNU user or not
    //
    
    public function isNTNU() {
        return $this->NTNU;
    }
    
    //
    // Returning if the current user is verified or not (alias)
    //
    
    public function isVerified() {
        return $this->NTNU;
    }
}
?>