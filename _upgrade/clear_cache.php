<?php
// Set headers
header('Content-Type: text/html; charset=utf-8');

// Includes
require dirname(__FILE__) . '/../local.php';

require BASE_PATH . '/vendor/autoload.php';

require BASE_PATH . '/libs/pdo2/pdo2.class.php';
require BASE_PATH . '/libs/pdo2/pdostatement2.class.php';

require BASE_PATH . '/controllers/youkok2.controller.php';
require BASE_PATH . '/controllers/external.controller.php';

$externalController = new ExternalController(array());
$externalController->getExternalService('youkok2.ClearCache', array());