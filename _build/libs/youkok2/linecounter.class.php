<?php

Class LineCounter {

    private $ignore_paths;
    private $ignore_files;
    private $add_files;

    private $elements;
    private $lines;
    private $lines_type;

    private $totalNumber;

    public function __construct($ignore_paths, $ignore_files, $add_files) {
        $this->ignore_paths = $ignore_paths;
        $this->ignore_files = $ignore_files;
        $this->add_files = $add_files;

        $this->elements = array('/' => array());
        $this->lines = array();
        $this->lines_type = array();

        $this->totalNumber = 0;
    }

    public function analyze() {

        //
        // Fix ignore paths
        //

        $this->ingorePaths();

        //
        // Add files
        //

        $this->addFiles();

        //
        // Number of lines
        //

        $this->lines('', $this->elements);

        $this->totalNumber = array_sum($this->lines_type);
    }

    public function getTotalLines() {
        return $this->totalNumber;
    }

    private function ingorePaths ($dir = '/') {
        if ($handle = opendir(BASE_PATH . $dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '..' and $entry != '.' and $entry != '.DS_Store'
                    and $entry != '._.DS_Store') {
                    if (is_dir(BASE_PATH . $dir . $entry)) {
                        if (!$this->isIgnored($dir, $entry . '/')) {
                            $this->ingorePaths($dir . $entry . '/');
                        }
                    }
                    else {
                        if (!$this->isIgnored($dir, $entry, false)) {
                            $node = $this->getNode($dir, $entry);
                        }
                    }
                }
            }
        }
    }

    private function addFiles() {
        foreach ($this->add_files as $v) {
            $v_split = explode('/', $v);
            $current = '/';
            foreach ($v_split as $ik => $iv) {
                if (count($v_split) == ($ik + 1)) {
                    $this->getNode($current, $iv);
                }
                else {
                    $current .= $iv . '/';
                }

            }
        }
    }

    private function isIgnored($dir, $file, $is_dir = true) {
        if ($dir == '/') {
            if ($is_dir) {
                foreach ($this->ignore_paths as $v) {
                    if ($v == '!' . $file) {
                        return true;
                    }
                }
            }
            else {
                foreach ($this->ignore_files as $v) {
                    if ($v == '!' . $file) {
                        return true;
                    }
                }
            }
        }

        return false;

    }

    private function getNode($dir, $entry) {
        $s = explode('/', $dir . $entry);
        $node = &$this->elements;
        
        foreach ($s as $k => $v) {
            if ($k == 0) {
                $v = '/';
            }
            
            if (!isset($node[$v])) {
                if ($v == $entry) {
                    $node[] = $v;
                    break;
                }
                else {
                    $node[$v] = array();
                }
            
            }
            
            $node = &$node[$v];
        }
        return $node;
    }

    private function lines($base, $path) {
        $prev = '';
        foreach ($path as $k => $v) {
            if (is_array($v)) {
                if ($k != '/') {
                    $prev .= $k . '/';
                }
                
                $this->lines($base . $prev, $v);
                $prev = '';
                
            }
            else {
                if (is_file(BASE_PATH . '/' . $base . $v)) {
                    $linecount = 0;
                    $handle = fopen(BASE_PATH . '/' . $base . $v, "r");
                    while (!feof($handle)){
                        $line = fgets($handle);
                        $linecount++;
                    }
                    
                    fclose($handle);
                    
                    $this->lines[$v] = $linecount;
                    
                    $file_explode = explode('.', $v);
                    $file_type = $file_explode[count($file_explode) -1 ];
                    if (!isset($this->lines_type[$file_type])) {
                        $this->lines_type[$file_type] = $linecount;
                    }
                    else {
                        $this->lines_type[$file_type] += $linecount;
                    }
                }
            }
        }
    }

}

?>