<?php
/*
 * File: Routes.php
 * Holds: Holds all the routes the Loader matches urls against
 * Created: 02.11.2014
 * Project: Youkok2
 */

namespace Youkok2\Utilities;

/*
 * Static class Routes. Holds and returnes the routes for the application
 */

class Routes {
    
    /*
     * Array with routes
     */
     
    const ARCHIVE = '/emner';
    const DOWNLOAD = '/last-ned';
    const REDIRECT = '/redirect';
    const PROSECESSOR = '/processor';
    
    private static $routes = array(
        'Home' => array(
            '/',
        ),

        'Archive' => array(
            self::ARCHIVE,
        ),

        'Profile' => array(
            '/profil',
        ),

        'Download' => array(
            self::DOWNLOAD,
        ),

        'Flat' => array(
            '/om',
            '/retningslinjer',
            '/privacy',
            '/hjelp',
            '/karma',
            '/changelog.txt',
        ),

        'NotFound' => array(
            '/404',
        ),

        'Auth' => array(
            '/logg-inn',
            '/logg-ut',
            '/registrer',
            '/glemt-passord',
            '/nytt-passord',
            '/verifiser',
        ),

        'Graybox' => array(
            '/graybox',
        ),

        'Search' => array(
            '/sok',
        ),

        'Admin' => array(
            '/admin',
        ),

        'Redirect' => array(
            self::REDIRECT,
        ),
    );
    
    /*
     * Array with processors
     */
    
    private static $processors = array(
        'User' => array(
            '/user/save',
        ),
    );
    
    /*
     * Array with redirects
     */
    
    private static $redirects = array(
        '/kokeboka*' => '/emner*',
    );
    
    /*
     * Return the internal variables
     */
    
    public static function getRoutes() {
        return self::$routes;
    }
    public static function getProcessors() {
        return self::$processors;
    }
    public static function getRedirects() {
        return self::$redirects;
    }
}