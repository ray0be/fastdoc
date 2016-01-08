<?php
namespace app;

/**
 * ===============================================
 *  use Flight PHP Engine
 * ===============================================
 */

use \Flight as F;

/**
 * ===============================================
 *  Fastdoc main class
 * ===============================================
 */

class Fastdoc
{
    /**
     * Starts the engine
     */
    public static function start()
    {
        session_start();
        
        # components
        require 'vendor/autoload.php';

        self::init();
        self::setup();

        # routes
        require 'app/router.php';

        F::start();
    }

    /**
     * Init with required vars
     */
    private function init()
    {
        # root path
        $root = str_replace('index.php', '', $_SERVER['PHP_SELF']);

        # config
        $config = json_decode(file_get_contents('app/config.json'), true);

        # admin password
        $password = file_get_contents('app/password.txt');

        # edition
        $edit = (file_get_contents('app/edit.txt') == 'true') ? true : false;

        F::clear();
        F::set('root', $root);
        F::set('config', $config);
        F::set('password', $password);
        F::set('edit', $edit);
    }

    /**
     * Set up Flight Engine (PHP micro-framework, http://flightphp.com)
     */
    private function setup()
    {
        # base url
        F::set('flight.base_url', F::get('root'));

        # Smarty init
        F::register('view', 'Smarty', array(), function($smarty)
        {
            if (env == 'dev') {
                $smarty->caching = 0;
                $smarty->force_compile = true;
            }

            $smarty->setTemplateDir('src/');
            $smarty->setCompileDir('src/_smarty/templates_c/');
            $smarty->setConfigDir('src/_smarty/config/');
            $smarty->setCacheDir('src/_smarty/cache/');
            $smarty->addPluginsDir('src/_smarty/plugins/');
        });

        # render templates
        F::map('render', function($template, $data = array())
        {
            F::view()->assign(array(
                'root' => F::get('root'),
                'app' => array(
                    "name" => "fastdoc",
                    "version" => "1.0",
                    "homepage" => "http://github.com/ray0be/fastdoc"
                ),
                'config' => F::get('config'),
                'edit' => F::get('edit'),
                'data' => $data
            ));

            # flashbags messages
            if (isset($_SESSION['flashbag'])) {
                F::view()->assign(array('flashbag' => $_SESSION['flashbag']));
                $_SESSION['flashbag'] = null;
            }

            F::view()->display($template);
        });
    }

    /**
     * Save information about the page
     */
    public static function savePageInfos(array $params = array())
    {
        F::set('pageinfos', $params);
    }

    /**
     * run a controller
     */
    public static function run($controller, $method = 'index', $params = array())
    {
        $controller = '\\'.$controller;
        $obj = new $controller(false);
        $obj->$method($params);
    }
}