<?php
namespace Admin;

use \Flight as F;

/**
 * ===============================================
 *  Install Controller
 * ===============================================
 */

class Install extends AdminController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(true);
    }

    /**
     * Install main page
     */
    public function index()
    {
        F::render('Admin/install.html');
    }

    /**
     * Save chosen password
     */
    public function save_pass()
    {
        $pass = F::request()->data->password;
        $pass2 = F::request()->data->password2;

        if ($pass === $pass2) {
            if (!empty($pass)) {
                if (Action::savePassword($pass)) {
                    $_SESSION['flashbag'] = '
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Your password has successfully been set.
                    </div>';
                    $_SESSION['admin'] = 1;
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
        $this->index();
    }
}