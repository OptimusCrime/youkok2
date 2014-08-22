<?php
/*
 * File: clearecache.class.php
 * Holds: Clears all cache for fresh upgrade
 * Created: 20.07.14
 * Project: Youkok2
 * 
*/

Class ClearCache {

    //
    // Construct
    //

    public function __construct() {
        
        //
        // Trying to include base
        //
        
        $local_file = dirname(dirname(__FILE__)) . '/local.php';
        if (!file_exists($local_file)) {
            die("Please create your local.php-file");
        }
        
        // Include base
        include_once $local_file;

        //
        // Typehead backup
        //

        copy(BASE_PATH . '/cache/typeahead-example.json', BASE_PATH . '/typeahead-example.json');

        if (file_exists(BASE_PATH . '/cache/typeahead.json')) {
            copy(BASE_PATH . '/cache/typeahead.json', BASE_PATH . '/typeahead.json');
        }

        //
        // Clear Smarty Cache
        //

        // New Smarty instance
        $smarty = new Smarty();
        $smarty->setCacheDir(BASE_PATH . '/cache/');

        // Cleare cache
        $smarty->clearAllCache();

        //
        // Move Typehead back
        //

        copy(BASE_PATH . '/typeahead-example.json', BASE_PATH . '/cache/typeahead-example.json');
        unlink(BASE_PATH . '/typeahead-example.json');

        if (file_exists(BASE_PATH . '/typeahead.json')) {
            copy(BASE_PATH . '/typeahead.json', BASE_PATH . '/cache/typeahead.json');
            unlink(BASE_PATH . '/typeahead.json');
        }

        //
        // Feedback to user
        //

        echo "Cleared cache.\n";
    }
}

//
// Run
//

new ClearCache();