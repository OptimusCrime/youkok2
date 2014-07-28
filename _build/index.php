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

require_once BASE_PATH . '/_build/libs/youkok2/linecounter.class.php';

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
echo '<span>------------------------------------</span><br />';

//
// Count number of lines
//

$ignore_paths = array(
	// Remove all directories we don't want
	'!.git/',
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

$ignore_files = array(
	// Remove all root files we don't want
	'!.htaccess',
	'!.gitignore',
	'!local.php',
	'!composer.lock',
    '!phinx-example.yml',
    '!phinx.yml',
);

$add_files = array(
	// Override directories we have ignored earlier
	'_build/index.php',
	'_build/libs/youkok2/linecounter.class.php',
	'assets/js/youkok.js',
	'assets/css/youkok.css',
	'libs/youkok2/cachemanager.php',
	'libs/youkok2/executioner.php',
	'libs/youkok2/utilities.php',
);

$linecounter = new LineCounter($ignore_paths, $ignore_files, $add_files);
$linecounter->analyze();
echo '<span style="color: green;">Read a total of ' . number_format($linecounter->getTotalLines()) . ' lines</span><br />';

//
// Finish
//

echo '<span>------------------------------------</span><br />';
echo '<span style="color: green;">Finished</span><br />';
?>