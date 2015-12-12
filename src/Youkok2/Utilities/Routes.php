<?php
/*
 * File: Routes.php
 * Holds: Holds all the routes the Loader matches urls against
 * Created: 02.11.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

class Routes {
    
    /*
     * Array with routes
     */
     
    const ARCHIVE = '/emner';
    const DOWNLOAD = '/last-ned';
    const REDIRECT = '/redirect';
    const PROCESSOR = '/processor';
    
    private static $routes = [
        'Frontpage' => [
            ['path' => '/', 'identifier' => 'frontpage'],
        ],
        
        'Courses' => [
            ['path' => self::ARCHIVE, 'subpath' => false, 'identifier' => 'courses'],
        ],
        
        'Archive' => [
            ['path' => self::ARCHIVE, 'subpath' => true, 'identifier' => 'archive'],
        ],

        'Profile' => [
            ['path' => '/profil'],
        ],

        'Download' => [
            ['path' => self::DOWNLOAD],
        ],

        'Flat' => [
            ['path' => '/om', 'method' => 'displayAbout', 'identifier' => 'flat_about'],
            ['path' => '/retningslinjer', 'method' => 'displayTerms', 'identifier' => 'flat_terms'],
            ['path' => '/hjelp', 'method' => 'displayHelp', 'identifier' => 'flat_help'],
        ],
        
        'StaticFiles' => [
            ['path' => '/changelog.txt', 'method' => 'returnChangelog', 'identifier' => 'changelog'],
            ['path' => '/favicon.ico', 'method' => 'returnFavicon'],
            ['path' => '/favicon.png', 'method' => 'returnFavicon'],
        ],
        
        'NotFound' => [
            ['path' => '/404'],
        ],

        'Auth' => [
            ['path' => '/logg-inn', 'method' => 'displayLogIn', 'identifier' => 'auth_login'],
            ['path' => '/logg-ut', 'method' => 'displayLogOut', 'identifier' => 'auth_logout'],
            ['path' => '/registrer', 'method' => 'displayRegister', 'identifier' => 'auth_register'],
            ['path' => '/glemt-passord', 'method' => 'displayForgottenPassword', 'identifier' => 'auth_forgotten_password'],
            ['path' => '/nytt-passord', 'method' => 'displayForgottenPasswordNew', 'identifier' => 'auth_new_password'],
        ],

        'Search' => [
            ['path' => '/sok', 'identifier' => 'search'],
        ],

        'Admin' => [
            ['path' => '/admin', 'identifier' => 'admin'],
        ],

        'Redirect' => [
            ['path' => self::REDIRECT, 'identifier' => 'redirect'],
        ],
    ];
    
    /*
     * Array with processors
     */
    
    private static $processors = [

        /*
         * Normal processors
         */

        'Favorites' => [
            ['path' => '/favorite'],
        ],
        'Module' => [
            ['path' => '/module/get', 'method' => 'get'],
            ['path' => '/module/update', 'method' => 'update'],
        ],
        'Register' => [
            ['path' => '/register/email', 'method' => 'checkEmail'],
        ],
        'StaticReturner' => [
            ['path' => '/search/courses.json'],
        ],
        'LoadHistory' => [
            ['path' => '/history/get'],
        ],
        'LinkTitle' => [
            ['path' => '/link/title'],
        ],
        'Admin\\LoadDownloads' => [
            ['path' => '/admin/loaddownloads'],
        ],
        'Graybox' => [
            ['path' => 'graybox/commits', 'method' => 'getCommits'],
            ['path' => 'graybox/newest', 'method' => 'getNewest'],
            ['path' => 'graybox/popular', 'method' => 'getPopular'],
        ],

        /*
         * Creates
         */

        'CreateFile' => [
            ['path' => '/file/create'],
        ],
        'CreateLink' => [
            ['path' => '/link/create'],
        ],
        'CreateFolder' => [
            ['path' => '/folder/create'],
        ],

        /*
         * Tasks
         */

        'Tasks\\Upgrade' => [
            ['path' => '/tasks/upgrade'],
        ],
        'Tasks\\ClearCache' => [
            ['path' => '/tasks/clearcache'],
        ],
        'Tasks\\LoadCourses' => [
            ['path' => '/tasks/courses'],
        ],
        'Tasks\\LoadCoursesJson' => [
            ['path' => '/tasks/coursesjson'],
        ],
        'Tasks\\LoadExams' => [
            ['path' => '/tasks/exams'],
        ],
        'Tasks\\FindDuplicates' => [
            ['path' => '/tasks/duplicates'],
        ],
        'Tasks\\GetCacheData' => [
            ['path' => '/tasks/cachedata'],
        ],
        
        /*
         * Syncs
         */
        
        'Tasks\\Sync\\SyncEmpty' => [
            ['path' => '/tasks/sync/syncempty'],
        ],
        'Tasks\\Sync\\SyncKarma' => [
            ['path' => '/tasks/sync/synckarma'],
        ],
    ];
    
    /*
     * Array with redirects
     */
    
    private static $redirects = [
        '/kokeboka/emner*' => '/emner*',
        '/kokeboka*' => '/emner*',
    ];
    
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