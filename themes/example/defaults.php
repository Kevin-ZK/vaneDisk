<?php

class OC_Theme {

	private $themeEntity;
	private $themeName;
	private $themeTitle;
	private $themeBaseUrl;
	private $themeDocBaseUrl;
	private $themeSyncClientUrl;
	private $themeSlogan;
	private $themeMailHeaderColor;

	/* put your custom text in these variables */
	function __construct() {
		$this->themeEntity = 'Custom Cloud Co.';
		$this->themeName = 'Custom Cloud';
		$this->themeTitle = 'Custom Cloud';
		$this->themeBaseUrl = 'https://owncloud.org';
		$this->themeDocBaseUrl = 'https://doc.owncloud.org';
		$this->themeSyncClientUrl = 'https://owncloud.org/install';
		$this->themeSlogan = 'Your custom cloud, personalized for you!';
		$this->themeMailHeaderColor = '#745bca';
	}
	/* nothing after this needs to be adjusted */

	public function getBaseUrl() {
		return $this->themeBaseUrl;
	}

	public function getSyncClientUrl() {
		return $this->themeSyncClientUrl;
	}

	public function getDocBaseUrl() {
		return $this->themeDocBaseUrl;
	}

	public function getTitle() {
		return $this->themeTitle;
	}

	public function getName() {
		return $this->themeName;
	}

	public function getEntity() {
		return $this->themeEntity;
	}

	public function getSlogan() {
		return $this->themeSlogan;
	}

	public function getShortFooter() {
		$footer = '© 2015 <a href="'.$this->getBaseUrl().'" target="_blank\">'.$this->getEntity().'</a>'.
			'<br/>' . $this->getSlogan();

		return $footer;
	}

	public function getLongFooter() {
		$footer = '© 2015 <a href="'.$this->getBaseUrl().'" target="_blank\">'.$this->getEntity().'</a>'.
			'<br/>' . $this->getSlogan();

		return $footer;
	}

	public function buildDocLinkToKey($key) {
		return $this->getDocBaseUrl() . '/server/8.0/go.php?to=' . $key;
	}

	public function getMailHeaderColor() {
		return $this->themeMailHeaderColor;
	}

}
