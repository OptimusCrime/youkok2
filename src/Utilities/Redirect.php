<?php
/*
 * File: Redirect.php
 * Holds: Script that redirects
 * Created: 28.11.2014
 * Project: Youkok2
 */

namespace Youkok2\Utilities;

/*
 * Define what classes to use
 */

use \Youkok2\Utilities\Database as Database;

/*
 * The redirect class
 */

class Redirect {

    /*
     * Method for redirecting
     */

    public static function send($p) {
        // Close database connection
        Database::close();

        // Redirect
        header('Location: ' . URL_FULL . $p);
    }
} 