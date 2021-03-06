<?php

namespace OCP\AppFramework\Http;

use OCP\AppFramework\Http;

/**
 * Base class for responses. Also used to just send headers.
 *
 * It handles headers, HTTP status code, last modified and ETag.
 * @since 6.0.0
 */
class Response {

	/**
	 * Headers - defaults to ['Cache-Control' => 'no-cache, must-revalidate']
	 * @var array
	 */
	private $headers = array(
		'Cache-Control' => 'no-cache, must-revalidate'
	);


	/**
	 * Cookies that will be need to be constructed as header
	 * @var array
	 */
	private $cookies = array();


	/**
	 * HTTP status code - defaults to STATUS OK
	 * @var int
	 */
	private $status = Http::STATUS_OK;


	/**
	 * Last modified date
	 * @var \DateTime
	 */
	private $lastModified;


	/**
	 * ETag
	 * @var string
	 */
	private $ETag;

	/** @var ContentSecurityPolicy|null Used Content-Security-Policy */
	private $contentSecurityPolicy = null;


	/**
	 * Caches the response
	 * @param int $cacheSeconds the amount of seconds that should be cached
	 * if 0 then caching will be disabled
	 * @return $this
	 * @since 6.0.0 - return value was added in 7.0.0
	 */
	public function cacheFor($cacheSeconds) {

		if($cacheSeconds > 0) {
			$this->addHeader('Cache-Control', 'max-age=' . $cacheSeconds .
				', must-revalidate');
		} else {
			$this->addHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
		}

		return $this;
	}

	/**
	 * Adds a new cookie to the response
	 * @param string $name The name of the cookie
	 * @param string $value The value of the cookie
	 * @param \DateTime|null $expireDate Date on that the cookie should expire, if set
	 * 									to null cookie will be considered as session
	 * 									cookie.
	 * @return $this
	 * @since 8.0.0
	 */
	public function addCookie($name, $value, \DateTime $expireDate = null) {
		$this->cookies[$name] = array('value' => $value, 'expireDate' => $expireDate);
		return $this;
	}


	/**
	 * Set the specified cookies
	 * @param array $cookies array('foo' => array('value' => 'bar', 'expire' => null))
	 * @return $this
	 * @since 8.0.0
	 */
	public function setCookies(array $cookies) {
		$this->cookies = $cookies;
		return $this;
	}


	/**
	 * Invalidates the specified cookie
	 * @param string $name
	 * @return $this
	 * @since 8.0.0
	 */
	public function invalidateCookie($name) {
		$this->addCookie($name, 'expired', new \DateTime('1971-01-01 00:00'));
		return $this;
	}

	/**
	 * Invalidates the specified cookies
	 * @param array $cookieNames array('foo', 'bar')
	 * @return $this
	 * @since 8.0.0
	 */
	public function invalidateCookies(array $cookieNames) {
		foreach($cookieNames as $cookieName) {
			$this->invalidateCookie($cookieName);
		}
		return $this;
	}

	/**
	 * Returns the cookies
	 * @return array
	 * @since 8.0.0
	 */
	public function getCookies() {
		return $this->cookies;
	}

	/**
	 * Adds a new header to the response that will be called before the render
	 * function
	 * @param string $name The name of the HTTP header
	 * @param string $value The value, null will delete it
	 * @return $this
	 * @since 6.0.0 - return value was added in 7.0.0
	 */
	public function addHeader($name, $value) {
		$name = trim($name);  // always remove leading and trailing whitespace
		                      // to be able to reliably check for security
		                      // headers

		if(is_null($value)) {
			unset($this->headers[$name]);
		} else {
			$this->headers[$name] = $value;
		}

		return $this;
	}


	/**
	 * Set the headers
	 * @param array $headers value header pairs
	 * @return $this
	 * @since 8.0.0
	 */
	public function setHeaders(array $headers) {
		$this->headers = $headers;

		return $this;
	}


	/**
	 * Returns the set headers
	 * @return array the headers
	 * @since 6.0.0
	 */
	public function getHeaders() {
		$mergeWith = [];

		if($this->lastModified) {
			$mergeWith['Last-Modified'] =
				$this->lastModified->format(\DateTime::RFC2822);
		}

		// Build Content-Security-Policy and use default if none has been specified
		if(is_null($this->contentSecurityPolicy)) {
			$this->setContentSecurityPolicy(new ContentSecurityPolicy());
		}
		$this->headers['Content-Security-Policy'] = $this->contentSecurityPolicy->buildPolicy();

		if($this->ETag) {
			$mergeWith['ETag'] = '"' . $this->ETag . '"';
		}

		return array_merge($mergeWith, $this->headers);
	}


	/**
	 * By default renders no output
	 * @return null
	 * @since 6.0.0
	 */
	public function render() {
		return null;
	}


	/**
	 * Set response status
	 * @param int $status a HTTP status code, see also the STATUS constants
	 * @return Response Reference to this object
	 * @since 6.0.0 - return value was added in 7.0.0
	 */
	public function setStatus($status) {
		$this->status = $status;

		return $this;
	}

	/**
	 * Set a Content-Security-Policy
	 * @param ContentSecurityPolicy $csp Policy to set for the response object
	 * @return $this
	 * @since 8.1.0
	 */
	public function setContentSecurityPolicy(ContentSecurityPolicy $csp) {
		$this->contentSecurityPolicy = $csp;
		return $this;
	}

	/**
	 * Get the currently used Content-Security-Policy
	 * @return ContentSecurityPolicy|null Used Content-Security-Policy or null if
	 *                                    none specified.
	 * @since 8.1.0
	 */
	public function getContentSecurityPolicy() {
		return $this->contentSecurityPolicy;
	}


	/**
	 * Get response status
	 * @since 6.0.0
	 */
	public function getStatus() {
		return $this->status;
	}


	/**
	 * Get the ETag
	 * @return string the etag
	 * @since 6.0.0
	 */
	public function getETag() {
		return $this->ETag;
	}


	/**
	 * Get "last modified" date
	 * @return \DateTime RFC2822 formatted last modified date
	 * @since 6.0.0
	 */
	public function getLastModified() {
		return $this->lastModified;
	}


	/**
	 * Set the ETag
	 * @param string $ETag
	 * @return Response Reference to this object
	 * @since 6.0.0 - return value was added in 7.0.0
	 */
	public function setETag($ETag) {
		$this->ETag = $ETag;

		return $this;
	}


	/**
	 * Set "last modified" date
	 * @param \DateTime $lastModified
	 * @return Response Reference to this object
	 * @since 6.0.0 - return value was added in 7.0.0
	 */
	public function setLastModified($lastModified) {
		$this->lastModified = $lastModified;

		return $this;
	}


}
