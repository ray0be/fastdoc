<?php
namespace Admin;

use \Flight as F;

/**
 * ===============================================
 *  Settings Controller
 * ===============================================
 */

class Settings extends AdminController
{
    /**
     * Settings edition page
     */
    public function index()
    {
        F::render('Admin/settings.html');
    }

    /**
     * Settings POST validation
     */
    public function save()
    {
        $public_key = isset(F::request()->data->recaptcha) ? F::request()->data->public_key : '';
        $private_key = isset(F::request()->data->recaptcha) ? F::request()->data->private_key : '';
        $tracker_code = isset(F::request()->data->analytics) ? F::request()->data->tracker_code : '';

        $config = array(
            'document' => F::request()->data->document,
            'author' => [
                'name' => F::request()->data->name,
                'url' => F::request()->data->url
            ],
            'page' => [
                'lang' => F::request()->data->lang,
                'charset' => F::request()->data->charset,
                'title' => F::request()->data->title,
                'description' => F::request()->data->description,
                'keywords' => F::request()->data->keywords,
                'maincolor' => F::request()->data->maincolor
            ],
            'recaptcha' => [
                'public' => $public_key,
                'private' => $private_key
            ],
            'analytics' => $tracker_code
        );

        if (Action::saveConfig($config))
        {
            $_SESSION['flashbag'] = '
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                Your settings have been saved.
            </div>';
            F::redirect('/settings');
        }
        else {
            $_SESSION['flashbag'] = '<div class="alert alert-danger">There is an error with the content of your settings. Note that the quotation mark (") is forbidden in all fields.</div>';
            $this->index();
        }
    }

    /**
     * Password edit page
     */
    public function password()
    {
        F::render('Admin/password.html');
    }

    /**
     * Password validation process
     */
    public function save_pass()
    {
        $actual_password = F::request()->data->apassword;
        $pass = F::request()->data->password;
        $pass2 = F::request()->data->password2;

        if (Verif::okPassword($actual_password))
        {
            if ($pass === $pass2)
            {
                if (!empty($pass))
                {
                    if (Action::savePassword($pass))
                    {
                        $_SESSION['flashbag'] = '
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Your new password has been saved.
                        </div>';
                        F::redirect('/settings');
                        exit;
                    }
                    else {
                        $_SESSION['flashbag'] = '<div class="alert alert-danger">An error occured. Please verify that the app/ and src/ folder are writable.</div>';
                    }
                }
                else {
                    $_SESSION['flashbag'] = '<div class="alert alert-warning">No password ? Are you serious ? Put at least some letters.</div>';
                }
            }
            else {
                $_SESSION['flashbag'] = '<div class="alert alert-danger">You must enter the same password twice.</div>';
            }
        }
        else {
            $_SESSION['flashbag'] = '<div class="alert alert-danger">Your actual password is wrong.</div>';
        }
        $this->password();
    }
}