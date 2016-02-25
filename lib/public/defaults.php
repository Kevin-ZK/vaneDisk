<?php
namespace OCP;

/**
 * public api to access default strings and urls for your templates
 * @since 6.0.0
 */
class Defaults {

	/**
	 * \OC_Defaults instance to retrieve the defaults
	 * @return string
	 * @since 6.0.0
	 */
	private $defaults;

	/**
	 * creates a \OC_Defaults instance which is used in all methods to retrieve the
	 * actual defaults
	 * @since 6.0.0
	 */
	function __construct() {
		$this->defaults = new \OC_Defaults();
	}

	/**
	 * get base URL for the organisation behind your vanedisk instance
	 * @return string
	 * @since 6.0.0
	 */
	public function getBaseUrl() {
		return $this->defaults->getBaseUrl();
	}

	/**
	 * link to the desktop sync client
	 * @return string
	 * @since 6.0.0
	 */
	public function getSyncClientUrl() {
		return $this->defaults->getSyncClientUrl();
	}

	/**
	 * link to the iOS client
	 * @return string
	 * @since 8.0.0
	 */
	public function getiOSClientUrl() {
		return $this->defaults->getiOSClientUrl();
	}

	/**
	 * link to the Android client
	 * @return string
	 * @since 8.0.0
	 */
	public function getAndroidClientUrl() {
		return $this->defaults->getAndroidClientUrl();
	}

	/**
	 * base URL to the documentation of your vaneDisk instance
	 * @return string
	 * @since 6.0.0
	 */
	public function getDocBaseUrl() {
		return $this->defaults->getDocBaseUrl();
	}

	/**
	 * name of your vanedisk instance
	 * @return string
	 * @since 6.0.0
	 */
	public function getName() {
		return $this->defaults->getName();
	}

	/**
	 * name of your vanedisk instance containing HTML styles
	 * @return string
	 * @since 8.0.0
	 */
	public function getHTMLName() {
		return $this->defaults->getHTMLName();
	}

	/**
	 * Entity behind your onwCloud instance
	 * @return string
	 * @since 6.0.0
	 */
	public function getEntity() {
		return $this->defaults->getEntity();
	}

	/**
	 * vanedisk slogan
	 * @return string
	 * @since 6.0.0
	 */
	public function getSlogan() {
		return $this->defaults->getSlogan();
	}

	/**
	 * logo claim
	 * @return string
	 * @since 6.0.0
	 */
	public function getLogoClaim() {
		return $this->defaults->getLogoClaim();
	}

	/**
	 * footer, short version
	 * @return string
	 * @since 6.0.0
	 */
	public function getShortFooter() {
		return $this->defaults->getShortFooter();
	}

	/**
	 * footer, long version
	 * @return string
	 * @since 6.0.0
	 */
	public function getLongFooter() {
		return $this->defaults->getLongFooter();
	}

	/**
	 * Returns the AppId for the App Store for the iOS Client
	 * @return string AppId
	 * @since 8.0.0
	 */
	public function getiTunesAppId() {
		return $this->defaults->getiTunesAppId();
	}
}
