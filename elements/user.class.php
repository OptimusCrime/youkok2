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
    
    //
    // Constructor
    //

    public function __construct($db, $template) {
        // Set pointers
        $this->db = $db;
        $this->template = $template;

        // Fetch from database to see if online
        $get_current_user = "SELECT id, email, nick, ntnu_verified, karma, banned
        FROM user 
        WHERE id = :id";
        
        $get_current_user_query = $this->db->prepare($get_current_user);
        $get_current_user_query->execute(array(':id' => 1));
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

            // Update last seen TODO
        }
        else {
            // The user is not logged in, yay
            $this->loggedIn = false;
            $this->nick = null;
        }

        // Generate top menu
        $this->template->assign('BASE_USER_IS_LOGGED_IN', $this->loggedIn);
        $this->template->assign('BASE_USER_NICK', $this->nick);
        $this->template->assign('BASE_USER_KARMA', $this->prettifyKarma($this->karma));
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
    // Returning if the current user is verified or not
    //
    
    public function isVerified() {
        return $this->isVerified;
    }

    //
    // Function for prettifying karma displayed in the header
    //

    private function prettifyKarma($s) {
        if ($s <= 0) {
            return 0;
        }
        else {
            $temp_string = (string) $s;
            $temp_string_split = explode('.', $temp_string);
            if ($temp_string_split[1] == '0') {
                return $temp_string_split[0];
            }
            else {
                return $s;
            }
        }
    }
}
?>