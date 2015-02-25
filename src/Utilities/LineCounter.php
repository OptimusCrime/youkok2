<?php
/*
 * File: LineCounter.php
 * Holds: Home made script that counts number of lines in files
 * Created: ??.??.14
 * Project: Youkok2
*/

namespace Youkok2\Utilities;

/*
 * The linecounter
 */


class LineCounter {
    
    // Holds the settings
    private $settings;
    
    // Holds a multidimentional array of all directories and files
    private $elements;
    
    // Two dimentional array where key is filepath and value is number of lines
    private $lines;
    
    // Holds different types of lines
    private $lineTypes;
    
    // Debug
    private $debug;

    /*
     * Constructor
     */

    public function __construct($settings) {
        // Apply settings
        $this->settings = $settings;
        
        // Some initial data
        $this->elements = array('/' => array());
        $this->lines = array();
        $this->lineTypes = array();
    }
    
    /*
     * Count the number of lines
     */

    public function analyze() {
        // Apply ignores
        $this->recursiveWalk();
        
        // Add additional files to override the ignore
        $this->addFiles();
        
        // Count the lines
        $this->countLines('', $this->elements);
        
        // Sort the result
        ksort($this->lines);
        
        // Output
        $this->output();
    }
    
    /*
     * Walk recursivly
     */

    private function recursiveWalk($dir = '/') {
        // Open directory
        if ($handle = opendir(BASE_PATH . $dir)) {
            // Loop the entire directory
            while (false !== ($entry = readdir($handle))) {
                // Remove .., . and those fucking .DS_Store files
                if ($entry != '..' and $entry != '.' and $entry != '.DS_Store' and $entry != '._.DS_Store') {
                    // Check if is directory or not
                    if (is_dir(BASE_PATH . $dir . $entry)) {
                        // Directory, check if this directory is ignored
                        if (!$this->isIgnored($dir, $entry . '/')) {
                            // Is not ignored but might contain sub directories that are ignored, go recursive
                            $this->recursiveWalk($dir . $entry . '/');
                        }
                    }
                    else {
                        // Is not a directory, check if file is ignored
                        if (!$this->isIgnored($dir, $entry, false)) {
                            // File is not ignored, set node
                            $this->getNode($dir, $entry);
                        }
                    }
                }
            }
        }
    }
    
    /*
     * Apply files that are supposed to be ignored
     */
    
    private function addFiles() {
        // Loop the settings
        foreach ($this->settings as $v) {
            // Only handle settings not beginning with a !
            if (substr($v, 0, 1) != '!') {
                // Split the line and keep track of the current location
                $v_split = explode('/', $v);
                $current = '/';
                
                // Loop the split
                foreach ($v_split as $ik => $iv) {
                    // Check if we ended at the correct file
                    if (count($v_split) == ($ik + 1)) {
                        // Add node
                        $this->getNode($current, $iv);
                    }
                    else {
                        // Add dir
                        $current .= $iv . '/';
                    }
                }
            }
        }
    }
    
    /*
     * Check if a directory or file is ignored
     */
    
    private function isIgnored($dir, $file, $is_dir = true) {
        // Only run if initial check
        if ($dir == '/') {
            // Loop the settings
            foreach ($this->settings as $v) {
                // Only handle entried that begin with a !
                if (substr($v, 0, 1) == '!') {
                    // Filter dir and not dir
                    if (($is_dir and substr($v, strlen($v) - 1, 1) == '/') or (!$is_dir and substr($v, strlen($v) - 1, 1) != '/')) {
                        // Check that the filename matches
                        if ($v == '!' . $file) {
                            // This file or directory is ignored
                            return true;
                        }
                    }
                }
            }
            
            // Is not ignored
            return false;
        }
    }
    
    /*
     * Returns node by reference
     */
    
    private function getNode($dir, $entry) {
        // Split the full path
        $s = explode('/', $dir . $entry);
        
        // Get node by reference
        $node = &$this->elements;
        
        // Loop the full path
        foreach ($s as $k => $v) {
            // Reset the value if the key is root
            if ($k == 0) {
                $v = '/';
            }
            
            // Check if root exists
            if (!isset($node[$v])) {
                // Check if current element matches the entry we are looking for
                if ($v == $entry) {
                    // Append node
                    $node[] = $v;
                    
                    // Break out of the loop
                    break;
                }
                else {
                    // Create a new directory in the tree
                    $node[$v] = array();
                }
            }
            
            // Set node to the element by reference
            $node = &$node[$v];
        }
    }
    
    /*
     * Reads line of code
     */

    private function countLines($base, $path) {
        // Keep track of previous path
        $prev = '';
        
        // Loop the path
        foreach ($path as $k => $v) {
            // Check if the new element if a directory or not
            if (is_array($v)) {
                // Is directory, if the key is not /, apply this to the history
                if ($k != '/') {
                    $prev .= $k . '/';
                }
                
                // Add to lines (used for later)
                $this->lines[$base . $prev] = null;
                
                // Call the function again
                $this->countLines($base . $prev, $v);
                
                // Reset prev
                $prev = '';
            }
            else {
                // Is file, double check that the file exists
                if (is_file(BASE_PATH . '/' . $base . $v)) {
                    // File exists, reset linecounter
                    $linecount = 0;
                    
                    // Open file
                    $handle = fopen(BASE_PATH . '/' . $base . $v, "r");
                    
                    // Read lines
                    while (!feof($handle)){
                        $line = fgets($handle);
                        $linecount++;
                    }
                    
                    // Close handle
                    fclose($handle);
                    
                    // Save number of lines
                    $this->lines[$base . $v] = $linecount;
                    
                    // Save number of lines of type
                    $file_explode = explode('.', $v);
                    $file_type = $file_explode[count($file_explode) -1 ];
                    if (!isset($this->lineTypes[$file_type])) {
                        // New linetype, set initial value
                        $this->lineTypes[$file_type] = $linecount;
                    }
                    else {
                        // Existing linetype, increase
                        $this->lineTypes[$file_type] += $linecount;
                    }
                }
            }
        }
    }
    
    /*
     * Output
     */
    
    private function output() {
        $output = [];
        // Loop all lines
        foreach ($this->lines as $k => $v) {
            // Check if directory or a file
            if ($v === null) {
                // Directory, cool
                $output[] = array('type' => 'dir',
                    'path' => $k,
                    'lines' => number_format($this->sumLines($k)));
            }
            else {
                // This is a file
                $output[] = array('type' => 'file',
                    'path' => $k,
                    'lines' => number_format($v));
            }
        }
        
        // Read the output array
        if (php_sapi_name() == 'cli') {
            // Command line, new instance of CLImate
            $climate = new \League\CLImate\CLImate;
            foreach ($output as $v) {
                // For output
                $bold = false;
                
                // Split the path
                $v_split = explode('/', $v['path']);
                
                // Check if dir or file
                if ($v['type'] == 'dir') {
                    // Do bold
                    $bold = true;
                    
                    // Indent
                    $out = $this->repeatChar(count($v_split));
                    
                    // Append out
                    if (count($v_split) == 1) {
                        $out .= '/';
                    }
                    else {
                        $out .= $v_split[count($v_split) - 2] . '/';
                    }
                }
                else {
                    // Indent
                    $out = $this->repeatChar(count($v_split) + 1);
                    
                    // File name
                    $out .= $v_split[count($v_split) - 1];
                }
                
                $out .= ' - ' . $v['lines'];
                
                // Send to climate
                if ($bold) {
                    // Bold output
                    $climate->bold($out);
                }
                else {
                    // Normal output
                    $climate->out($out);
                }
            }
        }
        else {
            // Start pre (for space indent)
            echo '<pre>';
            
            // Browser, loop and "style"
            foreach ($output as $v) {
                // For output
                $bold = false;
                
                // Split the path
                $v_split = explode('/', $v['path']);
                
                // Check if dir or file
                if ($v['type'] == 'dir') {
                    // Do bold
                    $bold = true;
                    
                    // Indent
                    $out = $this->repeatChar(count($v_split));
                    
                    // Append out
                    if (count($v_split) == 1) {
                        $out .= '/';
                    }
                    else {
                        $out .= $v_split[count($v_split) - 2] . '/';
                    }
                }
                else {
                    // Indent
                    $out = $this->repeatChar(count($v_split) + 1);
                    
                    // File name
                    $out .= $v_split[count($v_split) - 1];
                }
                
                $out .= ' - ' . $v['lines'];
                
                // Send to climate
                if ($bold) {
                    // Bold output
                    echo '<b>' . $out . '</b><br />';
                }
                else {
                    // Normal output
                     echo  $out . '<br />';
                }
            }
            
            // End pre
            echo '</pre>';
        }
    }
    
    /*
     * Sum lines within one directory
     */
    
    private function sumLines($path) {
        $sum = 0;
        foreach ($this->lines as $k => $v) {
            if ($v !== null) {
                if ($path == substr($k, 0, strlen($path))) {
                    $sum += $v;
                }
            }
        }
        return $sum;
    }
    
    /*
     * Repeats a character x numer of times
     */
    
    private function repeatChar($num) {
        $str = '';
        for ($i = 0; $i < (($num - 1) * 4); $i++) {
            $str .= ' ';
        }
        return $str;
    }
}