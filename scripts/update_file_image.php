<?php
// Set headers
header('Content-Type: text/html; charset=utf-8');

// Variables
$base_path = dirname(dirname(__FILE__));
require_once $base_path . '/local.php';

// Connect to database
try {
    $db  = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_TABLE, DATABASE_USER, DATABASE_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (Exception $e) {
    $db  = null;
}

// Authenticate if database-connection was successful
if ($db) {
    $get_all_items = "SELECT id, name, mime_type, missing_image
    FROM archive
    WHERE is_directory = 0
    ORDER BY id DESC";
    
    $missing = array();
    $wrong = array();
    $get_all_items_query = $db->prepare($get_all_items);
    $get_all_items_query->execute();
    while ($row = $get_all_items_query->fetch(PDO::FETCH_ASSOC)) {
        if (file_exists(BASE_PATH . '/assets/css/lib/images/mimetypes64/' . str_replace('/', '_', $row['mime_type']) . '.png')) {
            $has_missing_image = 0;
        }
        else {
            $has_missing_image = 1;
        }
        
        if ($has_missing_image != $row['missing_image']) {
            if ($has_missing_image) {
                $wrong[] = '<span style="color: red;">' . $row['id'] . ': ' . $row['name'] . ' has lost image for mimetype: ' . $row['mime_type'] . '!</span><br />';
            }
            else {
                $wrong[] = '<span style="color: green;">' . $row['id'] . ': ' . $row['name'] . ' has image for mimetype: ' . $row['mime_type'] . '!</span><br />';
            }
            
            $update_element = "UPDATE archive
            SET missing_image = :missing
            WHERE id = :id";
            $update_element_query = $db->prepare($update_element);
            $update_element_query->execute(array(':missing' => $has_missing_image,
                                                 ':id' => $row['id']));
            
        }
        if ($has_missing_image) {
            $missing[] = '<span style="color: red;">' . $row['id'] . ': ' . $row['name'] . ' has missing image for mimetype: ' . $row['mime_type'] . '!</span><br />';
        }
    }
    
    echo '<h2>Corrections (' . count($wrong) . '):</h2>';
    if (count($wrong) > 0) {
        echo implode('', $wrong);
    }
    else {
        echo '<span style="color: green;">Ingen!</span><br />';
    }
    
    echo '<h2>Missing (' . count($missing) . '):</h2>';
    if (count($missing) > 0) {
        echo implode('', $missing);
    }
    else {
        echo '<span style="color: green;">Ingen!</span><br />';
    }
    
    if (count($wrong) > 0 or count($missing) > 0) {
        echo '<h2>Clearing cache</h2>';
        include BASE_PATH . '/_upgrade/clearcache.class.php';
    }
}
else {
    // Return error
    echo '<h2>Error</h2>';
    die('Could not connect to database!');
}