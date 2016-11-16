<?php
namespace Youkok2\Views;

use Youkok2\Models\Me;
use Youkok2\Utilities\Loader;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\TemplateHelper;
use Youkok2\Utilities\Utilities;

class Profile extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
        
        $this->template->assign('HEADER_MENU', '');

        if (!$this->me->isLoggedIn()) {
            $this->application->send('', false, 403);
        }
    }
    
    public function profileRedirect() {
        $this->application->send(TemplateHelper::url_for('profile_settings'));
    }
    
    public function profileSettings() {
        $this->addSiteData('view', 'profile');
        
        if (!isset($_POST['source'])) {
            $this->template->assign('SITE_TITLE', 'Mine innstillinger');
            $this->template->assign('USER_EMAIL', $this->me->getEmail());

            $this->displayAndCleanup('profile_settings.tpl');
        }
        else {
            if ($_POST['source'] == 'password') {
                $this->profileUpdatePassword();
            }
            elseif ($_POST['source'] == 'info') {
                $this->profileUpdateInfo();
            }
            else {
                $this->application->send('');
            }
        }
    }

    public function profileHistory() {
        $this->template->assign('SITE_TITLE', 'Mine historikk');
        
        $history = $this->me->getKarmaElements();
        $this->template->assign('PROFILE_USER_HISTORY', $history);
        
        $this->displayAndCleanup('profile_history.tpl');
    }

    private function profileUpdatePassword() {
        if (isset($_POST['forgotten-password-new-form-oldpassword'])
            and isset($_POST['forgotten-password-new-form-password1'])
            and isset($_POST['forgotten-password-new-form-password2'])
            and ($_POST['forgotten-password-new-form-password1'] ==
                $_POST['forgotten-password-new-form-password2'])) {
            if (password_verify(
                $_POST['forgotten-password-new-form-oldpassword'],
                Utilities::reverseFuckup($this->me->getPassword())
            )) {
                $hash_salt = Utilities::generateSalt();
                $hash = Utilities::hashPassword($_POST['forgotten-password-new-form-password1'], $hash_salt);

                $this->me->setPassword($hash);

                $this->me->update();

                MessageManager::addMessage($this->application, 'Passordet er endret.', 'success');

                $set_login_cookie = false;
                if (isset($_COOKIE['youkok2'])) {
                    $set_login_cookie = true;
                }

                $this->me->setLogin($hash, $this->me->getEmail(), $set_login_cookie);

                $this->application->send(TemplateHelper::url_for('profile_settings'));
            }
            else {
                $message  = 'Passordet du oppga som ditt gamle passord er ikke korrekt eller de ';
                $message .= 'nye passordene matchet ikke. Prøv igjen.';
                MessageManager::addMessage($this->application, $message, 'danger');

                $this->application->send(TemplateHelper::url_for('profile_settings'));
            }
        }
    }

    private function profileUpdateInfo() {
        if (isset($_POST['register-form-email']) and isset($_POST['register-form-nick'])) {
            // Make sure there is no other users with this email
            $email_processor = null;
            if ($_POST['register-form-email'] != $this->me->getEmail()) {
                $_POST['email'] = $_POST['register-form-email'];
                $_POST['ignore'] = 1;
                
                $email_processor = $this->application->runProcessor('/register/email', [
                    'output' => false,
                    'close_db' => false]);
            }
            
            // Check if we should update e-mail
            if (($email_processor != null and $email_processor['code'] == 200)
                and $_POST['register-form-email'] != $this->me->getEmail()
                and strlen($_POST['register-form-email']) > 0
                and filter_var($_POST['register-form-email'], FILTER_VALIDATE_EMAIL)) {
                $this->me->setEmail($_POST['register-form-email']);
                $this->me->update();

                $set_login_cookie = false;
                if (isset($_COOKIE['youkok2'])) {
                    $set_login_cookie = true;
                }

                $hash_split = explode('asdkashdsajheeeeehehdffhaaaewwaddaaawww', $_SESSION['youkok2']);

                $this->me->setLogin($hash_split[1], $_POST['register-form-email'], $set_login_cookie);
            }

            if (($_POST['register-form-nick'] == '' and $this->me->getNick() != '<em>Anonym</em>') or
                ($_POST['register-form-nick'] != $this->me->getNickReal())) {
                $this->me->setNick($_POST['register-form-nick']);
            }

            $this->me->update();

            MessageManager::addMessage($this->application, 'Dine endringer er lagret.', 'success');

            $this->application->send(TemplateHelper::url_for('profile_settings'));
        }
        else {
            MessageManager::addMessage('Her gikk visst noe galt...', 'danger');

            $this->application->send(TemplateHelper::url_for('profile_settings'));
        }
    }
}
