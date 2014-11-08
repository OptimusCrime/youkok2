<?php
/*
 * File: Elements.php
 * Holds: Methods for fetching several Elements in a cluser
 * Created: 02.10.13
 * Project: Youkok2
*/

namespace Youkok2\Shared;

/*
 * Define what classes to use
 */
 
use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * The class Elements, represents loading of a clusered collection Element
 */

class Elements {
    
    /*
     * Variable defining how to fetch time spesific intervals
     */
    
    public static $delta = array(
        ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND a.is_visible = 1', 
        ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND a.is_visible = 1', 
        ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND a.is_visible = 1', 
        ' WHERE a.is_visible = 1',
        ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.is_visible = 1');
    
    /*
     * Fetch newest Elements
     */
    
    public static function getNewest() {
        // Declear variable for storing content
        $ret = '';
        
        // Loading newest files from the system TODO add filter
        $get_newest = "SELECT id
        FROM archive
        WHERE is_directory = 0
        AND is_visible = 1
        ORDER BY added DESC, name DESC
        LIMIT 15";
        
        $get_newest_query = Database::$db->query($get_newest);
        while ($row = $get_newest_query->fetch(\PDO::FETCH_ASSOC)) {
            // Check if cached
            $element = ElementCollection::get($row['id']);
            
            // Not cached, init
            if ($element == null) {
                $element = new Element();
                $element->controller->setLoadRootParent(true);
                $element->controller->createById($row['id']);
                
            }

            // Check if valid Element
            if ($element->controller->wasFound()) {
                ElementCollection::add($element);
                $ret .= $element->controller->getFrontpageLink('added');
            }
        }
        
        // Return the content
        return $ret;
    }

    /*
     * Fetch most popular Elements
     */

    public static function getMostPopular($override = null) {
        $ret = '';

        $user_delta = Me::getUserDelta($override);

        // Load most popular files from the system
        $get_most_popular = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'
        FROM download d
        LEFT JOIN archive AS a ON a.id = d.file
        " . self::$delta[$user_delta] . "
        GROUP BY d.file
        ORDER BY downloaded_times DESC, a.added DESC
        LIMIT 15";

        $get_most_popular_query = Database::$db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(\PDO::FETCH_ASSOC)) {
            // Check if cached
            $element = ElementCollection::get($row['id']);
            
            // Not cached, init
            if ($element == null) {
                $element = new Element();
                $element->controller->setLoadRootParent(true);
                $element->createById($row['id']);
            }
            
            // Check if valid Element
            if ($element->controller->wasFound()) {
                ElementCollection::add($element);
                
                // Set download count
                $element->controller->setDownloadCount($user_delta, $row['downloaded_times']);
                
                // Generate string
                $ret .= $element->controller->getFrontpageLink('most-popular', $override);
            }
        }

        // Check if null
        if ($ret == '') {
            $ret = '<li class="list-group-item">Det er visst ingen nedlastninger i dette tidsrommet!</li>';
        }

        return $ret;
    }
    
    /*
     * Fetch Me favorites
     */
    
    public static function getFavorites() {
        $favorites = Me::getFavorites();
        
        if (count($favorites) > 0) {
            $ret = '';
            foreach ($favorites as $favorite) {
                // Check if cached
                $element = ElementCollection::get($favorite);
                
                // Not cached, init
                if ($element == null) {
                    $element = new Element();
                    $element->createById($favorite);
                    
                }
                
                // Check if valid Element
                if ($element->controller->wasFound()) {
                    ElementCollection::add($element);
                    $ret .= $element->controller->getFrontpageLink('favorites');
                }
            }
            
            if ($ret == '') {
                $ret = '<li class="list-group-item"><em>Du har ingen favoritter...</em></li>';
            }
            
            // Return the list
            return $ret;
        }
    }
}