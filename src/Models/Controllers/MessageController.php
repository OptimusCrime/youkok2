<?php
/*
 * File: Messageontroller.php
 * Holds: Controller for the model Message
 * Created: 14.07.2015
 * Project: Youkok2
*/

namespace Youkok2\Models\Controllers;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\CacheManager as CacheManager;
use \Youkok2\Utilities\Database as Database;

/*
 * The class ElementController
 */

class MessageController extends BaseController {

    /*
     * Constructor
     */

    public function __construct($model) {
        parent::__construct($this, $model);
    }
}