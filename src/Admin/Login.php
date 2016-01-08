<?php
namespace Admin;

use \Flight as F;

/**
 * ===============================================
 *  Login Controller
 * ===============================================
 */

class Login extends AdminController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(false, true);
    }

    /**
     * Login page
     */
    public function index()
    {
        if (isset($_SESSION['admin'])) {
            F::redirect('/');
        }

        F::render('Admin/login.html');
    }

    /**
     * Login POST verification (authentication)
     */
    public function access()
    {
        $pass = F::request()->data->password;

        # captcha
        if (!empty(F::get('config')['recaptcha']['public'])) {
            $captcha = F::request()->data['g-recaptcha-response'];

            if (!Verif::okCaptcha($captcha)) {
                $_SESSION['flashbag'] = '<div class="alert alert-danger">Wrong security captcha.</div>';
                $this->index();
                exit;
            }
        }

        # password
        if (Verif::okPassword($pass)) {
            $_SESSION['admin'] = 1;
            $_SESSION['flashbag'] = '
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                You are now logged in.
            </div>';
            F::redirect('/');
        }
        else {
            $_SESSION['flashbag'] = '<div class="alert alert-danger">Wrong password.</div>';
        }
        $this->index();
    }

    /**
     * Logout process
     */
    public function logout()
    {
        if (isset($_SESSION['admin'])) {
            $_SESSION['admin'] = null;
            $_SESSION['flashbag'] = '
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                You are no longer logged in.
            </div>';
        }
        F::redirect('/');
    }
}