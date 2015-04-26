<?php
if(file_exists('vendor/autoload.php')){
	require 'vendor/autoload.php';
} else {
	echo "<h1>Please install via composer.json</h1>";
	echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
	echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
	exit;
}

if (!is_readable('app/core/config.php')) {
	die('No config.php found, configure and rename config.example.php to config.php in app/core.');
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
	define('ENVIRONMENT', 'development');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 */

if (defined('ENVIRONMENT')){

	switch (ENVIRONMENT){
		case 'development':
			error_reporting(E_ALL);
			ini_set("display_errors", 1);
		break;

		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}

}

//initiate config
new \core\config();

//create alias for Router
use \core\router as Router,
    \helpers\url as Url;

// CRON JOB ROUTE
Router::any('cronny/cron' , '\controllers\cron@run');

//Uploader APP
Router::any('uploader' , '\controllers\custom\uploader@index');
Router::any('processupload' , '\controllers\custom\uploader@process');

//define routes
Router::any('welcome', '\controllers\welcome@index');
Router::any('tables', '\controllers\welcome@tables');
Router::any('test' , '\controllers\test@test');
Router::any('shell' , '\controllers\test@shell');
Router::any('' , '\controllers\ebay\item@listing');

// MARGIN ROUTES
Router::any('margins' , '\controllers\margin@index');
Router::any('margins/add' , '\controllers\margin@add');
Router::any('margins/delete' , '\controllers\margin@delete');
Router::any('margins/update' , '\controllers\margin@update');
// ACCOUNT ROUTES
Router::any('ebay/account' , '\controllers\ebay\account@index');
Router::any('ebay/account/add' , '\controllers\ebay\account@add');
Router::any('ebay/account/delete' , '\controllers\ebay\account@delete');
Router::any('ebay/account/update' , '\controllers\ebay\account@update');


// EBAY MOD ROUTES
Router::any('ebay/massiveUserChange' , '\controllers\ebay\item@massiveUserChange');
Router::any('ebay/backtoactive/(:num)' , '\controllers\ebay\item@backToActive');
Router::any('ebay/massivebacktoactive' , '\controllers\ebay\item@massiveBackToActive');
Router::any('ebay/sendtooutofstock/(:num)' , '\controllers\ebay\item@sendToOutOfStock');
Router::any('ebay/massivesendtooutofstock', '\controllers\ebay\item@massiveSendToOutOfStock');
Router::any('ebay/list' , '\controllers\ebay\item@listing');
Router::any('ebay/list/alerts' , '\controllers\ebay\item@alerts');
Router::any('ebay/list/alerts/(:num)' , '\controllers\ebay\item@alerts');
Router::any('ebay/list/(:any)' , '\controllers\ebay\item@listing');
Router::any('ebay/list/(:any)/(:num)' , '\controllers\ebay\item@listing');
Router::any('ebay/item' , '\controllers\ebay\item@index');
Router::any('ebay/item/changeuser' , '\controllers\ebay\item@changeUser');
Router::any('ebay/item/addnew' , '\controllers\ebay\item@addNew');
Router::any('ebay/item/save' , '\controllers\ebay\item@save');
Router::any('ebay/item/margin' , '\controllers\ebay\item@changeItemMargin');
Router::any('ebay/item/quantityChange' , '\controllers\ebay\item@changeItemQuantity');
Router::any('ebay/item/delete' , '\controllers\ebay\item@delete');
Router::any('ebay/item/deleteAll' , '\controllers\ebay\item@deleteAll');
Router::any('ebay/item/makePinned' , '\controllers\ebay\item@pin');
Router::any('ebay/fetch' , '\controllers\ebay\item@fetch');
Router::any('ebay/fetchtoid' , '\controllers\ebay\item@fetchToId');

Router::any('ebay/packages' , '\controllers\ebay\package@listing');
Router::any('ebay/packages/alert' , '\controllers\ebay\package@alert');
Router::any('ebay/packages/alert/(:num)' , '\controllers\ebay\package@alert');
Router::any('ebay/packages/(:num)' , '\controllers\ebay\package@listing');
Router::any('ebay/package/(:num)' , '\controllers\ebay\package@index');
Router::any('ebay/package/save/(:num)' , '\controllers\ebay\package@save');
Router::any('ebay/package/addToPackage/(:num)/(:num)' , '\controllers\ebay\package@addToPackage');
Router::any('ebay/package/removeFromPackage/(:num)/(:num)' , '\controllers\ebay\package@removeFromPackage');
Router::any('ebay/package/removeItem/(:num)/(:num)' , '\controllers\ebay\package@removeItem');
Router::any('ebay/package/addItems/(:num)' , '\controllers\ebay\package@addItems');
Router::any('ebay/package/delete/(:num)' , '\controllers\ebay\package@delete');
Router::any('ebay/package/addnew' , '\controllers\ebay\package@addnew');


Router::any('ebay/revise/(:num)' , '\controllers\ebay\item@updatePrices');
Router::any('ebay/package/revise/(:num)' , '\controllers\ebay\package@updatePrices');


//NOTES MOD ROUTES
Router::any('notes/update/(:num)' , '\controllers\notes\notes@update');
Router::any('notes/add/(:num)' , '\controllers\notes\notes@add');
Router::any('notes/delete/(:num)' , '\controllers\notes\notes@delete');


// USER MOD ROUTES
Router::any('user/register' , '\controllers\user\user@register');
Router::any('user/changeprivs' , '\controllers\user\user@changeprivs');
Router::any('user/change' , '\controllers\user\user@changePass');
Router::any('user/delete' , '\controllers\user\user@delete');
Router::any('user/changeperpage/(:any)' , '\controllers\user\user@changePerPage');
Router::any('user' , '\controllers\user\user@index');
Router::any('login' , '\controllers\user\user@login');
Router::any('logout' , '\controllers\user\user@logout');

Router::any('user/users' , '\controllers\user\user@users');

// SHOW USAGE CUSTOM 
Router::any('ebay/showUsage' , '\controllers\ebay\item@showUsage');


//if no route found
Router::error('\core\error@index');

//turn on old style routing
Router::$fallback = true;

//execute matched routes
Router::dispatch();
