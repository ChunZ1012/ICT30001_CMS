<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Login');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->group('/', function($routes) {
    $routes->get('', 'Pages\PageController::home', [ 'as' => 'home_page' ]);
    // Redirect to Pages\PageController::home
    $routes->addRedirect('home', 'home_page');
    // $routes->get('login', 'Pages\PageController::login');
    // Directly return the desired page to the browser [Treated as GET]
    $routes->get('login', 'Pages\PageController::login');
    $routes->get('menu', 'Pages\PageController::menu');
    $routes->get('register', 'Pages\PageController::register');
    // Content
    $routes->group('content', function($routes){
        $routes->get('', 'Pages\PostController::list');
        $routes->get('list', 'Pages\PostController::list');
        $routes->get('add', 'Pages\PostController::add');
        $routes->get('edit/(:num)', 'Pages\PostController::edit/$1');
        $routes->get('view/(:num)', 'Pages\PostController::view/$1');
    });
    // Publish
    $routes->group('publish', function($routes){
        $routes->get('', 'Pages\PublicationController::list');
        $routes->get('list', 'Pages\PublicationController::list');
        $routes->get('add', 'Pages\PublicationController::add');
        $routes->get('edit/(:num)', 'Pages\PublicationController::edit/$1');
        $routes->get('view/(:num)', 'Pages\PublicationController::publish_view/$1');
        // Publication Category
        $routes->group('category', function($routes){
            $routes->get('', 'Pages\PublicationCategoryController::list');
            $routes->get('list', 'Pages\PublicationCategoryController::list');
            $routes->get('add', 'Pages\PublicationCategoryController::add');
            $routes->get('edit/(:num)', 'Pages\PublicationCategoryController::edit/$1');
        });
    });
    // Staff
    $routes->group('staff', function($routes){
        $routes->get('', 'Pages\StaffController::list');
        $routes->get('list', 'Pages\StaffController::list');
        $routes->get('add', 'Pages\StaffController::add');
        $routes->get('edit/(:num)', 'Pages\StaffController::edit/$1');
    });
    // User
    $routes->group('user', function($routes){
        $routes->get('', 'Pages\UserController::list');
        $routes->get('list', 'Pages\UserController::list');
        $routes->get('add', 'Pages\UserController::add');
        $routes->get('edit/(:num)', 'Pages\UserController::edit/$1');
    });
});
// API
$routes->group('api', function($routes) {
    $routes->group('auth', function($routes){
        $routes->post('login', 'API\AuthController::login');
        $routes->post('logout', 'API\AuthController::logout');
        $routes->post('register', 'API\AuthController::register');
        $routes->post('auth', 'API\AuthController::auth');
    });
    // $routes->post('reset', 'API\AuthController::reset');
    $routes->get('content/', 'API\PostAPIController::list');
    $routes->get('content/list', 'API\PostAPIController::list');
    $routes->get('content/(:num)', 'API\PostAPIController::get/$1');
    // Content 
    $routes->group('content', ['filter' => 'authFilter'], function($routes){
        $routes->post('add', 'API\PostAPIController::update/-1');
        $routes->post('edit/(:num)', 'API\PostAPIController::update/$1');
        $routes->put('activate/(:num)', 'API\PostAPIController::set_post_status/$1/1');
        $routes->put('deactivate/(:num)', 'API\PostAPIController::set_post_status/$1/0');
        $routes->delete('delete/(:num)', 'API\PostAPIController::delete/$1');
    });

    // Publication listing
    $routes->get('publish', 'API\PublicationAPIController::list', [ 'as' => 'publish_list' ]);
    // Redirect to publish
    $routes->addRedirect('publish/list', 'publish_list');
    $routes->get('publish/(:num)', 'API\PublicationAPIController::get/$1');
    // Publication Category Listing
    $routes->get('publish/category', 'API\PublicationCategoryAPIController::list', [ 'as' => 'publish_category_list' ]);
    // Redirect to 'Publication Category Listing'
    $routes->addRedirect('publish/category/list', 'publish_category_list');
    $routes->get('publish/category/(:num)', 'API\PublicationCategoryAPIController::get/$1');
    // Publication
    $routes->group('publish', ['filter' => 'authFilter'],  function($routes){
        $routes->post('add', 'API\PublicationAPIController::upload/-1');
        $routes->post('edit/(:num)', 'API\PublicationAPIController::upload/$1');
        $routes->put('activate/(:num)', 'API\PublicationAPIController::set_pub_status/$1/1');
        $routes->put('deactivate/(:num)', 'API\PublicationAPIController::set_pub_status/$1/0');
        $routes->delete('delete/(:num)', 'API\PublicationAPIController::delete/$1');
        // Publication Category
        $routes->group('category', function($routes){
            $routes->post('add', 'API\PublicationCategoryAPIController::add/');
            $routes->post('edit/(:num)', 'API\PublicationCategoryAPIController::edit/$1');
            $routes->put('activate/(:num)', 'API\PublicationCategoryAPIController::set_cate_status/$1/1');
            $routes->put('deactivate/(:num)', 'API\PublicationCategoryAPIController::set_cate_status/$1/0');
        });
    });

    // Staff
    $routes->get('staff/', 'API\StaffAPIController::list', [ 'as' => 'staff_list' ]);
    $routes->addRedirect('staff/list', 'staff_list');
    $routes->get('staff/(:num)', 'API\StaffAPIController::get/$1');
    $routes->group('staff', ['filter' => 'authFilter'], function($routes){
        $routes->post('add', 'API\StaffAPIController::update/-1');
        $routes->post('edit/(:num)', 'API\StaffAPIController::update/$1');
        $routes->delete('delete/(:num)', 'API\StaffAPIController::delete/$1');
    });

    // User 
    $routes->get('user/', 'API\UserAPIController::list', [ 'as' => 'user_list' ]);
    $routes->addRedirect('user/list', 'user_list');
    $routes->get('user/(:num)', 'API\UserAPIController::get/$1');
    $routes->group('user', ['filter' => 'authFilter'], function($routes){
        $routes->post('add', 'API\UserAPIController::update/-1');
        $routes->put('edit/(:num)', 'API\UserAPIController::update/$1');
        $routes->delete('delete/(:num)', 'API\UserAPIController::delete/$1');
        $routes->put('reset-password/(:num)', 'API\UserAPIController::reset_password/$1');
    });

});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
