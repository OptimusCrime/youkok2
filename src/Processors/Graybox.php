<?php
/*
 * File: Graybox.php
 * Holds: Fetch information for different grayboxes
 * Created: 14.07.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors;

use \Youkok2\Youkok2 as Youkok2;
use \Youkok2\Models\Element as Element;

class Graybox extends BaseProcessor {

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
        'Fixed typo 500ing the entire site', 'I messed up some stuff', 'Fixed admin interfjes', 'DFuck', 'What a mess',
        'Now actually clearing youkok2 cache, duurrr', 'Fixed archive markup a bit, still fucked up tho',
        'Fixed header yet again, looks cool', 'Began reimplemented archive, which is a pain in the aaaaass',
        'Trying to make stuff more OOP, broke some stuff ect', 'Made registration possible again, woopwoop', 'Well fuck',
        'For fuck sake', 'Damnit again, haha', 'JDFhdsjfklshlfsdjkflg', 'I suck even more', 'Well that was stupoid',
        'Removed typehead example (what stupid messed up stuff)', 'Fixing stuff, broke the frontpage',
        'Fixed a bug or someting', 'I am going to kill someone soon', 'NEW FUCKINGS BUILDDDDDDDDDDDDDDDDDDD', 'FUCK THIS',
        'Stupid stupid stupid stupid', 'Trying to fix fuckup in the update script', 'Fixed fuckup in the buildscript',
        'hrherhehrh', 'Shit shit shit shit', 'Removed fucked up stuff in shell script', 'Fixed eifnsdflhsdfkhdsalkhasdlksadh',
        'NOOO MOAR TABS, CAN I HAS ZHE SPACE INDENTS', 'Ran new build for no apparent reason',
        'Fuckings mac creating invisible files all over the place',
    ];

    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Constructor
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }

    /*
     * Get different commits
     */

    public function getCommits() {
        // Array for storing random commits
        $commits = [];

        // Get n random commits
        for ($i = 0; $i < 3; $i++) {
            $commits[] = $this->commits[rand(0, (count($this->commits) - 1))];
        }

        // Set to data
        $this->setData('data', $commits);

        // Set OK
        $this->setOk();
    }
    
    /*
     * Get the newest elements
     */
    
    public function getNewest() {
        // Get the collection
        $raw_collection = Element::getNewest();
        
        // Encode the raw collection
        $collection = [];
        foreach ($raw_collection as $v) {
            $collection[] = $v->toArray();
        }
        
        // Set to data
        $this->setData('data', $collection);
        
        // Set OK
        $this->setOk();
    }

    /*
     * Get most popular downloads
     */

    public function getPopular() {
        $data = Youkok2::runProcessor('/module/get',[
            'output' => false,
            'encode' => true,
            'close_db' => false,
            'delta' => 3]);

        // Set to data
        $this->setData('data', $data['data']);

        // Set OK
        $this->setOk();
    }
}