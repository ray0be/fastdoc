<?php

/**
 * ===============================================
 *  autoload
 * ===============================================
 */

spl_autoload_register('autoloadClass');

function autoloadClass($class)
{
    if (class_exists($class)) return;
    
    $path = str_replace('\\', '/', $class);

    if( file_exists($path.'.php') ) {
        require_once $path.'.php';
    }
    elseif (file_exists('app/'.$path.'.php')) {
        require_once 'app/'.$path.'.php';
    }
    elseif (file_exists('src/'.$path.'.php')) {
        require_once 'src/'.$path.'.php';
    }
}

/**
 * ===============================================
 *  readable & writable files verification
 * ===============================================
 */

function checkRights()
{
    $error = false;
    $error_text = '<h1>Configuration Errors</h1>';

    # check the dependencies folder
    $vendor = '';
    if (!file_exists('vendor/')) {
        $vendor .= '<p style="color:red;"><strong>Warning!</strong> The "<u>vendor/</u>" folder is missing! Don\'t forget to run the " <strong>composer update</strong> " command.</p>';
        $error = true;
    }
    elseif (!is_readable('vendor/autoload.php')) {
        $vendor .= '<p>Please make the "<u>vendor/</u>" folder READABLE (and "<u>vendor/autoload.php</u>"). (chown / chmod)</p>';
        $error = true;
    }
    if (!empty($vendor)) {
        $error_text .= '<h2>&lt; Dependencies &gt;</h2>'.$vendor;
    }

    # check project files/folders
    $needToRead = array(
        'app/config.json',
        'app/password.txt',
        'app/edit.txt',
        'docs/',
        'docs/list.json',
        'src/_smarty/'
    );

    $list = '';
    foreach ($needToRead as $file)
    {
        if (!file_exists($file) || !is_readable($file) || !is_writable($file)) {
            $list .= '<li>Please check that "<u>'. $file .'</u>" exists and that we have the right to READ and WRITE it. (chown / chmod)</li>';
            $error = true;
        }
    }
    if (!empty($list)) {
        $error_text .= '<h2>&lt; File Right Error &gt;</h2><ul>'. $list .'</ul>';
    }

    # echo error and exit
    if ($error)
    {
        http_response_code(500);
        if (env == 'dev') {
            echo $error_text;
            exit();
        }
        else {
            echo '<h1>Internal Server Error</h1><p>If you are the administrator, change the value of " <strong>env</strong> " var to " <em>dev</em> " in index.php (to see details).</p>';
            exit();
        }
    }
}

/**
 * ===============================================
 *  debug
 * ===============================================
 */

function debug($var)
{
    # serialize()
    $serial = serialize($var);

    # print_r()
    $print_r = print_r($var, true);

    # var_dump()
    ob_start();
    var_dump($var);
    $var_dump = ob_get_clean();
    ob_end_clean();

    # var_export()
    $var_export = var_export($var, true);

    \Flight::view()->assign(array(
        'serial' => $serial,
        'print_r' => $print_r,
        'var_dump' => $var_dump,
        'var_export' => $var_export
    ));
    \Flight::render('debug.html');
    exit;
}