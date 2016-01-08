<?php
namespace Doc;

use \Flight as F;
use app\Files;

/**
 * ===============================================
 *  Global Doc Controller
 * ===============================================
 */

class DocController extends \Controller
{
    /**
     * Constructor
     */
    public function __construct($install)
    {
        parent::__construct($install);

        if (F::has('pageinfos'))
        {
            F::view()->assign(array(
                'pageinfos' => Files::getPageInfos(F::get('pageinfos')['cat'], F::get('pageinfos')['page'])
            ));
        }
    }
}