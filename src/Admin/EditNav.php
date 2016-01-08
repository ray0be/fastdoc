<?php
namespace Admin;

use \Flight as F;

/**
 * ===============================================
 *  Create categories & pages / manage
 * ===============================================
 */

class EditNav extends AdminController
{
    /**
     * Main page
     */
    public function index()
    {
        F::render('Admin/edit_nav.html');
    }

    /**
     * Save the pages title
     */
    public function save()
    {
        if (isset(F::request()->data->list) && is_array(F::request()->data->list))
        {
            $list = F::request()->data->list;

            $docfiles = F::get('docfiles');
            $newdocs = $docfiles;

            // check all categories to detect changes
            foreach ($docfiles as $category => $categoryArray)
            {
                if (isset($list[$category]['_title']))
                {
                    // check all pages in this category
                    foreach ($categoryArray['pages'] as $page => $pageTitle)
                    {
                        if (isset($list[$category][$page]))
                        {
                            // change the page title
                            if ($list[$category][$page] != $pageTitle)
                            {
                                $newID = $this->getPageId($category, $list[$category][$page]);
                                $newdocs[$category]['pages'][$newID] = $list[$category][$page];
                                if ($newID != $page) {
                                    unset($newdocs[$category]['pages'][$page]);
                                }

                                // rename file
                                if (!rename('docs/'.$category.'/'.$page, 'docs/'.$category.'/'.$newID)) {
                                    die('Error while renaming "'. $category .'" page');
                                }
                            }
                        }
                        else {
                            die('Missing value for "'. $page .'" page.');
                        }
                    }

                    // change the category title
                    if ($list[$category]['_title'] != $categoryArray['title'])
                    {
                        $newID = $this->getCategoryId($list[$category]['_title']);
                        
                        if ($newID != $category) {
                            $newdocs[$newID] = $newdocs[$category];
                            unset($newdocs[$category]);
                        }
                        $newdocs[$newID]['title'] = $list[$category]['_title'];

                        // rename folder
                        if (rename('docs/'.$category.'/', 'docs/'.$newID.'/') === false) {
                            die('Error while renaming "'. $category .'" folder');
                        }
                    }
                }
                else {
                    die('Missing value for "'. $category .'"" category.');
                }
            }

            // sauvegarde des modifications
            if (Action::saveDocPages($newdocs))
            {
                $_SESSION['flashbag'] = '
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    The titles have been updated
                </div>';
            }
            else {
                $_SESSION['flashbag'] = '
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    An error occured
                </div>';
            }
            F::redirect('/');
        }
    }

    /**
     * Create a category.
     */
    public function create_category()
    {
        $category = F::request()->data->categoryTitle;
        $docfiles = F::get('docfiles');

        $clean_category = $this->getCategoryId($category);

        if (mkdir('docs/'.$clean_category, 0777))
        {
            $docfiles[$clean_category] = array(
                'title' => $category,
                'pages' => array()
            );

            if (Action::saveDocPages($docfiles))
            {
                $_SESSION['flashbag'] = '
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    The category has been created
                </div>';
                F::redirect('/');
            }
        }

        $_SESSION['flashbag'] = '
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            An error occured
        </div>';
        F::redirect('/');
    }

    /**
     * Create a page in a category.
     */
    public function create_page()
    {
        $category = F::request()->data->pageCategory;
        $page = F::request()->data->pageTitle;

        $docfiles = F::get('docfiles');

        if (empty($category)) die('Empty Category field.');
        if (empty($page)) die('Empty Page Title field.');

        if (!array_key_exists($category, $docfiles))
        {
            $_SESSION['flashbag'] = '
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                Please choose a category
            </div>';
        }
        else
        {
            $clean_page = $this->getPageId($category, $page);

            if (file_put_contents('docs/'. $category .'/'. $clean_page, "<p>Empty page.</p>"))
            {
                $docfiles[$category]['pages'][$clean_page] = $page;

                if (Action::saveDocPages($docfiles))
                {
                    $_SESSION['flashbag'] = '
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        The page has successfully been created
                    </div>';
                }
                else {
                    $_SESSION['flashbag'] = '
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Unable to save the doc structure. Please try again and verify that <strong>docs/list.json</strong> is writable.
                    </div>';
                }
            }
            else {
                $_SESSION['flashbag'] = '
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Unable to save the page. Please try again.
                </div>';
            }
        }

        F::redirect('/');
    }

    /**
     * Remove a category
     */
    public function remove_category($params)
    {
        $cat = $params[0];
        $docfiles = F::get('docfiles');

        if (array_key_exists($cat, F::get('docfiles')))
        {
            if (count($docfiles[$cat]['pages']) == 0)
            {
                unset($docfiles[$cat]);

                if (Action::saveDocPages($docfiles) && rmdir('docs/'.$cat))
                {
                    $_SESSION['flashbag'] = '
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        The category has been removed
                    </div>';
                    F::redirect('/edit-nav');
                }
                else {
                    $_SESSION['flashbag'] = '
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        An error occured while removing category
                    </div>';
                }
            }
            else {
                die('This category contains pages, you can\'t actually remove it');
            }
        }
        else {
            die('Unkown category');
        }
    }

    /**
     * Remove a page
     */
    public function remove_page($params)
    {
        $cat = $params[0];
        $page = $params[1];
        $docfiles = F::get('docfiles');

        if (array_key_exists($cat, $docfiles))
        {
            if (array_key_exists($page, $docfiles[$cat]['pages']))
            {
                unset($docfiles[$cat]['pages'][$page]);

                if (Action::saveDocPages($docfiles) && unlink('docs/'.$cat.'/'.$page))
                {
                    $_SESSION['flashbag'] = '
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        This page has been removed
                    </div>';
                    F::redirect('/edit-nav');
                }
                else {
                    $_SESSION['flashbag'] = '
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        An error occured while removing page
                    </div>';
                }
            }
            else {
                die('Unknown page');
            }
        }
        else {
            die('Unknown category');
        }
    }

    /**
     * Return the new page & category ID for a title
     */
    public function getCategoryId($title)
    {
        return $this->getId(F::get('docfiles'), $title);
    }
    public function getPageId($cat, $title)
    {
        return $this->getId(F::get('docfiles')[$cat]['pages'], $title);
    }
    public function getId($arrayToCheck, $title)
    {
        $clean_thing_base = Action::cleanUrl($title);
        $clean_thing = $clean_thing_base;

        if (empty($clean_thing)) die('Empty Category/Page Title field.');

        $i = 2;
        while (array_key_exists($clean_thing, $arrayToCheck))
        {
            $clean_thing = $clean_thing_base .'-'. $i;
            $i++;
        }

        return $clean_thing;
    }
}