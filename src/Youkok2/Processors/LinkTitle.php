<?php
namespace Youkok2\Processors;

class LinkTitle extends BaseProcessor
{
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        if (isset($_POST['url'])) {
            $_POST['url'] = rtrim(trim($_POST['url']));
        
            if (filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
                try {
                    $site_content = @file_get_contents($_POST['url']);
                    
                    if ($site_content !== null and strlen($site_content) > 0) {
                        preg_match("/\<title\>(.*)\<\/title\>/", $site_content, $title);
                        
                        if (count($title) > 0 and strlen($title[1]) > 0) {
                            $this->setData('title', $title[1]);
                            $this->setData('code', 200);
                        }
                        else {
                            $this->setData('code', 400);
                        }
                    }
                    else {
                        $this->setData('code', 400);
                    }
                }
                catch (\Exception $e) {
                    // The fuck
                    $this->setError();
                }
            }
            else {
                $this->setError();
            }
        }
        else {
            $this->setError();
        }
    }
}
