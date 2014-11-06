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
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;
use \Youkok2\Utilities\Utilities as Utilities;

class Elements {
    
    public static $delta = array(
        ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND a.is_visible = 1', 
        ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND a.is_visible = 1', 
        ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND a.is_visible = 1', 
        ' WHERE a.is_visible = 1',
        ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.is_visible = 1');
    
    public static function getNewest() {
        // Declear variable for storing content
        $ret = '';
        
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
                    $local_dir_str = '<a href="' . $local_dir_element->controller->generateUrl(Routes::ARCHIVE) . '">' . $local_dir_element->getName() . '</a> i ';
                }
                
                if ($element->isLink()) {
                    $element_url = $element->controller->generateUrl(Routes::REDIRECT);
                    $ret .= '<li class="list-group-item"><a rel="nofollow" target="_blank" title="Link til: ' . $element->getUrl() . '" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->controller->generateUrl(Routes::ARCHIVE) . '" data-toggle="tooltip" data-placement="top" title="$root_parent->getCourse()->getName()">' . $root_parent->getName() . '</a>') . ' [<span class="moment-timestamp" style="cursor: help;" title="' . Utilities::prettifySQLDate($element->getAdded()) . '" data-ts="' . $element->getAdded() . '">Laster...</span>]</li>';
                }
                else {
                    $element_url = $element->controller->generateUrl(Routes::DOWNLOAD);
                    $ret .= '<li class="list-group-item"><a rel="nofollow" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->controller->generateUrl(Routes::ARCHIVE) . '" data-toggle="tooltip" data-placement="top" title="$root_parent->getCourse()->getName()">' . $root_parent->getName() . '</a>') . ' [<span class="moment-timestamp" style="cursor: help;" title="' . Utilities::prettifySQLDate($element->getAdded()) . '" data-ts="' . $element->getAdded() . '">Laster...</span>]</li>';
                }
            }
        }
        
        // Return the content
        return $ret;
    }

    //
    // Method for loading the files with most downloads
    //

    public static function getMostPopular($override = null) {
        $ret = '';

        $user_delta = Me::getUserDelta($override);

        // Load most popular files from the system
        $get_most_popular = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'
        FROM download d
        LEFT JOIN archive AS a ON a.id = d.file
        " . self::$delta[$user_delta] . "
        GROUP BY d.file
        ORDER BY downloaded_times DESC
        LIMIT 15";

        $get_most_popular_query = Database::$db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get element
            $element = ElementCollection::get($row['id']);

            // Get file if not cached
            if ($element == null) {
                $element = new Element();
                $element->controller->setLoadRootParent(true);
                $element->createById($row['id']);
                ElementCollection::add($element);
            }

            // Set downloaded
            $element->controller->setDownloadCount($user_delta, $row['downloaded_times']);

            // CHeck if element was loaded
            if ($element != null) {
                $root_parent = $element->controller->getRootParent();

                // Check if we should load local dir for element
                $local_dir_str = '';
                if ($element->getParent() != $root_parent->getId()) {
                    $local_dir_element = ElementCollection::get($element->getParent());
                    $local_dir_str = '<a href="' . $local_dir_element->controller->generateUrl(Routes::ARCHIVE) . '">' . $local_dir_element->getName() . '</a> i ';
                }

                if ($element->isLink()) {
                    $element_url = $element->controller->generateUrl(Routes::REDIRECT);
                    $ret .= '<li class="list-group-item"><a rel="nofollow" target="_blank" title="Link til: ' . $element->getUrl() . '" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->controller->generateUrl(Routes::ARCHIVE) . '" data-toggle="tooltip" data-placement="top" title="$root_parent->getCourse()->getName()">' . $root_parent->getName() . '</a>') . ' [' . number_format($element->controller->getDownloadCount(self::$delta[$user_delta])) . ']</li>';
                }
                else {
                    $element_url = $element->controller->generateUrl(Routes::DOWNLOAD);
                    $ret .= '<li class="list-group-item"><a rel="nofollow" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->controller->generateUrl(Routes::ARCHIVE) . '" data-toggle="tooltip" data-placement="top" title="$root_parent->getCourse()->getName()">' . $root_parent->getName() . '</a>') . ' [' . number_format($element->controller->getDownloadCount(self::$delta[$user_delta])) . ']</li>';
                }
            }
        }

        // Check if null
        if ($ret == '') {
            $ret = '<li class="list-group-item">Det er visst ingen nedlastninger i dette tidsrommet!</li>';
        }

        return $ret;
    }
}