<?php
/*
 * File: home.controller.php
 * Holds: The HomeController-class
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

//
// Displaying the Home screen
//

class HomeController extends Youkok2 {

    //
    // The constructor for this subclass
    //

    public function __construct($routes, $kill = false) {
        // Calling Base' constructor
        parent::__construct($routes);
        
        // Check if we should autodisplay or not
        if ($kill == false) {
            // Load newest files
            $this->template->assign('HOME_NEWEST', $this->loadNewest());
            
            // Load most popular files
            $this->template->assign('HOME_MOST_POPULAR', $this->loadMostPopular());
            
            // Check if this user is logged in
            if ($this->user->isLoggedIn()) {
                $this->template->assign('HOME_INFOBOX', null);
                $this->template->assign('HOME_USER_LATEST', $this->loadLastDownloads());
                $this->template->assign('HOME_USER_FAVORITES', $this->loadFavorites());
            } else {
                $this->template->assign('HOME_INFOBOX', $this->loadInfobox());
                $this->template->assign('HOME_USER_LATEST', '<li class="list-group-item"><em><a href="#" data-toggle="dropdown" class="login-opener">Logg inn</a> eller <a href="registrer">registrer deg</a>.</em></li>');
                $this->template->assign('HOME_USER_FAVORITES', '<li class="list-group-item"><em><a href="#" data-toggle="dropdown" class="login-opener">Logg inn</a> eller <a href="registrer">registrer deg</a>.</em></li>');
            }
            
            // Display the template
            $this->displayAndCleanup('index.tpl');
        }
    }
    
    //
    // Method for loading the newest files in the system
    //
    
    private function loadNewest() {
        // Declear variable for storing content
        $ret = '';
        
        // Loading newest files from the system TODO add filter
        $get_newest = "SELECT id
        FROM archive
        WHERE is_directory = 0
        ORDER BY added DESC
        LIMIT 15";
        
        $get_newest_query = $this->db->query($get_newest);
        while ($row = $get_newest_query->fetch(PDO::FETCH_ASSOC)) {
            // Create new object
            $element = $this->collection->get($row['id']);

            if ($element == null) {
                $element = new Item($this);
                $element->setLoadRootParent(true);
                $element->createById($row['id']);
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
                
                $ret .= '<li class="list-group-item"><a rel="nofollow" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . ' [<span class="moment-timestamp" style="cursor: help;" title="' . $this->utils->prettifySQLDate($element->getAdded()) . '" data-ts="' . $element->getAdded() . '">Laster...</span>]</li>';
            }
        }
        
        // Return the content
        return $ret;
    }
    
    //
    // Method for loading the files with most downloads
    //
    
    public function loadMostPopular() {
        $ret = '';

        // Deltas
        $delta = array(' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK)', 
            ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH)', 
            ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR)', 
            '',
            ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)');

        if ($this->user->isLoggedIn()) {
            $user_delta = $this->user->getMostPopularDelta();
        }
        else {
            if (isset($_COOKIE['home_popular'])) {
                $user_delta = $_COOKIE['home_popular'];
            }
            else {
                $user_delta = 1;
            }
        }

        // Assign to Smarty
        $this->template->assign('HOME_MOST_POPULAR_DELTA', $user_delta);
        
        // Load most popular files from the system
        $get_most_popular = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'
        FROM download d
        " . $delta[$user_delta] . "
        GROUP BY d.file
        ORDER BY downloaded_times DESC
        LIMIT 15";
        
        $get_most_popular_query = $this->db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(PDO::FETCH_ASSOC)) {
            // Get element
            $element = $this->collection->get($row['id']);

            // Get file if not cached
            if ($element == null) {
                $element = new Item($this);
                $element->setLoadRootParent(true);
                $element->createById($row['id']);
                $this->collection->add($element);
            }

            // Set downloaded
            $element->setDownloadCount($user_delta, $row['downloaded_times']);

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
                
                $ret .= '<li class="list-group-item"><a rel="nofollow" href="' . $element_url . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . ' [' . number_format($element->getDownloadCount($user_delta)) . ']</li>';
            }
        }

        // Check if null
        if ($ret == '') {
            $ret = '<li class="list-group-item">Det er visst ingen nedlastninger i dette tidsrommet!</li>';
        }
        
        return $ret;
    }

    //
    // Method for loading user favorites
    //
    
    private function loadFavorites() {
        // Declear variable for storing content
        $ret = '';
        
        // Load all favorites
        $get_favorites = "SELECT file
        FROM favorite
        WHERE user = :user
        ORDER BY ordering ASC";
        
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
                    $ret .= '<li class="list-group-item"><a href="' . $element->generateUrl($this->routes['archive'][0]) . '">' . $element->getName() . '</a>' . (($root_parent == null or $element->getId() == $root_parent->getId()) ? '' : ' @ <a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . '</li>';
                }
                else {
                    // Check if we should load local dir for element
                    $local_dir_str = '';
                    if ($element->getParent() != $root_parent->getId()) {
                        $local_dir_element = $this->collection->get($element->getParent());
                        $local_dir_str = '<a href="' . $local_dir_element->generateUrl($this->routes['archive'][0]) . '">' . $local_dir_element->getName() . '</a>, ';
                    }
                
                    $ret .= '<li class="list-group-item"><a href="' . $element->generateUrl($this->routes['download'][0]) . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . '</li>';
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
        $get_last_downloads = "SELECT file
        FROM download d
        WHERE user = :user
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
                
                $ret .= '<li class="list-group-item"><a href="' . $element->generateUrl($this->routes['download'][0]) . '">' . $element->getName() . '</a> @ ' . $local_dir_str . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->routes['archive'][0]) . '" data-toggle="tooltip" data-placement="top" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>') . '</li>';
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
        return '<h3>Hei og velkommen til Youkok2. Den beste kokeboka på nettet!</h3><p>Vi har for tiden <b>' . number_format($get_user_number_result['antall_brukere']) . '</b> registrerte brukere, <b>' . number_format($get_file_number_result['antall_filer']) . '</b> filer og totalt <b>' . number_format($get_dowload_number_result['antall_nedlastninger']) . '</b> nedlastninger i vårt system.</p><p>Som registrerte brukere på Youkok2 får mulighet til å lagre favoritter, se sine siste nedlastninger, samt muligheten til å laste opp filer og å bidra til å gjøre Youkok2 enda bedre. Du kan lese mer om dette i <a href="om">om-seksjonen</a> vår.</p><p>La oss gjøre studiehverdagen enklere, sammen!</p><p>- Youkok2</p>';
    }
}

//
// Return the class name
//

return 'HomeController';