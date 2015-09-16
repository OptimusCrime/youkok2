<?php
/*
 * File: KarmaController.php
 * Holds: Interface for the controllers
 * Created: 17.09.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Models\Controllers;

class KarmaController extends BaseController {
    
    /*
     * Constructor
     */

    public function __construct($model) {
        parent::__construct($this, $model);
    }
    
    /*
     * To Array (for output)
     */

    public function toArray() {
        // Get the initial fields from the array
        return $this->model->toArrayInitial();
    }
}