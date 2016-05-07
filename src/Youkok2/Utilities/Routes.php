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
    
    private static $routes = [
        'Views\Frontpage' => [
            ['path' => '/', 'identifier' => 'frontpage'],
        ],
        
        'Views\Courses' => [
            ['path' => '/emner', 'identifier' => 'courses'],
        ],
        
        'Views\Archive' => [
            ['path' => '/emner/+', 'identifier' => 'archive', 'construct' => '+/', 'endfix' => true],
        ],

        'Views\Profile' => [
            ['path' => '/profil', 'identifier' => 'profile_home', 'method' => 'profileRedirect'],
            ['path' => '/profil/innstillinger', 'identifier' => 'profile_settings', 'method' => 'profileSettings'],
            ['path' => '/profil/historikk', 'identifier' => 'profile_history', 'method' => 'profileHistory'],
        ],

        'Views\Download' => [
            ['path' => '/last-ned/+', 'identifier' => 'download', 'construct' => '+/', 'endfix' => false],
        ],

        'Views\Flat' => [
            ['path' => '/om', 'method' => 'displayAbout', 'identifier' => 'flat_about'],
            ['path' => '/retningslinjer', 'method' => 'displayTerms', 'identifier' => 'flat_terms'],
            ['path' => '/hjelp', 'method' => 'displayHelp', 'identifier' => 'flat_help'],
        ],
        
        'Views\StaticFiles' => [
            ['path' => '/changelog.txt', 'method' => 'returnChangelog', 'identifier' => 'changelog'],
            ['path' => '/favicon.ico', 'method' => 'returnFavicon'],
            ['path' => '/favicon.png', 'method' => 'returnFavicon'],
        ],
        
        'Views\NotFound' => [
            ['path' => '/404'],
        ],

        'Views\Auth' => [
            ['path' => '/logg-inn', 'method' => 'displayLogIn', 'identifier' => 'auth_login'],
            ['path' => '/logg-ut', 'method' => 'displayLogOut', 'identifier' => 'auth_logout'],
            ['path' => '/registrer', 'method' => 'displayRegister', 'identifier' => 'auth_register'],
            ['path' => '/glemt-passord', 'method' => 'displayForgottenPassword', 'identifier' => 'auth_forgotten_password'],
            ['path' => '/nytt-passord', 'method' => 'displayForgottenPasswordNew', 'identifier' => 'auth_new_password'],
        ],

        'Views\Search' => [
            ['path' => '/sok', 'identifier' => 'search'],
        ],

        'Views\Redirect' => [
            ['path' => '/redirect/+', 'identifier' => 'redirect', 'construct' => '+/', 'endfix' => false],
        ],
        
        /*
         * Admin views
         */
        
        'Views\Admin\Home' => [
            ['path' => '/admin', 'method' => 'displayAdminHome', 'identifier' => 'admin_home'],
        ],
        'Views\Admin\Contribution' => [
            ['path' => '/admin/bidrag', 'method' => 'displayAdminContributions', 'identifier' => 'admin_contribution'],
        ],
        'Views\Admin\Files' => [
            ['path' => '/admin/filer', 'method' => 'displayAdminFiles', 'identifier' => 'admin_files'],
        ],
        'Views\Admin\Statistics' => [
            ['path' => '/admin/statistikk', 'method' => 'displayAdminStatistics', 'identifier' => 'admin_statistics'],
        ],
        'Views\Admin\Diagnostics' => [
            ['path' => '/admin/diagnostikk', 'method' => 'displayAdminDiagnostics', 'identifier' => 'admin_diagnostics'],
        ],
        'Views\Admin\Logs' => [
            ['path' => '/admin/logger', 'method' => 'displayAdminLogs', 'identifier' => 'admin_logs'],
        ],
        'Views\Admin\Scripts' => [
            ['path' => '/admin/scripts', 'method' => 'displayAdminScripts', 'identifier' => 'admin_scripts'],
        ],
        
        /*
         * Processors
         */

        'Processors\Favorites' => [
            ['path' => '/favorite'],
        ],
        'Processors\Module' => [
            ['path' => '/module/get', 'method' => 'get'],
            ['path' => '/module/update', 'method' => 'update'],
        ],
        'Processors\Register' => [
            ['path' => '/register/email', 'method' => 'checkEmail'],
        ],
        'Processors\StaticReturner' => [
            ['path' => '/search/courses.json'],
        ],
        'Processors\LoadHistory' => [
            ['path' => '/history/get'],
        ],
        'Processors\LinkTitle' => [
            ['path' => '/link/title'],
        ],
        'Processors\Graybox' => [
            ['path' => 'graybox/commits', 'method' => 'getCommits'],
            ['path' => 'graybox/newest', 'method' => 'getNewest'],
            ['path' => 'graybox/popular', 'method' => 'getPopular'],
        ],

        /*
         * Creates
         */

        'Processors\CreateFile' => [
            ['path' => '/file/create'],
        ],
        'Processors\CreateLink' => [
            ['path' => '/link/create'],
        ],
        'Processors\CreateFolder' => [
            ['path' => '/folder/create'],
        ],
        
        /*
         * Admin
         */
        
        'Processors\Admin\HomeBoxes' => [
            ['path' => '/admin/homeboxes'],
        ],
        'Processors\Admin\HomeGraph' => [
            ['path' => '/admin/homegraph'],
        ],

        /*
         * Tasks
         */

        'Processors\Tasks\Upgrade' => [
            ['path' => '/tasks/upgrade'],
        ],
        'Processors\Tasks\ClearCache' => [
            ['path' => '/tasks/clearcache'],
        ],
        'Processors\Tasks\LoadCourses' => [
            ['path' => '/tasks/courses'],
        ],
        'Processors\Tasks\LoadCoursesJson' => [
            ['path' => '/tasks/coursesjson'],
        ],
        'Processors\Tasks\LoadExams' => [
            ['path' => '/tasks/exams'],
        ],
        'Processors\Tasks\FindDuplicates' => [
            ['path' => '/tasks/duplicates'],
        ],
        'Processors\Tasks\GetCacheData' => [
            ['path' => '/tasks/cachedata'],
        ],
        
        /*
         * Syncs
         */
        
        'Processors\Tasks\Sync\SyncEmpty' => [
            ['path' => '/tasks/sync/syncempty'],
        ],
        'Processors\Tasks\Sync\SyncKarma' => [
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
    public static function getRedirects() {
        return self::$redirects;
    }
}