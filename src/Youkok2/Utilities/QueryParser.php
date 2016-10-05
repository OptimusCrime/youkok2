<?php

namespace Youkok2\Utilities;

class QueryParser
{

    private $youkok;
    private $path;
    
    public function __construct($youkok) {
        $this->youkok = $youkok;

        $this->getRequestQuery();
    }
    
    private function getRequestQuery() {
        $request_path = $this->getRequest();

        // We have a path, find the base-path to include the correct script
        if ($request_path == null or $request_path == '' or $request_path == '/') {
            $this->path = '/';
        }
        elseif (strpos($request_path, '/') !== false) {
            $path_split = explode('/', $request_path);

            // Clean the path
            $path_clean = [];
            foreach ($path_split as $path_split_seq) {
                $path_split_seq_clean = str_replace(['/', ' '], '', $path_split_seq);
                if (strlen($path_split_seq_clean) > 0) {
                    $path_clean[] = $path_split_seq_clean;
                }
            }

            if (count($path_clean) > 1) {
                $this->path = '/' . implode('/', $path_clean);
            }
            elseif (count($path_clean) == 1) {
                $this->path = '/' . $path_clean[0];
            }
            else {
                $this->path = '/';
            }
        }
        else {
            $this->path = '/' . $request_path;
        }
    }

    private function getRequest() {
        // Check if we are running built in server or apache/nginx
        if ($this->youkok->getServer('SERVER_SOFTWARE') !== null and
            strpos($this->youkok->getServer('SERVER_SOFTWARE'), 'Development Server') !== false) {
            $request_url = $this->youkok->getServer('REQUEST_URI');

            // Check if request uri has additional information (? params)
            if (strpos($request_url, '?') !== false) {
                $request_url = explode('?', $request_url)[0];
            }

            // PHP built in server
            return substr($request_url, 1);
        }
        else {
            // Apache/nginx/etc
            return (($this->youkok->getGet('q') !== null) ? $this->youkok->getGet('q') : '/');
        }
    }
    
    public function getPath() {
        return $this->path;
    }
}
