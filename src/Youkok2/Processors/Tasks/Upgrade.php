<?php
namespace Youkok2\Processors\Tasks;

use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\LineCounter;

class Upgrade extends BaseProcessor
{
    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }

    protected function requireDatabase() {
        return true;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        $this->buildJS();
        $this->buildCSS();
    }
    
    private function buildJS() {
        $minifier = new \MatthiasMullie\Minify\JS();
        
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(BASE_PATH . '/assets/js/youkok/', \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        
        foreach ($iter as $file) {
            if (!$file->isDir()) {
                $minifier->add($file->getPathname());
            }
        }
        
        $minifier->minify(BASE_PATH . '/assets/js/youkok.min.temp.js');
        
        $js_content = '';
        
        $libs = [
            'jquery-2.1.4.min.js',
            'jquery-ui-1.11.4.min.js',
            'jquery.fileupload.js',
            'jquery.ba-outside-events.min.js',
            'jquery.countdown.min.js',
            'typeahead.bundle.min.js',
            'moment-2.10.3.min.js',
            'underscore-1.8.3.min.js',
            'bootstrap-3.3.5.min.js',
        ];
        
        foreach ($libs as $lib) {
            $js_content .= file_get_contents(BASE_PATH . '/assets/js/libs/' . $lib) . PHP_EOL;
        }
        
        $handle = fopen(BASE_PATH . '/assets/js/youkok.min.temp.js', 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $js_content .= $line;
            }
        }
        fclose($handle);
        
        file_put_contents(BASE_PATH . '/assets/js/youkok.min.js', $js_content);
        
        $this->setData('js', 'Successfully build JS files');
    }
    
    private function buildCSS() {
        $minifier = new \MatthiasMullie\Minify\CSS();
        
        $minifier->add(BASE_PATH . '/assets/css/libs/bootstrap.lumen.min.css');
        $minifier->add(BASE_PATH . '/assets/css/youkok.css');
        
        $minifier->minify(BASE_PATH . '/assets/css/youkok.min.css');
        
        $this->setData('css', 'Successfully build CSS files');
    }
}
