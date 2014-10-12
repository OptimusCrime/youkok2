<?php
/*
 * File: user.php
 * Holds: Holds all the user-related stuff
 * Created: 02.10.13
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
    
    // Pointer to the controller
    private $controller;

    // Fields in the database
    private $id;
    private $email;
    private $nick;
    private $mostPopularDelta;
    private $karma;
    private $karmaPending;
    private $banned;
    
    // Other variables
    private $loggedIn;

    //
    // Constructor
    //

    public function __construct($controller) {
        // Set pointers
        $this->controller = &$controller;

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
                $get_current_user = "SELECT id, email, nick, karma, karma_pending, banned, most_popular_delta
                FROM user 
                WHERE email = :email
                AND password = :password";
                
                $get_current_user_query = $this->controller->db->prepare($get_current_user);
                $get_current_user_query->execute(array(':email' => $hash_split[0], ':password' => $hash_split[1]));
                $row = $get_current_user_query->fetch(PDO::FETCH_ASSOC);

                // Check if anything want returned
                if (isset($row['id'])) {
                    // The user is logged in, gogo
                    $this->loggedIn = true;

                    // Set attributes
                    $this->id = $row['id'];
                    $this->email = $row['email'];
                    $this->nick = (($row['nick'] == null or strlen($row['nick']) == 0) ? '<em>Anonym</em>' : $row['nick']);
                    $this->karma = $row['karma'];
                    $this->karmaPending = $row['karma_pending'];
                    $this->banned = (boolean) $row['banned'];
                    $this->mostPopularDelta = $row['most_popular_delta'];

                    // Update last seen
                    $this->updateLastSeen();
                }
                else {
                    // Unset all
                    unset($_SESSION['youkok2']);
                    setcookie('youkok2', null, time() - (60 * 60 * 24), '/');
                }
            }
        }

        // Generate top menu
        $this->controller->template->assign('BASE_USER_IS_LOGGED_IN', $this->loggedIn);
        $this->controller->template->assign('BASE_USER_NICK', $this->nick);
        $this->controller->template->assign('BASE_USER_KARMA', $this->karma);
        $this->controller->template->assign('BASE_USER_KARMA_PENDING', $this->karmaPending);
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

    public function getKarmaPending() {
        return $this->karmaPending;
    }

    public function getMostPopularDelta() {
        return $this->mostPopularDelta;
    }

    //
    // Multiple states
    //

    public function isBanned() {
        return $this->banned;
    }
    
    public function hasKarma() {
        return $this->karma > 0;
    }

    public function canContribute() {
        return $this->karma > 0 and !$this->banned;
    }

    //
    // Setters
    //

    public function setMostPopularDelta($delta) {
        $update_most_popular_delta = "UPDATE user
        SET most_popular_delta = :delta
        WHERE id = :id";
        
        $update_most_popular_delta_query = $this->controller->db->prepare($update_most_popular_delta);
        $update_most_popular_delta_query->execute(array(':delta' => $delta, ':id' => $this->id));
    }

    //
    // Function for updating the last_seen field to the current timestamp
    //

    private function updateLastSeen() {
        $update_last_seen = "UPDATE user
        SET last_seen = NOW()
        WHERE id = :id";
        
        $update_last_seen_query = $this->controller->db->prepare($update_last_seen);
        $update_last_seen_query->execute(array(':id' => $this->id));
    }


    //
    // Login
    //

    public function logIn() {
        // Check if logged in
        if (!$this->isLoggedIn()) {
            // Okey
            if (isset($_POST['login-email']) and isset($_POST['login-pw'])) {
                // Try to fetch email
                $get_login_user = "SELECT id, email, salt, password
                FROM user 
                WHERE email = :email";
                
                $get_login_user_query = $this->controller->db->prepare($get_login_user);
                $get_login_user_query->execute(array(':email' => $_POST['login-email']));
                $row = $get_login_user_query->fetch(PDO::FETCH_ASSOC);

                // Check result
                if (isset($row['id'])) {
                    // Try to match password
                    $hash = $this->hashPassword($_POST['login-pw'], $row['salt']);
                    
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
                        $this->setLogin($hash, $_POST['login-email'], $remember_me);

                        // Add message
                        $this->controller->addMessage('Du er nå logget inn.', 'success');
                        
                        // Check if we should redirect the user back to the previous page
                        if (strstr($_SERVER['HTTP_REFERER'], SITE_URL) !== false) {
                            // Has referer, remove base
                            $clean_referer = str_replace(SITE_URL_FULL, '', $_SERVER['HTTP_REFERER']);
                            
                            // Check if anything left
                            if (strlen($clean_referer) > 0) {
                                // Refirect to whatever we have left
                                $this->controller->redirect($clean_referer);
                            }
                            else {
                                // Send to frontpage
                                $this->controller->redirect('');
                            }
                        }
                        else {
                            // Does not have referer
                            $this->controller->redirect('');
                        }
                    }
                    else {
                        // Message
                        $this->controller->addMessage('Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');
                        
                        $_SESSION['login_correct_email'] = $row['email'];

                        $this->controller->redirect('logg-inn');
                    }
                }
                else {
                    // Message
                    $this->controller->addMessage('Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');
                    
                    // Display
                    $this->controller->redirect('logg-inn');
                }
            }
            else {
                // Not submitted or anything, just redirect
                $this->controller->redirect('');
            }
        }
    }


    //
    // Method for logging user out
    //

    public function logOut() {
        // Check if logged in
        if ($this->isLoggedIn()) {
            unset($_SESSION['youkok2']);
            setcookie('youkok2', null, time() - (60 * 60 * 24), '/');

            // Set message
            $this->controller->addMessage('Du har nå logget ut.', 'success');
        }

        // Check if we should redirect the user back to the previous page
        if (strstr($_SERVER['HTTP_REFERER'], SITE_URL) !== false) {
            // Has referer, remove base
            $clean_referer = str_replace(SITE_URL_FULL, '', $_SERVER['HTTP_REFERER']);
            
            // Check if anything left
            if (strlen($clean_referer) > 0) {
                // Refirect to whatever we have left
                $this->controller->redirect($clean_referer);
            }
            else {
                // Send to frontpage
                $this->controller->redirect('');
            }
        }
        else {
            // Does not have referer
            $this->controller->redirect('');
        }
    }

    //
    // Hash password
    //

    public function hashPassword($pass, $salt, $hard = true) {
        // Create hash
        $hash = password_hash($pass, PASSWORD_BCRYPT, array('cost' => 12, 'salt' => $salt));

        // Check if the hash should be fucked up in addition
        if ($hard) {
            return $this->controller->utils->passwordFuckup($hash);
        }
        else {
            return $hash;
        }
    }

    public function setLogin($hash, $email, $cookie = false) {
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

    //
    // Favorite
    //

    public function addFavorite($item) {
        $insert_favorite = "INSERT INTO favorite
        (file, user)
        VALUES (:file, :user)";
        
        $insert_favorite_query = $this->controller->db->prepare($insert_favorite);
        $insert_favorite_query->execute(array(':file' => $item->getId(), ':user' => $this->id));
    }

    public function removeFavorite($item) {
        $remove_favorite = "DELETE FROM favorite
        WHERE file = :file
        AND user = :user";
        
        $remove_favorite_query = $this->controller->db->prepare($remove_favorite);
        $remove_favorite_query->execute(array(':file' => $item->getId(), ':user' => $this->id));
    }

    //
    // Add karma
    //

    public function addPendingKarma($value) {
        $this->karmaPending += $value;

        $update_karma_pending = "UPDATE user
        SET karma_pending = :karma_pending
        WHERE id = :id";
        
        $update_karma_pending_query = $this->controller->db->prepare($update_karma_pending);
        $update_karma_pending_query->execute(array(':karma_pending' => $this->karmaPending, 
                                                   ':id' => $this->id));
    }
}