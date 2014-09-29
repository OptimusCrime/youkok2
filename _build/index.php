<?php
// Set headers
header('Content-Type: text/html; charset=utf-8');

// Includes
require '../local.php';

require BASE_PATH . '/vendor/autoload.php';

require BASE_PATH . '/libs/pdo2/pdo2.class.php';
require BASE_PATH . '/libs/pdo2/pdostatement2.class.php';
require BASE_PATH . '/libs/minify/minify.class.php';

require BASE_PATH . '/controllers/youkok2.controller.php';
require BASE_PATH . '/controllers/external.controller.php';

$externalController = new ExternalController(array());

//
// Minify CSS
//

$css_minifier = $externalController->getExternalService('minify.CSS', array(BASE_PATH . '/assets/css/youkok.css'));
$css_minifier->minify(BASE_PATH . '/assets/css/youkok.min.css');
$css_minifier = null;
echo '<span style="color: green;">Minified CSS</span><br />';

//
// Minify JS
//

$js_minifier = $externalController->getExternalService('minify.JS', array(BASE_PATH . '/assets/js/youkok.js'));
$js_minifier->minify(BASE_PATH . '/assets/js/youkok.min.js');
echo '<span style="color: green;">Minified JS</span><br />';
echo '<span>------------------------------------</span><br />';

//
// Count number of lines
//

// Remove all directories we don't want
$ignore_paths = array(
    '!.git/',
    '!.idea/',
    '!_build/',
    '!assets/',
    '!cache/',
    '!files',
    '!libs/',
    '!migrations/',
    '!processor/',
    '!templates_c/',
    '!vendor/',
);

// Remove all root files we don't want
$ignore_files = array(
    '!.htaccess',
    '!.gitignore',
    '!.idea',
    '!local.php',
    '!composer.lock',
    '!phinx-example.yml',
    '!phinx.yml',
);

// Override directories we have ignored earlier
$add_files = array(
    '_build/index.php',
    'assets/js/youkok.js',
    'assets/css/youkok.css',
    'libs/pdo/pdo2.class.php',
    'libs/pdo/pdostatement2.class.php',
    'libs/youkok2/cachemanager.class.phpp',
    'libs/youkok2/clearcache.class.php',
    'libs/youkok2/executioner.php',
    'libs/youkok2/linecounter.php',
    'libs/youkok2/utilities.php',
);

$linecounter = $externalController->getExternalService('youkok2.LineCounter',
                                                       array($ignore_paths,
                                                             $ignore_files, 
                                                             $add_files));

$linecounter->analyze();
echo '<span style="color: green;">Read a total of ' . number_format($linecounter->getTotalLines())
   . ' lines</span><br />';
echo $linecounter->getOutput();

//
// Finish
//

echo '<span>------------------------------------</span><br />';
echo '<span style="color: green;">Finished</span><br />';
?>