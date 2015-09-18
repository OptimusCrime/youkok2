<?php
/*
 * File: UserController.php
 * Holds: The controller for the class User
 * Created: 19.09.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Models\Controllers;

class UserController extends BaseController {
    
    /*
     * Constructor
     */

    public function __construct($model) {
        parent::__construct($this, $model);
    }
}