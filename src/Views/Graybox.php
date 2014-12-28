<?php
/*
 * File: Graybox.php
 * Holds: The Graybox-view
 * Created: 23.04.14
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Shared\Elements as SharedElements;
use \Youkok2\Utilities\Database as Database;

/*
 * The Graybox class, extending Base class
 */

class Graybox extends Base {

    /*
     * Internal variables
     */
    
    private $commits = [
        'Forgot the emails, fuuuck', 'Added homescreen text and stuff', 'Added some stuff to header',
        'Cleanup', 'Added good mails', 'Added very good error message if database-connection goes down',
        'Forgot one stupid line', 'Them dates', 'Ups', 'Changed order of stuff in header',
        'Trying to upload not allowed filetype gies error', 'Fixed accidental error',
        'Cleanup', 'Fixed issue where uploader would reload page too soon, fucking up the uploads',
        'Fixed stupid mistake', 'FUCK', 'Vuupps', 'Unfucking stuff',
        'Removed a string that was not supposted to be there', 'Derp', 'Fixed context menu being fucked',
        'Prettieid modals', 'Minor fixes, overhaul, bugstuff etc', 'Derp', 'Fixed header yet again',
        'Hide stuff that should noe be there', 'Forgot the supid dates again', 'Much prettification',
        'Fixed stuff', 'e.pventdefault ass', 'Fixed header a bit more', 'Fixed w3 validator fuckup',
        'Added theme and began making stuff prettier :)', 'Fixed some of the fuck in the header',
        'Fixed commented out line which broke stuff', 'Fixed stupid error', 'wups', 'Cleaned up a bit',
        'Minor cleanup', 'Fixed supid thingy', 'Removed some anoying stuff',
        'Working on implementing the fileupload-stuff', 'And again', 'Changed dates, because I suck',
        'Deleted empty nameless file', 'Expanding the login-meganicsm', 'Added possibility to log in',
        'Made it possible to star stuff and many fixes and stuff', 'Mejooor refractooor',
        'Fixed stupid error causing downloads to be all messed up :)', 'WTF',
        'Did stuff', 'Added overlay and stuff', 'Added autocomplete n stuff',
        'Adde constants for urls etc (might still be some leftovers', 'Removed old methods from the fucked up past',
        'Reimplemented download and 404 handling :D', 'So much done, awesome souce',
        'Fixed various issues and errors, derp', 'Workig on refractoring the entire thingyy',
        'Moved some files around etc', 'Added bootstrap and a lot of other stuff to the project... WIP so much',
        'Began working on a lot of stuff', 'Added some stuff and made more dyamic', 'Did some changes...',
        'Refractor is the shit, I fix stuff and...well', 'Wtf am I doing', 'Fuckings tab indents', 'Strupid',
        'Refract00o0oring to get everything to work again, zzzz', 'Asaaand removed composer rofl', 'Fixed old leftovershit',
        'Messed up the order of the params, fuckem', 'MOST IMPORTANT CHANGES EVER FRRIKING HELL', 
        'Soooo much refractoring my eyes are dropping', 'Refractor the loader class to reflect some bad design choices',
        'Began working on a nice as F caching system', 'Added epic search functionality', 'Removed a hell of a lot images',
        'Fixed typo 500ing the entire site', 'I messed up some stuff', 
    ];

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();

        // Check query
        if ($this->queryGetClean() == 'graybox/newest') {
            $this->generateNewest();
        }
        else if ($this->queryGetClean() == 'graybox/downloads') {
            $this->generateDownloads();
        }
        else if ($this->queryGetClean() == 'graybox/numbers') {
            $this->generateNumbers();
        }
    }
    
    /*
     * Method for generating graybox for newest files
     */
    
    private function generateNewest() {
        echo '<ul class="list-group">' . SharedElements::getNewest() . '</ul>';
    }
    
    /*
     * Method for generating graybox for newest downloads
     */
    
    private function generateDownloads() {
        echo '<ul class="list-group">' . SharedElements::getMostPopular(4) . '</ul>';
    }
    
    /*
     * Method for generating grayfor for funny numbers
     */
    
    private function generateNumbers() {
        echo '<p><strong>Tilfeldig commit:</strong> ' . $this->commits[rand(0, (count($this->commits) - 1))] . '</p>';
    }
}