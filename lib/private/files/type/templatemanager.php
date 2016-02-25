<?php

namespace OC\Files\Type;

class TemplateManager {
	protected $templates = array();

	public function registerTemplate($mimetype, $path) {
		$this->templates[$mimetype] = $path;
	}

	/**
	 * get the path of the template for a mimetype
	 *
	 * @param string $mimetype
	 * @return string|null
	 */
	public function getTemplatePath($mimetype) {
		if (isset($this->templates[$mimetype])) {
			return $this->templates[$mimetype];
		} else {
			return null;
		}
	}

	/**
	 * get the template content for a mimetype
	 *
	 * @param string $mimetype
	 * @return string
	 */
	public function getTemplate($mimetype) {
		$path = $this->getTemplatePath($mimetype);
		if ($path) {
			return file_get_contents($path);
		} else {
			return '';
		}
	}
}
