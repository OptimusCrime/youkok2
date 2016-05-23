<?php
/*
 * File: Routes.php
 * Holds: Holds all the routes the Loader matches urls against
 * Created: 02.11.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

class Routes
{
    
    /*
     * Used to identify processors
     */
    
    const PROCESSOR = '/processor';
    
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
            ['path' => Routes::PROCESSOR . '/favorite'],
        ],
        'Processors\Module' => [
            ['path' => Routes::PROCESSOR . '/module/get', 'method' => 'get'],
            ['path' => Routes::PROCESSOR . '/module/update', 'method' => 'update'],
        ],
        'Processors\Register' => [
            ['path' => Routes::PROCESSOR . '/register/email', 'method' => 'checkEmail'],
        ],
        'Processors\StaticReturner' => [
            ['path' => Routes::PROCESSOR . '/search/courses.json'],
        ],
        'Processors\LoadHistory' => [
            ['path' => Routes::PROCESSOR . '/history/get'],
        ],
        'Processors\LinkTitle' => [
            ['path' => Routes::PROCESSOR . '/link/title'],
        ],
        'Processors\Graybox' => [
            ['path' => Routes::PROCESSOR . '/graybox/commits', 'method' => 'getCommits'],
            ['path' => Routes::PROCESSOR . '/graybox/newest', 'method' => 'getNewest'],
            ['path' => Routes::PROCESSOR . '/graybox/popular', 'method' => 'getPopular'],
        ],

        /*
         * Creates
         */

        'Processors\CreateFile' => [
            ['path' => Routes::PROCESSOR . '/file/create'],
        ],
        'Processors\CreateLink' => [
            ['path' => Routes::PROCESSOR . '/link/create'],
        ],
        'Processors\CreateFolder' => [
            ['path' => Routes::PROCESSOR . '/folder/create'],
        ],
        
        /*
         * Admin
         */
        
        'Processors\Admin\HomeBoxes' => [
            ['path' => Routes::PROCESSOR . '/admin/homeboxes'],
        ],
        'Processors\Admin\HomeGraph' => [
            ['path' => Routes::PROCESSOR . '/admin/homegraph'],
        ],

        /*
         * Tasks
         */

        'Processors\Tasks\Upgrade' => [
            ['path' => Routes::PROCESSOR . '/tasks/upgrade'],
        ],
        'Processors\Tasks\ClearCache' => [
            ['path' => Routes::PROCESSOR . '/tasks/clearcache'],
        ],
        'Processors\Tasks\LoadCourses' => [
            ['path' => Routes::PROCESSOR . '/tasks/courses'],
        ],
        'Processors\Tasks\LoadCoursesJson' => [
            ['path' => Routes::PROCESSOR . '/tasks/coursesjson'],
        ],
        'Processors\Tasks\LoadExams' => [
            ['path' => Routes::PROCESSOR . '/tasks/exams'],
        ],
        'Processors\Tasks\FindDuplicates' => [
            ['path' => Routes::PROCESSOR . '/tasks/duplicates'],
        ],
        'Processors\Tasks\GetCacheData' => [
            ['path' => Routes::PROCESSOR . '/tasks/cachedata'],
        ],
        
        /*
         * Syncs
         */
        
        'Processors\Tasks\Sync\SyncEmpty' => [
            ['path' => Routes::PROCESSOR . '/tasks/sync/syncempty'],
        ],
        'Processors\Tasks\Sync\SyncKarma' => [
            ['path' => Routes::PROCESSOR . '/tasks/sync/synckarma'],
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
