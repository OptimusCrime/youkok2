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
        'Frontpage' => array(
            array('path' => '/'),
        ),
        
        'Courses' => array(
            array('path' => self::ARCHIVE, 'subpath' => false),
        ),
        
        'Archive' => array(
            array('path' => self::ARCHIVE, 'subpath' => true),
        ),

        'Profile' => array(
            array('path' => '/profil'),
        ),

        'Download' => array(
            array('path' => self::DOWNLOAD),
        ),

        'Flat' => array(
            array('path' => '/om', 'method' => 'displayAbout'),
            array('path' => '/retningslinjer', 'method' => 'displayTerms'),
            array('path' => '/hjelp', 'method' => 'displayHelp'),
            array('path' => '/karma', 'method' => 'displayKarma'),
            
        ),
        
        'StaticFiles' => array(
            array('path' => '/changelog.txt', 'method' => 'returnChangelog'),
            array('path' => '/favicon.ico', 'method' => 'returnFavicon'),
            array('path' => '/favicon.png', 'method' => 'returnFavicon'),
        ),
        
        'NotFound' => array(
            array('path' => '/404'),
        ),

        'Auth' => array(
            array('path' => '/logg-inn', 'method' => 'displayLogIn'),
            array('path' => '/logg-ut', 'method' => 'displayLogOut'),
            array('path' => '/registrer', 'method' => 'displayRegister'),
            array('path' => '/glemt-passord', 'method' => 'displayForgottenPassword'),
            array('path' => '/nytt-passord', 'method' => 'displayForgottenPasswordNew'),
        ),

        'Graybox' => array(
            array('path' => '/graybox'),
        ),

        'Search' => array(
            array('path' => '/sok'),
        ),

        'Admin' => array(
            array('path' => '/admin'),
        ),

        'Redirect' => array(
            array('path' => self::REDIRECT),
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
            array('path' => '/tasks/build'),
        ),
        'Tasks\\Upgrade' => array(
            array('path' => '/tasks/upgrade'),
        ),
        'Tasks\\ClearCache' => array(
            array('path' => '/tasks/clearcache'),
         ),
        'Tasks\\Check404' => array(
            array('path' => '/tasks/check404'),
        ),
        'Tasks\\LoadCourses' => array(
            array('path' => '/tasks/courses'),
        ),
        'Tasks\\LoadCoursesJson' => array(
            array('path' => '/tasks/coursesjson'),
        ),
        'Tasks\\LoadExams' => array(
            array('path' => '/tasks/exams'),
        ),
        'Tasks\\FindDuplicates' => array(
            array('path' => '/tasks/duplicates'),
        ),
        'Tasks\\GetCacheData' => array(
            array('path' => '/tasks/cachedata'),
        ),
        
        /*
         * Syncs
         */
        
        'Tasks\\Sync\\SyncEmpty' => array(
            array('path' => '/tasks/sync/syncempty'),
        ),
        
        /*
         * Other stuff
         */
        
        'Favorite' => array(
            array('path' => '/favorite'),
        ),
        'Module' => array (
            array('path' => '/module/get', 'method' => 'get'),
            array('path' => '/module/update', 'method' => 'update'),
        ),
        'Register' => array(
            array('path' => '/register/email', 'method' => 'checkEmail'),
        ),
        'StaticReturner' => array(
            array('path' => '/search/courses.json'),
        ),
        'LoadHistory' => array(
            array('path' => '/history/get', 'method' => 'getHistory'),
        ),
        'LinkTitle' => array(
            array('path' => '/link/title'),
        ),
        'Admin\\LoadDownloads' => array(
            array('path' => '/admin/loaddownloads'),
        ),
        
        /*
         * Creates
         */
        
        'CreateFile' => array(
            array('path' => '/file/create'),
        ),
        'CreateLink' => array(
            array('path' => '/link/create'),
        ),
        'CreateFolder' => array(
            array('path' => '/folder/create'),
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