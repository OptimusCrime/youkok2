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

	private $base;
	private $currentChecking;
	private $currentContent;
	
	//
	// The constructor
	//

	public function __construct($base) {
		// Store base path
		$this->base = $base;

		// Set current to all nulls
		$this->currentChecking = null;
		$this->currentContent = null;
	}

	//
	// Check if Item is cached or not
	//

	public function isCached($id) {
		// Generate full path for item
		$file = $this->getFileName($id);
		
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

	public function getCache($id) {
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

	public function setCache($id, $s) {
		// Get file name
		$file = $this->getFileName($id);
		
		// Build content
		$data = '<?php return array(' . $s . '); ?>';

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

	private function getFileName($id) {
		$hash = substr(md5('lorem ipsum' . $id . md5($id)), 0, 22);
		return $this->base . '/cache/elements/' . $hash . '_' . $id . '_c.php';
	}
}
?>