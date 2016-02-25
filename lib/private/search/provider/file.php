<?php

namespace OC\Search\Provider;
use OC\Files\Filesystem;

/**
 * Provide search results from the 'files' app
 */
class File extends \OCP\Search\Provider {

	/**
	 * Search for files and folders matching the given query
	 * @param string $query
	 * @return \OCP\Search\Result
	 */
	function search($query) {
		$files = Filesystem::search($query);
		$results = array();
		// edit results
		foreach ($files as $fileData) {
			// skip versions
			if (strpos($fileData['path'], '_versions') === 0) {
				continue;
			}
			// skip top-level folder
			if ($fileData['name'] === 'files' && $fileData['parent'] === -1) {
				continue;
			}
			// create audio result
			if($fileData['mimepart'] === 'audio'){
				$result = new \OC\Search\Result\Audio($fileData);
			}
			// create image result
			elseif($fileData['mimepart'] === 'image'){
				$result = new \OC\Search\Result\Image($fileData);
			}
			// create folder result
			elseif($fileData['mimetype'] === 'httpd/unix-directory'){
				$result = new \OC\Search\Result\Folder($fileData);
			}
			// or create file result
			else{
				$result = new \OC\Search\Result\File($fileData);
			}
			// add to results
			$results[] = $result;
		}
		// return
		return $results;
	}
	
}
