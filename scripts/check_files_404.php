<?php
// Set headers
header('Content-Type: text/html; charset=utf-8');

// Variables
$base_path = dirname(dirname(__FILE__));

// Includes
require_once $base_path . '/local.php';
require_once $base_path . '/elements/collection.class.php';
require_once $base_path . '/elements/item.class.php';


// Connect to database
try {
    $db  = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_TABLE, DATABASE_USER, DATABASE_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (Exception $e) {
    $db  = null;
}

// Authenticate if database-connection was successful
if ($db) {
    // Some variables
    $success = '';
    $error = '';
    $success_num = 0;
    $error_num = 0;
    
    // Init collection with root element
    $collection = new Collection();
    $root_element = new Item($collection, $db);
    $root_element->createById(1);
    $collection->add($root_element);
    
    // Search all files in the system
    $get_all_items = "SELECT id
    FROM archive
    WHERE is_directory = 0
    ORDER BY id DESC";
    
    $get_all_items_query = $db->prepare($get_all_items);
    $get_all_items_query->execute();
    while ($row = $get_all_items_query->fetch(PDO::FETCH_ASSOC)) {
        // Create element
        $element = new Item($collection, $db);
        $element->createById($row['id']);
        $collection->add($element);
        
        // Generate full url
        $url = SITE_URL_FULL . $element->generateUrl('/last-ned');
        
        // Init curl
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        
        // Do the actual request
        $foo = curl_exec($handle);
        
        // Gather http request info
        $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        
        // Check http code
        if($http_code == 404) {
            // Increase counter
            $error_num++;
            
            // Message
            $error .= 'Id: ' . $row['id'] . ' @ ' . $url . '<br />';
        }
        else {
            // Increase counter
            $success_num++;
            
            // Message
            $success .= 'Id: ' . $row['id'] . ' @ ' . $url . '<br />';
        }
        
        // Close handler
        curl_close($handle);
    }
    
    // Close db
    $db = null;
    
    // Output
    echo '<h2 style="color: red;">Errors (' . number_format($error_num) . ')</h2>';
    echo $error;
    echo '<hr />';
    
    echo '<h2 style="color: green;">Success (' . number_format($success_num) . ')</h2>';
    echo $success;
}
else {
    die('Could not connect to database');
}
?>