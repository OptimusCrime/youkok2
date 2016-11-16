<?php
namespace Youkok2\Views;

use Youkok2\Models\Me;
use Youkok2\Models\User;
use Youkok2\Models\ChangePassword;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\TemplateHelper;
use Youkok2\Utilities\Utilities;

class Auth extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);

        $this->template->assign('HEADER_MENU', null);
    }
    
    public function displayLogIn() {
        if ($this->me->isLoggedIn()) {
            $this->application->send('');
        }
        else {
            if (isset($_POST['login2-email']) or isset($_POST['login-email'])) {
                if (isset($_POST['login2-email'])) {
                    $_POST['login-email'] = $_POST['login2-email'];
                    $_POST['login-pw'] = $_POST['login2-pw'];
                }

                $this->me->login();
            }
            else {
                $this->template->assign('SITE_TITLE', 'Logg inn');

                if (isset($_SESSION['login_correct_email'])) {
                    $this->template->assign('LOGIN_EMAIL', $_SESSION['login_correct_email']);
                    unset($_SESSION['login_correct_email']);
                }

                $this->displayAndCleanup('login.tpl');
            }
        }
    }
    
    public function displayLogOut() {
        if (!$this->me->isLoggedIn()) {
            $this->application->send('');
        }
        
        $this->me->logOut();
    }

    public function displayRegister() {
        if ($this->me->isLoggedIn()) {
            $this->application->send('');
        }
        
        $this->addSiteData('view', 'register');
        
        $this->template->assign('SITE_TITLE', 'Registrer');
        
        if (!isset($_POST['register-form-email'])) {
            $this->displayAndCleanup('register.tpl');
        }
        else {
            $fields = [
                'register-form-email',
                'register-form-nick',
                'register-form-password1',
                'register-form-password2'
            ];

            $err = false;
            foreach ($fields as $v) {
                if (!isset($_POST[$v]) or ($v != 'register-form-nick' and strlen($_POST[$v]) < 7)) {
                    $err = true;
                    break;
                }
            }

            if (!$err) {
                $_POST['email'] = $_POST['register-form-email'];

                $email_check = $this->runProcessor('register/email', false, true);
                
                if (isset($email_check['code']) and $email_check['code'] == 200 and
                    filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == true) {
                    if ($_POST['register-form-password1'] == $_POST['register-form-password2']) {
                        $hash = Utilities::hashPassword($_POST['register-form-password1'], Utilities::generateSalt());

                        $this->me->create();
                        
                        $this->me->setEmail($_POST['register-form-email']);
                        $this->me->setPassword($hash);
                        $this->me->setNick($_POST['register-form-nick']);

                        $this->me->save();

                        $mail = new \PHPMailer;
                        $mail->From = 'donotreply@' . DOMAIN;
                        $mail->FromName = 'Youkok2';
                        $mail->addAddress($_POST['register-form-email']);
                        $mail->addReplyTo(EMAIL_CONTACT);

                        $mail->WordWrap = 75;
                        $mail->isHTML(false);

                        $mail->Subject = utf8_decode('Velkommen til Youkok2');
                        $mail->Body = utf8_decode(file_get_contents(BASE_PATH . '/files/mail/register.txt'));
                        $mail->send();
                    }
                    else {
                        $err = true;
                    }
                }
                else {
                    $err = true;
                }
            }

            if ($err) {
                MessageManager::addMessage($this->application, 'Her gikk visst noe galt...', 'danger');
               
                $this->application->send(TemplateHelper::urlFor('auth_register'));
            }
            else {
                MessageManager::addMessage($this->application, 'Velkommen til Youkok2!', 'success');

                $this->me->setLogin($hash, $_POST['register-form-email']);
                $this->application->send('');
            }
        }
    }

    public function displayForgottenPassword() {
        if ($this->me->isLoggedIn()) {
            $this->application->send('');
        }
        
        $this->template->assign('SITE_TITLE', 'Glemt passord');
        
        if (isset($_POST['forgotten-email'])) {
            $get_login_user  = "SELECT id" . PHP_EOL;
            $get_login_user .= "FROM user" . PHP_EOL;
            $get_login_user .= "WHERE email = :email";

            $get_login_user_query = Database::$db->prepare($get_login_user);
            $get_login_user_query->execute([':email' => $_POST['forgotten-email']]);
            $row = $get_login_user_query->fetch(\PDO::FETCH_ASSOC);

            if (isset($row['id'])) {
                $hash = Utilities::hashPassword(
                    md5(rand(0, 100000) . md5(time()) . $row['id']),
                    sha1(rand(0, 1000)),
                    false
                );

                $change_password = new ChangePassword();
                $change_password->setUser($row['id']);
                $change_password->setHash($hash);
                $change_password->setTimeout(date('Y-m-d H:i:s', (time() + (60 * 60 * 24))));

                $change_password->save();

                $mail = new \PHPMailer;
                $mail->From = 'donotreply@' . DOMAIN;
                $mail->FromName = 'Youkok2';
                $mail->addAddress($_POST['forgotten-email']);
                $mail->addReplyTo(EMAIL_CONTACT);

                $mail->WordWrap = 75;
                $mail->isHTML(false);

                $mail->Subject = utf8_decode('Glemt passord på Youkok2');
                $message = utf8_decode(file_get_contents(BASE_PATH . '/files//mail/forgotten.txt'));
                $message_keys = [
                    '{{SITE_URL}}' => URL_FULL,
                    '{{PATH}}' => TemplateHelper::urlFor('auth_new_password'),
                    '{{HASH}}' => $hash
                ];
                $mail->Body = str_replace(array_keys($message_keys), $message_keys, $message);
                $mail->send();

                $message  = 'Det er blitt sendt en e-post til deg. Denne inneholder en link for ';
                $message .= 'å velge nytt passord. Denne linken er gyldig i 24 timer.';
                MessageManager::addMessage($this->application, $message, 'success');
            }
            else {
                MessageManager::addMessage(
                    $this->application,
                    'E-posten du oppga ble ikke funnet i systemet. Prøv igjen.',
                    'danger'
                );
            }

            $this->application->send(TemplateHelper::urlFor('auth_forgotten_password'));
        }
        else {
            $this->displayAndCleanup('forgotten_password.tpl');
        }
    }

    public function displayForgottenPasswordNew() {
        if ($this->me->isLoggedIn() or !isset($_GET['hash'])) {
            $this->application->send('');
        }
        
        $this->addSiteData('view', 'forgotten-password');
        
        $this->template->assign('SITE_TITLE', 'Nytt passord');
        
        $change_password = new ChangePassword();
        $change_password->createByHash($_GET['hash']);
        
        if ($change_password->getId() != null) {
            if (!isset($_POST['forgotten-password-new-form-password1'])) {
                $this->displayAndCleanup('forgotten_password_new.tpl');
            }
            else {
                if ($_POST['forgotten-password-new-form-password1'] ==
                    $_POST['forgotten-password-new-form-password2']) {
                    $user = new User($change_password->getUser());
                    
                    $hash_salt = Utilities::generateSalt();
                    $hash = Utilities::hashPassword($_POST['forgotten-password-new-form-password1'], $hash_salt);

                    $user->setPassword($hash);
                    $user->update();

                    $change_password->delete();

                    MessageManager::addMessage($this->application, 'Passordet er endret!', 'success');

                    $this->me->setLogin($hash, $user->getEmail());

                    $this->application->send('');
                }
                else {
                    MessageManager::addMessage($this->application, 'De to passordene er ikke like.', 'danger');

                    $this->application->send(TemplateHelper::urlFor('auth_new_password') . '?hash=' . $_GET['hash']);
                }
            }
        }
        else {
            MessageManager::addMessage($this->application, 'Denne linken er ikke lenger gyldig.', 'danger');

            $this->application->send('');
        }
    }
}
