<?php
namespace Admin;

use \Flight as F;

/**
 * ===============================================
 *  Admin Global Controller
 * ===============================================
 */

class AdminController extends \Controller
{
    /**
     * Check if the user is authorized
     */
    public function __construct($install, $login = false)
    {
        if (!isset($_SESSION['admin']) && !$install && !$login) {
            F::redirect('/login');
        }

        parent::__construct($install);
    }
}