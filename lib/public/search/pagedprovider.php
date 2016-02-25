<?php

namespace OCP\Search;

/**
 * Provides a template for search functionality throughout vanedisk;
 * @since 8.0.0
 */
abstract class PagedProvider extends Provider {

	/**
	 * show all results
	 * @since 8.0.0
	 */
	const SIZE_ALL = 0;

	/**
	 * Constructor
	 * @param array $options
	 * @since 8.0.0
	 */
	public function __construct($options) {
		$this->options = $options;
	}

	/**
	 * Search for $query
	 * @param string $query
	 * @return array An array of OCP\Search\Result's
	 * @since 8.0.0
	 */
	public function search($query) {
		// old apps might assume they get all results, so we use SIZE_ALL
		$this->searchPaged($query, 1, self::SIZE_ALL);
	}

	/**
	 * Search for $query
	 * @param string $query
	 * @param int $page pages start at page 1
	 * @param int $size, 0 = SIZE_ALL
	 * @return array An array of OCP\Search\Result's
	 * @since 8.0.0
	 */
	abstract public function searchPaged($query, $page, $size);
}
