<?php
namespace Youkok2\Processors\Tasks;

use Youkok2\Processors\BaseProcessor;

class ClearCache extends BaseProcessor
{

    protected function checkPermissions() {
        var_dump($this->requireForce());
        return $this->requireCli() or $this->requireForce() or $this->requireAdmin();
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        if (file_exists(CACHE_PATH . '/courses.json')) {
            copy(CACHE_PATH . '/courses.json', BASE_PATH . '/courses.json');
        }
        if (file_exists(CACHE_PATH . '/typeahead.json')) {
            copy(CACHE_PATH . '/typeahead.json', BASE_PATH . '/typeahead.json');
        }

        $smarty = new \Smarty();
        $smarty->setCompileDir(CACHE_PATH . '/smarty/compiled/');
        $smarty->setCacheDir(CACHE_PATH . '/smarty/cache/');

        $smarty->clearAllCache();
        $smarty->clearCompiledTemplate();
        
        if (file_exists(BASE_PATH . '/courses.json')) {
            copy(BASE_PATH . '/courses.json', CACHE_PATH . '/courses.json');
            unlink(BASE_PATH . '/courses.json');
        }
        if (file_exists(BASE_PATH . '/typeahead.json')) {
            copy(BASE_PATH . '/typeahead.json', CACHE_PATH . '/typeahead.json');
            unlink(BASE_PATH . '/typeahead.json');
        }
        
        $partitions_ignore = explode(',', CLEAR_CACHE_IGNORE_PARTITIONS);
        foreach (array_filter(glob(CACHE_PATH . '/youkok/*'), 'is_dir') as $v) {
            $v_split = explode('/', $v);
            $cache_identifier = $v_split[count($v_split) - 1];
            
            if (!in_array($cache_identifier, $partitions_ignore)) {
                $this->rmNonemptyDir($v);
            }
        }

        $this->setData('code', 200);
        $this->setData('msg', 'Cache cleared');
    }
    
    private function rmNonemptyDir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir . '/' . $object) == 'dir') {
                        $this->rmNonemptyDir($dir . '/' . $object);
                    }
                    else {
                        unlink($dir . '/' . $object);
                    }
                }
            }
            
            reset($objects);
            rmdir($dir);
        }
    }
}
