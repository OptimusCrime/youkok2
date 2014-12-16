<?php
/*
 * File: BaseController.php
 * Holds: Interface for the controllers
 * Created: 06.11.2014
 * Project: Youkok2
*/

namespace Youkok2\Models\Controllers;

/*
 * The interface
 */

interface BaseController {
    // Construct
    public function __construct($model);
    
    // Method for caching
    public function cache();
    
    // Method for saving
    public function save();
    
    // Method for updating
    public function update();
}