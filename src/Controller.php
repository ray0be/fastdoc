<?php

use \Flight as F;
use app\Files;

/**
 * ===============================================
 *  Global Controller
 * ===============================================
 */

class Controller
{
    /**
     * some processes before real controller execution
     */
    public function __construct($install)
    {
        $this->checkInstall($install);

        $this->checkAdmin();

        $this->scanPages();
    }

    /**
     * scan pages
     */
    private function scanPages()
    {
        $docfiles = Files::scanDocPages();
        F::set('docfiles', $docfiles);
        F::view()->assign(array('docfiles' => $docfiles));
    }

    /**
     * check if user is logged in as admin
     */
    private function checkAdmin()
    {
        $admin = false;
        if (isset($_SESSION['admin'])) {
            F::set('admin', 1);
            $admin = true;
        }
        F::view()->assign(array('admin' => $admin));
    }

    /**
     * check if the install process have been done
     */
    private function checkInstall($install)
    {
        if (F::has('password'))
        {
            if (!$install && empty(F::get('password')))
            {
                F::redirect('/install');
            }
            elseif ($install && !empty(F::get('password')))
            {
                F::redirect('/login');
            }
        }
    }
}