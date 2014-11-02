<?php
namespace Youkok2;

/*
 * The Loader class. Loads the correct controller based on the url
 */

class Loader {

    /*
     * Internal variables
     */

    private $routes = array(
        'home' => array(
            '/',
        ),

        'archive' => array(
            '/kokeboka',
        ),

        'profile' => array(
            '/profil',
        ),

        'download' => array(
            '/last-ned',
        ),

        'flat' => array(
            '/om',
            '/retningslinjer',
            '/privacy',
            '/hjelp',
            '/karma',
            '/changelog.txt',
        ),

        'notfound' => array(
            '/404',
        ),

        'processor' => array(
            '/processor',
        ),

        'auth' => array(
            '/logg-inn',
            '/logg-ut',
            '/registrer',
            '/glemt-passord',
            '/nytt-passord',
            '/verifiser',
        ),

        'graybox' => array(
            '/graybox',
        ),

        'search' => array(
            '/sok',
        ),

        'admin' => array(
            '/admin',
        ),

        'redirect' => array(
            '/redirect',
        ),
    );

    /*
     * Constructor
     */

    public function __construct() {
        // Checking wether the path is set or not
        if (isset($_GET['q'])) {
            // We have a path, find the base-path to include the correct script
            if (strpos($_GET['q'], '/') !== false) {
                // We have multiple slashed, use the first one as base for path-lookup
                $path_split = explode('/', $_GET['q']);
                $path = '/' . $path_split[0];
            }
            else {
                // We don't have any slashes in the url, use what we got
                $path = '/' . str_replace('/', '', $_GET['q']);
            }
        } else {
            // Clean path, use a single shash to identify home-page
            $path = '/';
        }

        // Storing the controller to load
        $controller_filename = '';

        // Loop the path-array and find what controller to load
        foreach ($this->routes as $k => $v) {
            foreach ($v as $iv) {
                if ($iv == $path) {
                    // We found matching url-pattern, store controllername
                    $controller_filename = $k;
                    break;
                }
            }
        }

        // Build controller path
        $file = $this->buildControllerPath($controller_filename);

        // Checking to see if the file exsists
        if (file_exists($file)) {
            // File exists, load it
            $controller = require $file;
        }
        else {
            // Load not found
            $controller = require $this->buildControllerPath('notfound');
        }

        // Run instance
        new $controller($this->routes);
    }

    /*
     * Return the full path for a controller
     */

    private function buildControllerPath($name) {
        return BASE_PATH .
        '/youkok2/controllers/' .
        strtolower(str_replace(array('.', '/'), '', $name)) .
        '.controller.php';
    }
}