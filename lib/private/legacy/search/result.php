<?php

/**
 * @deprecated use \OCP\Search\Result instead
 */
class OC_Search_Result extends \OCP\Search\Result {
	/**
	 * Create a new search result
	 * @param string $id unique identifier from application: '[app_name]/[item_identifier_in_app]'
	 * @param string $name displayed text of result
	 * @param string $link URL to the result within its app
	 * @param string $type @deprecated because it is now set in \OC\Search\Result descendants
	 */
	public function __construct($id = null, $name = null, $link = null, $type = null) {
		$this->id = $id;
		$this->name = $name;
		$this->link = $link;
		$this->type = $type;
	}
}
