<?php
/*
 * File: cachemanager.php
 * Holds: Manges cache of Item objects
 * Created: 13.06.14
 * Project: Youkok2
 * 
*/

Class CacheManager {

	//
	// Some variables
	//

	private $controller;

	private $currentChecking;
	private $currentContent;
	
	//
	// The constructor
	//

	public function __construct($controller) {
		// Store base path
		$this->controller = &$controller;

		// Set current to all nulls
		$this->currentChecking = null;
		$this->currentContent = null;
	}

	//
	// Check if Item is cached or not
	//

	public function isCached($id, $type) {
		// Generate full path for item
		$file = $this->getFileName($id, $type);
		
		// Check if file exists
		if (file_exists($file)) {
			// Get content
			$temp_content = file_get_contents($file);

			// Check if content is valid (and safe!)
			if (substr(file_get_contents($file), 0, 19) == '<?php return array(') {
				// Is valid, store current
				$this->currentChecking = $id;
				$this->currentContent = $temp_content;

				// Return true
				return true;
			}
			else {
				// TODO delete cache
			}
		}
		else {
			// Reset current
			$this->currentChecking = null;
			$this->currentContent = null;

			// Return status
			return false;
		}
	}

	//
	// Return cache
	//

	public function getCache($id, $type) {
		// Check if already validated
		if ($this->currentChecking == $id) {
			return $this->evalAndClean($this->currentContent);
		}
		else {
			// Validate first
			if ($this->isCached($id)) {
				// Is valid
				return $this->evalAndClean($this->currentContent);
			}
			else {
				// Return null, this is not a valid cache
				return null;
			}
		}
	}

	//
	// Set cache
	//

	public function setCache($id, $type, $s) {
		// Get file name
		$file = $this->getFileName($id, $type);
		
		// Build content
		$data = '<?php return array(' . $s . '); ?>';

		// Check if directory exists
		$hash = $this->getHash($id, $type);
		$parent_dir = BASE_PATH . '/cache/elements/' . substr($hash, 0, 1);
		if (!file_exists($parent_dir)) {
			mkdir($parent_dir);
		}

		// Store content in file
		file_put_contents($file, $data);
	}

	//
	// Private method for evaling and removing php-tags from the file
	//

	private function evalAndClean($c) {
		return eval(str_replace(array('<?php', '?>'), '', $c));
	}

	//
	// Private method for generating hashes used by the cache
	//

	private function getFileName($id, $type) {
		$hash = $this->getHash($id, $type);
		return BASE_PATH . '/cache/elements/' . substr($hash, 0, 1) . '/' . $hash . '_' . $type . '_' . $id . '_c.php';
	}

	//
	// Private method that returns that hash
	//

	private function getHash($id, $type) {
		return $hash = substr(md5('lorem ' . $type . ' ipsum' . $id . md5($id)), 0, 22);
	}
}
?>