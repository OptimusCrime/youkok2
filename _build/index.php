<?php
/*
 * File: index.php
 * Holds: Creates new builds for the system
 * Created: 13.05.13
 * Project: Youkok2
 * 
*/

//
// Include the libs
//

require_once '../local.php';
require_once BASE_PATH . '/_build/libs/minify/Minify.php';
require_once BASE_PATH . '/_build/libs/minify/CSS.php';
require_once BASE_PATH . '/_build/libs/minify/JS.php';
require_once BASE_PATH . '/_build/libs/minify/Exception.php';

//
// Set namespace
//

use MatthiasMullie\Minify;

//
// Minify CSS
//

$css_minifier = new Minify\CSS(BASE_PATH . '/assets/css/youkok.css');
$css_minifier->minify(BASE_PATH . '/assets/css/youkok.min.css');
$css_minifier = null;
echo '<span style="color: green;">Minified CSS</span><br />';

//
// Minify JS
//

$js_minifier = new Minify\JS(BASE_PATH . '/assets/js/youkok.js');
$js_minifier->minify(BASE_PATH . '/assets/js/youkok.min.js');
echo '<span style="color: green;">Minified JS</span><br />';

//
// Finish
//

echo '<span>------------------------------------</span><br />';
echo '<span style="color: green;">Finished</span><br />';
?>