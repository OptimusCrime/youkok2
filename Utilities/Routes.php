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
    
    private static $routes = array(
        'Home' => array(
            '/',
        ),

        'Archive' => array(
            '/kokeboka',
        ),

        'Profile' => array(
            '/profil',
        ),

        'Download' => array(
            '/last-ned',
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

        'Processor' => array(
            '/processor',
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
            '/redirect',
        ),
    );
    
    /*
     * Static method returning the routes
     */
    
    public static function getRoutes() {
        return self::$routes;
    }
}