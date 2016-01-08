<?php
namespace app;

/**
 * ===============================================
 *  File functions
 * ===============================================
 */

class Files
{
    /**
     * List of categories and pages
     */
    private static $docfiles = array();

    /**
     * Scan the docs/ folder to list the categories and pages.
     */
    public static function scanDocPages()
    {
        $file = 'docs/list.json';
        self::$docfiles = json_decode(file_get_contents($file), true);

        return self::$docfiles;
    }

    /**
     * Get information about a page
     */
    public static function getPageInfos($cat, $page)
    {
        return array(
            'cat' => array(
                "title" => self::getCategoryTitle($cat),
                "url" => self::getCategoryUrl($cat)
            ),
            'page' => array(
                "title" => self::getPageTitle($cat, $page),
                "url" => self::getPageUrl($cat, $page)
            )
        );
    }

    /**
     * Get page title
     */
    public static function getPageTitle($cat, $page)
    {
        if (array_key_exists($cat, self::$docfiles) && array_key_exists($page, self::$docfiles[$cat]['pages'])) {
            return self::$docfiles[$cat]['pages'][$page];
        }
        return '';
    }

    /**
     * Get page url
     */
    public static function getPageUrl($cat, $page)
    {
        if (array_key_exists($cat, self::$docfiles) && array_key_exists($page, self::$docfiles[$cat]['pages'])) {
            return self::getCategoryUrl($cat).'/'.$page;
        }
        return '';
    }

    /**
     * Get category title
     */
    public static function getCategoryTitle($cat)
    {
        if (array_key_exists($cat, self::$docfiles)) {
            return self::$docfiles[$cat]['title'];
        }
        return '';
    }

    /**
     * Get category url
     */
    public static function getCategoryUrl($cat)
    {
        if (array_key_exists($cat, self::$docfiles)) {
            return $cat;
        }
        return '';
    }
}