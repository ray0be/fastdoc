<?php
namespace Admin;

use \Flight as F;

/**
 * ===============================================
 *  Verif Helper
 * ===============================================
 */

class Verif
{
    /**
     * Check if a given password is the same as the admin password
     */
    public static function okPassword($pass)
    {
        $right = file_get_contents('app/password.txt');
        $pass = hash('sha512', '#fff'.$pass.'lzw:Flight');

        return $right == $pass;
    }

    /**
     * Check if the captcha is OK
     */
    public function okCaptcha($code)
    {
        if (!isset(F::get('config')['recaptcha']['private']) || empty(F::get('config')['recaptcha']['private'])) return false;

        $params = [
            'secret'    => F::get('config')['recaptcha']['private'],
            'response'  => $code,
            'remoteip'  => F::request()->ip
        ];

        $url = "https://www.google.com/recaptcha/api/siteverify?".http_build_query($params);

        if (function_exists('curl_version'))
        {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        } else {
            $response = file_get_contents($url);
        }

        if (empty($response) || is_null($response)) {
            return false;
        }
        
        $json = json_decode($response);
        return $json->success;
    }

    /**
     * Check if config array is valid,
     * in order to save it in json file
     */
    public static function okConfig(array $config)
    {
        foreach ($config as $value)
        {
            if (is_array($value)) {
                foreach ($value as $value2) {
                    if (!is_string($value2) || strpos($value2, '"') !== false) return false;
                }
            }
            else {
                if (strpos($value, '"') !== false) return false;
            }
        }
        return true;
    }

    /**
     * Check if a page title is valid
     */
    public static function titleFilter($name)
    {
        return str_replace('"', '\\"', $name);
    }
}