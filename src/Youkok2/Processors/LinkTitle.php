<?php
/*
 * File: LinkTitle.php
 * Holds: Tries to fetch title for a url
 * Created: 25.02.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Processors;

class LinkTitle extends BaseProcessor {
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Load data
     */
    
    public function run() {
        parent::run();
        
        if (isset($_POST['url'])) {
            // Trim away
            $_POST['url'] = rtrim(trim($_POST['url']));
        
            // Check for valid url
             if (filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
                // Valid url, fetch content of page
                try {
                    $site_content = @file_get_contents($_POST['url']);
                    
                    // Check if anything was returned
                    if ($site_content !== null and strlen($site_content) > 0) {
                        // Try to match the title
                        preg_match("/\<title\>(.*)\<\/title\>/", $site_content, $title);
                        
                        // Check if any title was found
                        if (count($title) > 0 and strlen($title[1]) > 0) {
                            // Title was found
                            $this->setData('title', $title[1]);
                            $this->setData('code', 200);
                        }
                        else {
                            // Title was not found
                            $this->setData('code', 400);
                        }
                    }
                    else {
                        // Did not get any response
                        $this->setData('code', 400);
                    }
                }
                catch (\Exception $e) {
                    // The fuck
                    $this->setError();
                }
            }
            else {
                // Return error
                $this->setError();
            }
        }
        else {
            // Return error
            $this->setError();
        }
        
        // Handle the output
        $this->handleOutput();
    }
}