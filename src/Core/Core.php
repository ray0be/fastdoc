<?php
namespace Core;

use \Flight as F;

/**
 * ===============================================
 *  Core Controller (Public)
 * ===============================================
 */

class Core extends \Controller
{
    /**
     * 404 Not Found Error
     */
    public function error404()
    {
        F::render('Core/404.html');
    }
}