<?php
/*
 * File: HistoryController.php
 * Holds: Interface for the controllers
 * Created: 24.02.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Models\Controllers;

class HistoryController extends BaseController
{
    
    /*
     * Constructor
     */

    public function __construct($model) {
        parent::__construct($this, $model);
    }
}
