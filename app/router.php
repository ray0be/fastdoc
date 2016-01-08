<?php
namespace app;

use \Flight as F;

/**
 * ===============================================
 *  Admin
 * ===============================================
 */

// ########################################### \\
// ## ## ## ## // == install == \\ ## ## ## ## \\

F::route('GET /install', function() {
    Fastdoc::run('Admin\Install');
});

F::route('POST /install', function() {
    Fastdoc::run('Admin\Install', 'save_pass');
});

// ######################################### \\
// ## ## ## ## // == login == \\ ## ## ## ## \\

F::route('GET /login', function() {
    Fastdoc::run('Admin\Login');
});

F::route('POST /login', function() {
    Fastdoc::run('Admin\Login', 'access');
});

// ########################################## \\
// ## ## ## ## // == logout == \\ ## ## ## ## \\

F::route('GET /logout', function() {
    Fastdoc::run('Admin\Login', 'logout');
});

// ############################################ \\
// ## ## ## ## // == settings == \\ ## ## ## ## \\

F::route('GET /settings', function() {
    Fastdoc::run('Admin\Settings');
});

F::route('POST /settings', function() {
    Fastdoc::run('Admin\Settings', 'save');
});

F::route('GET /password', function() {
    Fastdoc::run('Admin\Settings', 'password');
});

F::route('POST /password', function() {
    Fastdoc::run('Admin\Settings', 'save_pass');
});

/**
 * ===============================================
 *  Edition
 * ===============================================
 */

// ###################################################### \\
// ## ## ## ## // == document structure == \\ ## ## ## ## \\

F::route('GET /edit-nav',function() {
    Fastdoc::run('Admin\EditNav');
});

F::route('POST /edit-nav',function() {
    Fastdoc::run('Admin\EditNav', 'save');
});

// ############################################ \\
// ## ## ## ## // == category == \\ ## ## ## ## \\

F::route('POST /create/category',function() {
    Fastdoc::run('Admin\EditNav', 'create_category');
});

F::route('GET /edit-nav/remove-category/@cat:[A-Za-z0-9_-]+', function($cat)
{
    Fastdoc::run('Admin\EditNav', 'remove_category', array($cat));
});

// ######################################## \\
// ## ## ## ## // == page == \\ ## ## ## ## \\

F::route('POST /create/page',function() {
    Fastdoc::run('Admin\EditNav', 'create_page');
});

F::route('GET /edit-nav/remove-page/@cat:[A-Za-z0-9_-]+/@page:[A-Za-z0-9_-]+', function($cat, $page)
{
    Fastdoc::run('Admin\EditNav', 'remove_page', array($cat, $page));
});

/**
 * ===============================================
 *  Page view & Page edit
 * ===============================================
 */

F::route('/@cat:[A-Za-z0-9_-]*(/@page:[A-Za-z0-9_-]+)(\.@mode:edit)', function($cat, $page, $mode)
{
    Fastdoc::savePageInfos(array(
        'cat' => $cat,
        'page' => $page
    ));

    $mode = empty($mode) ? 'view' : 'edit';

    Fastdoc::run('Doc\Doc', 'page', array($mode));
});


/**
 * ===============================================
 *  Public
 * ===============================================
 */

// ####################################### \\
// ## ## ## ## // == 404 == \\ ## ## ## ## \\

F::map('notFound', function() {
    Fastdoc::run('Core\Core', 'error404');
});