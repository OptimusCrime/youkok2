<?php
namespace Youkok2\Models;

use Youkok2\Utilities\CsrfManager;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\TemplateHelper;

class Me
{

    private $app;
    private $user;
    private $favorites;

    public function __construct($app) {
        $this->user = null;
        $this->favorites = null;

        $this->app = $app;

        if ($this->app->getSession('youkok2') !== null or $this->app->getCookie('youkok2') !== null) {
            if ($this->app->getCookie('youkok2') !== null) {
                $hash = $this->app->getCookie('youkok2');

                $this->app->setSession('youkok2', $hash);
            } else {
                $hash = $this->app->getSession('youkok2');
            }

            $hash_split = explode('asdkashdsajheeeeehehdffhaaaewwaddaaawww', $hash);
            if (count($hash_split) == 2) {
                $get_current_user = "SELECT id, email, password, nick, module_settings, last_seen, " . PHP_EOL;
                $get_current_user .= "karma, karma_pending, banned" . PHP_EOL;
                $get_current_user .= "FROM user " . PHP_EOL;
                $get_current_user .= "WHERE email = :email" . PHP_EOL;
                $get_current_user .= "AND password = :password";

                $get_current_user_query = Database::$db->prepare($get_current_user);
                $get_current_user_query->execute([':email' => $hash_split[0],
                    ':password' => $hash_split[1]]);
                $row = $get_current_user_query->fetch(\PDO::FETCH_ASSOC);

                if (isset($row['id'])) {
                    $this->user = new User($row);
                }
            }

            if ($this->user === null) {
                $this->app->clearSession('youkok2');
                $this->app->clearCookie('youkok2');
            }
        }
    }
    
    public function create() {
        $this->user = new User();
    }

    public function getUser() {
        return $this->user;
    }
    
    public function getModuleSettings($key = null) {
        $settings_data = null;
        if ($this->user !== null) {
            $settings_data = $this->user->getModuleSettings();
        }
        else {
            if ($this->app->getCookie('module_settings') !== null and
                strlen($this->app->getCookie('module_settings')) > 0) {
                $settings_data = $this->app->getCookie('module_settings');
            }
        }

        if ($settings_data != null) {
            $settings_data_decoded = json_decode($settings_data, true);
            
            if (is_array($settings_data_decoded)) {
                if ($key == null) {
                    return $settings_data_decoded;
                }
                else {
                    if (isset($settings_data_decoded[$key])) {
                        return $settings_data_decoded[$key];
                    }
                }
            }
        }
        
        if ($key == null) {
            return null;
        }
        elseif ($key == 'module1_delta' or $key == 'module2_delta') {
            return 3;
        }
        
        return null;
        
    }

    public function setNick($nick) {
        if ($nick == '') {
            $nick = null;
        }

        $this->user->setNick($nick);
    }
    public function setModuleSettings($key, $value) {
        $settings = $this->getModuleSettings();
        
        if ($settings == null) {
            $settings = [];
        }
        
        $settings[$key] = $value;
        
        if ($this->user === null) {
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

    public function logIn() {
        if ($this->user === null) {
            if ($this->app->getPost('login-email') !== null and $this->app->getPost('login-pw') !== null and
                $this->app->getPost('_token') !== null) {
                try {
                    CsrfManager::validateSignature($this->app->getPost('_token'));
                }
                catch (\Exception $e) {
                    $this->app->setStatus(400);
                    return;
                }
                
                $get_login_user  = "SELECT id, email, password, nick, module_settings, last_seen, " . PHP_EOL;
                $get_login_user .= "karma, karma_pending, banned" . PHP_EOL;
                $get_login_user .= "FROM user" . PHP_EOL;
                $get_login_user .= "WHERE email = :email";

                $get_login_user_query = Database::$db->prepare($get_login_user);
                $get_login_user_query->execute([':email' => $this->app->getPost('login-email')]);
                $row = $get_login_user_query->fetch(\PDO::FETCH_ASSOC);

                if (isset($row['id'])) {
                    $hash = Utilities::reverseFuckup($row['password']);
                    if (password_verify($this->app->getPost('login-pw'), $hash)) {
                        if ($this->app->getPost('login-remember') == 'remember') {
                            $remember_me = true;
                        }
                        else {
                            $remember_me = false;
                        }

                        $this->setLogin($row['password'], $this->app->getPost('login-email'), $remember_me);

                        $this->user = new User($row);

                        MessageManager::addMessage($this->app, 'Du er nå logget inn.', 'success');

                        if ($this->app->getServer('HTTP_REFERER') !== null and
                            strpos($this->app->getServer('HTTP_REFERER'), URL) !== false) {
                            $clean_referer = str_replace(URL_FULL, '', $this->app->getServer('HTTP_REFERER'));

                            if (strlen($clean_referer) > 0 and $clean_referer != 'logg-inn') {
                                $this->app->send($clean_referer);
                            }
                            else {
                                $this->app->send('');
                            }
                        }
                        else {
                            $this->app->send('');
                        }
                    }
                    else {
                        MessageManager::addMessage(
                            $this->app,
                            'Oiisann. Feil brukernavn og/eller passord. Prøv igjen.',
                            'danger'
                        );
                        
                        $this->app->setSession('login_correct_email', $row['email']);
                        
                        $this->app->send(TemplateHelper::urlFor('auth_login'));
                    }
                }
                else {
                    MessageManager::addMessage(
                        $this->app,
                        'Oiisann. Feil brukernavn og/eller passord. Prøv igjen.',
                        'danger'
                    );
                    
                    $this->app->send(TemplateHelper::urlFor('auth_login'));
                }
            }
            else {
                $this->app->send('');
            }
        }
    }
    
    public function setLogin($hash, $email, $cookie = false) {
        $this->app->clearSession('youkok2');
        
        $this->app->clearCookie('youkok2');
        
        $strg = Me::generateLoginString($hash, $email);

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

    public function logOut() {
        if ($this->user !== null and $this->app->getGet('_token')) {
            $this->app->clearSession('youkok2');
            
            $this->app->clearCookie('youkok2');

            MessageManager::addMessage($this->app, 'Du har nå logget ut.', 'success');
        }
        else {
            $this->app->send('');
        }

        if ($this->app->getServer('HTTP_REFERER') !== null and
            strstr($this->app->getServer('HTTP_REFERER'), URL) !== false) {
            $clean_referer = str_replace(URL_FULL, '', $this->app->getServer('HTTP_REFERER'));
            
            if (strlen($clean_referer) > 0) {
                $this->app->send($clean_referer);
            }
            else {
                $this->app->send('');
            }
        }
        else {
            $this->app->send('');
        }
    }

    /*
     * Favorites
     */

    public function getFavorites() {
        if ($this->favorites === null) {
            $this->favorites = [];

            if ($this->user === null) {
                return $this->favorites;
            }

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

        return $this->favorites;
    }
    
    public function isFavorite($id) {
        if ($this->favorites === null) {
            $this->getFavorites();
        }

        foreach ($this->favorites as $v) {
            if ($v->getId() == $id) {
                return true;
            }
        }

        return false;
    }

    public function getKarmaElements() {
        $collection = [];

        $get_user_karma_elements  = "SELECT id, user, file, value, pending, state, added" . PHP_EOL;
        $get_user_karma_elements .= "FROM karma" . PHP_EOL;
        $get_user_karma_elements .= "WHERE user = :user" . PHP_EOL;
        $get_user_karma_elements .= "ORDER BY added DESC";

        $get_user_karma_elements_query = Database::$db->prepare($get_user_karma_elements);
        $get_user_karma_elements_query->execute([':user' => $this->user->getId()]);
        while ($row = $get_user_karma_elements_query->fetch(\PDO::FETCH_ASSOC)) {
            $collection[] = new Karma($row);
        }

        return $collection;
    }
    
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
    
    public function __call($name, $arguments) {
        if ($this->user != null and method_exists($this->user, $name)) {
            return call_user_func_array([$this->user,
                $name], $arguments);
        }
    }
}
