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
        
        /*
         * Tasks
         */
        
        'Tasks\\Build' => array(
            '/tasks/build',
        ),
        'Tasks\\ClearCache' => array(
            '/tasks/clearcache',
         ),
        'Tasks\\Check404' => array(
            '/tasks/check404',
        ),
        'Tasks\\LoadCourses' => array(
            '/tasks/courses',
        ),
        'Tasks\\LoadCoursesJson' => array(
            '/tasks/coursesjson',
        ),
        'Tasks\\LoadExams' => array(
            '/tasks/exams',
        ),
        'Tasks\\FindDuplicates' => array(
            '/tasks/duplicates',
        ),
        'Tasks\\GetCacheData' => array(
            '/tasks/cachedata',
        ),
        
        /*
         * Syncs
         */
        
        'Tasks\\Sync\\SyncEmpty' => array(
            '/tasks/sync/syncempty',
        ),
        
        /*
         * Other stuff
         */
        
        'Favorite' => array(
            '/favorite',
        ),
        'Module' => array (
            '/module/update',
        ),
        'Register' => array(
            '/register/email',
        ),
        'StaticReturner' => array(
            '/search/courses.json',
        ),
        'LoadHistory' => array(
            '/history/get',
        ),
        'LinkTitle' => array(
            '/link/title',
        ),
        
        /*
         * Creates
         */
        
        'CreateFile' => array(
            '/file/create',
        ),
        'CreateLink' => array(
            '/link/create',
        ),
        'CreateFolder' => array(
            '/folder/create',
        ),
    );
    
    /*
     * Array with redirects
     */
    
    private static $redirects = array(
        '/kokeboka/emner*' => '/emner*',
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