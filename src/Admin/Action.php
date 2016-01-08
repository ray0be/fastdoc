<?php
namespace Admin;

/**
 * ===============================================
 *  Admin Actions
 * ===============================================
 */

class Action
{
    /**
     * Paswword
     */
    public static function savePassword($new)
    {
        $pass = hash('sha512', '#fff'.$new.'lzw:Flight');

        return file_put_contents('app/password.txt', $pass);
    }

    /**
     * Config (Settings)
     */
    public static function saveConfig(array $config)
    {
        if (!Verif::okConfig($config)) return false;

        $json = json_encode($config, JSON_PRETTY_PRINT);

        return file_put_contents('app/config.json', $json);
    }

    /**
     * Parse category & page title to get a smart url
     */
    public static function cleanUrl($str, $delimiter = '-')
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    /**
     * Save list of categories & pages
     */
    public static function saveDocPages($docfiles)
    {
        $json = json_encode($docfiles, JSON_PRETTY_PRINT);

        return file_put_contents('docs/list.json', $json);
    }
}