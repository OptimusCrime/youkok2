<?php
/*
 * File: home.controller.php
 * Holds: The HomeController-class
 * Created: 02.10.13
 * Project: Youkok2
*/

namespace Youkok2\Views;

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Me as Me;
use \Youkok2\Shared\Elements as Elements;
use \Youkok2\Utilities\Database as Database;

class Home extends Youkok2 {

    //
    // The constructor for this subclass
    //

    public function __construct($kill = false) {
        // Calling Base' constructor
        parent::__construct();
        
        // Load newest files
        $this->template->assign('HOME_NEWEST', Elements::getNewest());
        
        // Load most popular files
        $this->template->assign('HOME_MOST_POPULAR', Elements::getMostPopular());
        
        // Check if this user is logged in
        if (Me::isLoggedIn()) {
            $this->template->assign('HOME_INFOBOX', null);
            //$this->template->assign('HOME_USER_LATEST', $this->loadLastDownloads());
            //$this->template->assign('HOME_USER_FAVORITES', $this->loadFavorites());
        } else {
            //$this->template->assign('HOME_INFOBOX', $this->loadInfobox());
            //$this->template->assign('HOME_USER_LATEST', '<li class="list-group-item"><em><a href="#" data-toggle="dropdown" class="login-opener">Logg inn</a> eller <a href="registrer">registrer deg</a>.</em></li>');
            //$this->template->assign('HOME_USER_FAVORITES', '<li class="list-group-item"><em><a href="#" data-toggle="dropdown" class="login-opener">Logg inn</a> eller <a href="registrer">registrer deg</a>.</em></li>');
        }
        
        // Display the template
        $this->displayAndCleanup('index.tpl');
    }

    //
    // Method for loading user favorites
    //
    
    private function loadFavorites() {
        // Declear variable for storing content
        $ret = '';
        
        // Load all favorites
        $get_favorites = "SELECT f.file
        FROM favorite AS f
        LEFT JOIN archive AS a ON a.id = f.file
        WHERE f.user = :user
        AND a.is_visible = 1
        ORDER BY f.ordering ASC";
        
        $get_favorites_query = $this->db->prepare($get_favorites);
        $get_favorites_query->execute(array(':user' => $this->user->getId()));
        while ($row = $get_favorites_query->fetch(PDO::FETCH_ASSOC)) {
            // Get element
            $element = $this->collection->get($row['file']);

            // Get file if not cached
            if ($element == null) {
                $element = new Item($this);
                $element->setLoadRootParent(true);
                $element->createById($row['file']);
                $this->collection->add($element);
            }

            // CHeck if element was loaded
            if ($element != null) {
                $element_url = $element->generateUrl($this->routes['download'][0]);
                $root_parent = $element->getRootParent();
                if ($element->isDirectory()) {
                    if ($element->hasCourse()) {
                        $ret .= '<li class="list-group-item list-group-star"><i title="Fjern favoritt" data-id="' . $element->getId() . '" class="fa fa-times-circle star-remove"></i><a href="' . $element->generateUrl($this->routes['archive'][0]) . '">' . $element->getName() . ' — ' . $element->getCourse()->getName() . '</a></li>';
                    }
                    else {
                        $ret .= '<li class="list-group-item list-group-star"><i title="Fjern favoritt" data-id="' . $element->getId() . '" class="fa fa-times-circle star-remove""></i><a href="' . $element->generateUrl($this->routes['archive'][0]) . '">' . $element->getName() . '</a>' . (($root_parent == null or $element->getId() == $root_parent->getId()) ? '' : ' @ <a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . '</li>';
                    }
                }
                else {
                    // Check if we should load local dir for element
                    $local_dir_str = '';
                    if ($element->getParent() != $root_parent->getId()) {
                        $local_dir_element = $this->collection->get($element->getParent());
                        $local_dir_str = '<a href="' . $local_dir_element->generateUrl($this->routes['archive'][0]) . '">' . $local_dir_element->getName() . '</a> i ';
                    }
                    
                    if ($element->isLink()) {
                        $element_url = $element->generateUrl($this->routes['redirect'][0]);
                        $ret .= '<li class="list-group-item list-group-star"><i title="Fjern favoritt" data-id="' . $element->getId() . '" class="fa fa-times-circle star-remove"></i><a target="_blank" title="Link til: ' . $element->getUrl() . '" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . '</li>';
                    }
                    else {
                        $element_url = $element->generateUrl($this->routes['download'][0]);
                        $ret .= '<li class="list-group-item list-group-star"><i title="Fjern favoritt" data-id="' . $element->getId() . '" class="fa fa-times-circle star-remove"></i><a href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . '</li>';
                    }
                }
            }
        }
        
        // Check if null
        if ($ret == '') {
            $ret .= '<li class="list-group-item"><em>Du har ingen favoritter...</em></li>';
        }

        // Return the content
        return $ret;
    }

    //
    // Method for loading user latest downloads 
    //

    private function loadLastDownloads() {
        // Declear variable for storing content
        $ret = '';
        
        // Load all favorites
        $get_last_downloads = "SELECT d.file
        FROM download AS d
        LEFT JOIN archive AS a ON a.id = d.file
        WHERE d.user = :user
        AND a.is_visible = 1
        AND d.id = (
            SELECT dd.id
            FROM download dd
            WHERE d.file = dd.file
            ORDER BY dd.downloaded_time
            DESC LIMIT 1)
        ORDER BY d.downloaded_time DESC
        LIMIT 15";
        
        $get_last_downloads_query = $this->db->prepare($get_last_downloads);
        $get_last_downloads_query->execute(array(':user' => $this->user->getId()));
        while ($row = $get_last_downloads_query->fetch(PDO::FETCH_ASSOC)) {
            // Get element
            $element = $this->collection->get($row['file']);

            // Get file if not cached
            if ($element == null) {
                $element = new Item($this);
                $element->setLoadRootParent(true);
                $element->createById($row['file']);
                $this->collection->add($element);
            }

            // CHeck if element was loaded
            if ($element != null) {
                $element_url = $element->generateUrl($this->routes['download'][0]);
                $root_parent = $element->getRootParent();
                
                // Check if we should load local dir for element
                $local_dir_str = '';
                if ($element->getParent() != $root_parent->getId()) {
                    $local_dir_element = $this->collection->get($element->getParent());
                    $local_dir_str = '<a href="' . $local_dir_element->generateUrl($this->routes['archive'][0]) . '">' . $local_dir_element->getName() . '</a>, ';
                }
                
                if ($element->isLink()) {
                    $element_url = $element->generateUrl($this->routes['redirect'][0]);
                    $ret .= '<li class="list-group-item"><a target="_blank" title="Link til: ' . $element->getUrl() . '" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . '</li>';
                }
                else {
                    $element_url = $element->generateUrl($this->routes['download'][0]);
                    $ret .= '<li class="list-group-item"><a href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . '</li>';
                }
            }
        }
        
        // Check if null
        if ($ret == '') {
            $ret .= '<li class="list-group-item"><em>Du har ikke lastet ned noen filer enda...</em></li>';
        }

        // Return the content
        return $ret;
    }
    
    //
    // Method for loading infobox (users not logged in)
    //
    
    private function loadInfobox() {
        // Load users
        $get_user_number = "SELECT COUNT(id) as 'antall_brukere'
        FROM user";
        
        $get_user_number_query = $this->db->prepare($get_user_number);
        $get_user_number_query->execute();
        $get_user_number_result = $get_user_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Load files
        $get_file_number = "SELECT COUNT(id) as 'antall_filer'
        FROM archive
        WHERE is_directory = 0";
        
        $get_file_number_query = $this->db->prepare($get_file_number);
        $get_file_number_query->execute();
        $get_file_number_result = $get_file_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Load downloads
        $get_download_number = "SELECT COUNT(id) as 'antall_nedlastninger'
        FROM download";
        
        $get_download_number_query = $this->db->prepare($get_download_number);
        $get_download_number_query->execute();
        $get_dowload_number_result = $get_download_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Return text
        return '<h1>Hei og velkommen til Youkok2. Den beste kokeboka på nettet!</h1><p>Vi har for tiden <b>' . number_format($get_user_number_result['antall_brukere']) . '</b> registrerte brukere, <b>' . number_format($get_file_number_result['antall_filer']) . '</b> filer og totalt <b>' . number_format($get_dowload_number_result['antall_nedlastninger']) . '</b> nedlastninger i vårt system.</p><p>Som registrerte brukere på Youkok2 får mulighet til å lagre favoritter, se sine siste nedlastninger, samt muligheten til å laste opp filer og å bidra til å gjøre Youkok2 enda bedre. Du kan lese mer om dette i <a href="om">om-seksjonen</a> vår.</p><p>La oss gjøre studiehverdagen enklere, sammen!</p><p>- Youkok2</p>';
    }
}

//
// Return the class name
//

return 'HomeController';