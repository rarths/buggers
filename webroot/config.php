<?php
/**
 * Sample configuration file for Anax webroot.
 *
 */


/**
 * Define essential Anax paths, end with /
 *
 */
define('ANAX_INSTALL_PATH', realpath(__DIR__ . '/../') . '/');
define('ANAX_APP_PATH',     ANAX_INSTALL_PATH . 'app/');



/**
 * Include autoloader.
 *
 */
include(ANAX_APP_PATH . 'config/autoloader.php'); 



/**
 * Include global functions.
 *
 */
include(ANAX_INSTALL_PATH . 'src/functions.php'); 



// Check if enviroment is local
if ( $_SERVER['REMOTE_ADDR'] == '::1' ) {
    define('ANAX_DB_DSN', 'mysql:host=localhost;dbname=roha15');
    define('ANAX_DB_USER', 'root');
    define('ANAX_DB_PASSWORD', 'root');
} else {
    define('ANAX_DB_DSN', 'mysql:host=blu-ray.student.bth.se;dbname=roha15');
    define('ANAX_DB_USER', 'roha15');
    define('ANAX_DB_PASSWORD', 'GiE62nW"');
}