<?php
/*
 * File: home.controller.php
 * Holds: The HomeController-class
 * Created: 02.10.13
 * Project: Youkok2
*/

namespace Youkok2\Shared;

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Element as Element;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;
use \Youkok2\Utilities\Utilities as Utilities;

class Elements {
    
    public static function getNewest() {
        // Declear variable for storing content
        $ret = '';
        
        // Get the routes
        $routes = Routes::getRoutes();
        
        // Loading newest files from the system TODO add filter
        $get_newest = "SELECT id
        FROM archive
        WHERE is_directory = 0
        AND is_visible = 1
        ORDER BY added DESC
        LIMIT 15";
        
        $get_newest_query = Database::$db->query($get_newest);
        while ($row = $get_newest_query->fetch(\PDO::FETCH_ASSOC)) {
            // Create new object
            $element = ElementCollection::get($row['id']);

            if ($element == null) {
                $element = new Element();
                $element->controller->setLoadRootParent(true);
                $element->controller->createById($row['id']);
                ElementCollection::add($element);
            }

            // Check if element was loaded
            if ($element != null) {
                $root_parent = $element->controller->getRootParent();
                
                // Check if we should load local dir for element
                $local_dir_str = '';
                if ($element->getParent() != $root_parent->getId()) {
                    $local_dir_element = ElementCollection::get($element->getParent());
                    $local_dir_str = '<a href="' . $local_dir_element->controller->generateUrl($routes['Archive'][0]) . '">' . $local_dir_element->getName() . '</a> i ';
                }
                
                if ($element->isLink()) {
                    $element_url = $element->controller->generateUrl($routes['Redirect'][0]);
                    $ret .= '<li class="list-group-item"><a rel="nofollow" target="_blank" title="Link til: ' . $element->getUrl() . '" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->controller->generateUrl($routes['Archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="$root_parent->getCourse()->getName()">' . $root_parent->getName() . '</a>') . ' [<span class="moment-timestamp" style="cursor: help;" title="' . Utilities::prettifySQLDate($element->getAdded()) . '" data-ts="' . $element->getAdded() . '">Laster...</span>]</li>';
                }
                else {
                    $element_url = $element->controller->generateUrl($routes['Download'][0]);
                    $ret .= '<li class="list-group-item"><a rel="nofollow" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->controller->generateUrl($routes['Archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="$root_parent->getCourse()->getName()">' . $root_parent->getName() . '</a>') . ' [<span class="moment-timestamp" style="cursor: help;" title="' . Utilities::prettifySQLDate($element->getAdded()) . '" data-ts="' . $element->getAdded() . '">Laster...</span>]</li>';
                }
            }
        }
        
        // Return the content
        return $ret;
    }
    
}