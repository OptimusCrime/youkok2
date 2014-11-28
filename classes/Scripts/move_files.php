<?php
namespace Youkok2\Scripts;
require dirname(dirname(dirname(__FILE__))) . '/index.php';

use Youkok2\Models\Element;
use \Youkok2\Utilities\Database as Database;

Database::connect();

 // Loading newest files from the system TODO add filter
$get_newest = "SELECT id
FROM archive
WHERE is_directory = 0
AND url IS NULL";

$get_newest_query = Database::$db->query($get_newest);
while ($row = $get_newest_query->fetch(\PDO::FETCH_ASSOC)) {
    $element = new Element();
    $element->controller->setLoadFullLocation(true);
    $element->controller->setLoadRootParent(true);
    $element->controller->createById($row['id']);
    echo FILE_PATH . '/' . $element->controller->getFullLocation() . '<br />';
}