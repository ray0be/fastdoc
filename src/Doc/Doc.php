<?php
namespace Doc;

use \Flight as F;
use \app\Files;

/**
 * ===============================================
 *  Doc Controller
 * ===============================================
 */

class Doc extends DocController
{
    /**
     * Reading / Editing page
     */
    public function page($params)
    {
        $mode = $params[0];
        $pageinfos = F::get('pageinfos');

        if (empty($pageinfos['cat']))
        {
            $pageTitle = F::get('config')['document'];
            $url_view = '{$root}';
            $url_edit = '{$root}.edit';
            $path = 'docs/.home';
        }
        else
        {
            $pageTitle = Files::getPageTitle($pageinfos['cat'], $pageinfos['page']);
            $url_view = '{$root}'.$pageinfos['cat'].'/'.$pageinfos['page'];
            $url_edit = '{$root}'.$pageinfos['cat'].'/'.$pageinfos['page'].'.edit';
            $path = 'docs/'.$pageinfos['cat'].'/'.$pageinfos['page'];
        }
        
        if (file_exists($path) && is_file($path) && is_readable($path))
        {
            $pageContent = file_get_contents($path);

            # Edit mode
            if ($mode == 'edit' && F::has('admin'))
            {
                # Content submited
                if (F::request()->method == 'POST')
                {
                    $pageContent = F::request()->data->pageContent;

                    file_put_contents($path, $pageContent);
                    $_SESSION['flashbag'] = '
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        The page has been saved
                    </div>';

                    F::redirect('/'.$pageinfos['cat'].'/'.$pageinfos['page']);
                }
                # Want to edit (not already done)
                else
                {
                    $pageContent = file_get_contents($path);

                    F::render('Doc/page-edit.html', array(
                        'page_edit_url' => $url_edit,
                        'page_view_url' => $url_view,
                        'page_title' => $pageTitle,
                        'page_content' => $pageContent,
                        'edition_mode' => true
                    ));
                }
            }
            # Read mode
            else
            {
                F::render('Doc/page-view.html', array(
                    'page_edit_url' => $url_edit,
                    'page_title' => $pageTitle,
                    'page_content' => $pageContent,
                    'page_last_modif' => date('Y-m-d H:i:s', filemtime($path))
                ));
            }
        }
        else {
            F::render('Core/404.html');
        }
    }
}