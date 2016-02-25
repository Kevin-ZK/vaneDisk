<?php

namespace OC\AppFramework\Http;

use OC\Security\TrustedDomainHelper;
use OCP\IConfig;
use OCP\IRequest;
use OCP\Security\ISecureRandom;

/**
 * Class for accessing variables in the request.
 * This class provides an immutable object with request variables.
 */
class Request implements \ArrayAccess, \Countable, IRequest {

	const USER_AGENT_IE = '/MSIE/';
	// Android Chrome user agent: https://developers.google.com/chrome/mobile/docs/user-agent
	const USER_AGENT_ANDROID_MOBILE_CHROME = '#Android.*Chrome/[.0-9]*#';
	const USER_AGENT_FREEBOX = '#^Mozilla/5\.0$#';
	const REGEX_LOCALHOST = '/^(127\.0\.0\.1|localhost)$/';

	protected $inputStream;
	protected $content;
	protected $items = array();
	protected $allowedKeys = array(
		'get',
		'post',
		'files',
		'server',
		'env',
		'cookies',
		'urlParams',
		'parameters',
		'method',
		'requesttoken',
	);
	/** @var ISecureRandom */
	protected $secureRandom;
	/** @var IConfig */
	protected $config;
	/** @var string */
	protected $requestId = '';

	/**
	 * @param array $vars An associative array with the following optional values:
	 *        - array 'urlParams' the parameters which were matched from the URL
	 *        - array 'get' the $_GET array
	 *        - array|string 'post' the $_POST array or JSON string
	 *        - array 'files' the $_FILES array
	 *        - array 'server' the $_SERVER array
	 *        - array 'env' the $_ENV array
	 *        - array 'cookies' the $_COOKIE array
	 *        - string 'method' the request method (GET, POST etc)
	 *        - string|false 'requesttoken' the requesttoken or false when not available
	 * @param ISecureRandom $secureRandom
	 * @param IConfig $config
	 * @param string $stream
	 * @see http://www.php.net/manual/en/reserved.variables.php
	 */
	public function __construct(array $vars=array(),
								ISecureRandom $secureRandom = null,
								IConfig $config,
								$stream='php://input') {
		$this->inputStream = $stream;
		$this->items['params'] = array();
		$this->secureRandom = $secureRandom;
		$this->config = $config;

		if(!array_key_exists('method', $vars)) {
			$vars['method'] = 'GET';
		}

		foreach($this->allowedKeys as $name) {
			$this->items[$name] = isset($vars[$name])
				? $vars[$name]
				: array();
		}

		// 'application/json' must be decoded manually.
		if (strpos($this->getHeader('Content-Type'), 'application/json') !== false) {
			$params = json_decode(file_get_contents($this->inputStream), true);
			if(count($params) > 0) {
				$this->items['params'] = $params;
				if($vars['method'] === 'POST') {
					$this->items['post'] = $params;
				}
			}
		// Handle application/x-www-form-urlencoded for methods other than GET
		// or post correctly
		} elseif($vars['method'] !== 'GET'
				&& $vars['method'] !== 'POST'
				&& strpos($this->getHeader('Content-Type'), 'application/x-www-form-urlencoded') !== false) {

			parse_str(file_get_contents($this->inputStream), $params);
			if(is_array($params)) {
				$this->items['params'] = $params;
			}
		}

		$this->items['parameters'] = array_merge(
			$this->items['get'],
			$this->items['post'],
			$this->items['urlParams'],
			$this->items['params']
		);

	}
	/**
	 * @param array $parameters
	 */
	public function setUrlParameters(array $parameters) {
		$this->items['urlParams'] = $parameters;
		$this->items['parameters'] = array_merge(
			$this->items['parameters'],
			$this->items['urlParams']
		);
	}

	/**
	 * Countable method
	 * @return int
	 */
	public function count() {
		return count(array_keys($this->items['parameters']));
	}

	/**
	* ArrayAccess methods
	*
	* Gives access to the combined GET, POST and urlParams arrays
	*
	* Examples:
	*
	* $var = $request['myvar'];
	*
	* or
	*
	* if(!isset($request['myvar']) {
	* 	// Do something
	* }
	*
	* $request['myvar'] = 'something'; // This throws an exception.
	*
	* @param string $offset The key to lookup
	* @return boolean
	*/
	public function offsetExists($offset) {
		return isset($this->items['parameters'][$offset]);
	}

	/**
	* @see offsetExists
	*/
	public function offsetGet($offset) {
		return isset($this->items['parameters'][$offset])
			? $this->items['parameters'][$offset]
			: null;
	}

	/**
	* @see offsetExists
	*/
	public function offsetSet($offset, $value) {
		throw new \RuntimeException('You cannot change the contents of the request object');
	}

	/**
	* @see offsetExists
	*/
	public function offsetUnset($offset) {
		throw new \RuntimeException('You cannot change the contents of the request object');
	}

	/**
	 * Magic property accessors
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		throw new \RuntimeException('You cannot change the contents of the request object');
	}

	/**
	* Access request variables by method and name.
	* Examples:
	*
	* $request->post['myvar']; // Only look for POST variables
	* $request->myvar; or $request->{'myvar'}; or $request->{$myvar}
	* Looks in the combined GET, POST and urlParams array.
	*
	* If you access e.g. ->post but the current HTTP request method
	* is GET a \LogicException will be thrown.
	*
	* @param string $name The key to look for.
	* @throws \LogicException
	* @return mixed|null
	*/
	public function __get($name) {
		switch($name) {
			case 'put':
			case 'patch':
			case 'get':
			case 'post':
				if($this->method !== strtoupper($name)) {
					throw new \LogicException(sprintf('%s cannot be accessed in a %s request.', $name, $this->method));
				}
			case 'files':
			case 'server':
			case 'env':
			case 'cookies':
			case 'parameters':
			case 'params':
			case 'urlParams':
				if(in_array($name, array('put', 'patch'))) {
					return $this->getContent();
				} else {
					return isset($this->items[$name])
						? $this->items[$name]
						: null;
				}
				break;
			case 'method':
				return $this->items['method'];
				break;
			default;
				return isset($this[$name])
					? $this[$name]
					: null;
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name) {
		return isset($this->items['parameters'][$name]);
	}

	/**
	 * @param string $id
	 */
	public function __unset($id) {
		throw new \RunTimeException('You cannot change the contents of the request object');
	}

	/**
	 * Returns the value for a specific http header.
	 *
	 * This method returns null if the header did not exist.
	 *
	 * @param string $name
	 * @return string
	 */
	public function getHeader($name) {

		$name = strtoupper(str_replace(array('-'),array('_'),$name));
		if (isset($this->server['HTTP_' . $name])) {
			return $this->server['HTTP_' . $name];
		}

		// There's a few headers that seem to end up in the top-level
		// server array.
		switch($name) {
			case 'CONTENT_TYPE' :
			case 'CONTENT_LENGTH' :
				if (isset($this->server[$name])) {
					return $this->server[$name];
				}
				break;

		}

		return null;
	}

	/**
	 * Lets you access post and get parameters by the index
	 * In case of json requests the encoded json body is accessed
	 *
	 * @param string $key the key which you want to access in the URL Parameter
	 *                     placeholder, $_POST or $_GET array.
	 *                     The priority how they're returned is the following:
	 *                     1. URL parameters
	 *                     2. POST parameters
	 *                     3. GET parameters
	 * @param mixed $default If the key is not found, this value will be returned
	 * @return mixed the content of the array
	 */
	public function getParam($key, $default = null) {
		return isset($this->parameters[$key])
			? $this->parameters[$key]
			: $default;
	}

	/**
	 * Returns all params that were received, be it from the request
	 * (as GET or POST) or throuh the URL by the route
	 * @return array the array with all parameters
	 */
	public function getParams() {
		return $this->parameters;
	}

	/**
	 * Returns the method of the request
	 * @return string the method of the request (POST, GET, etc)
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Shortcut for accessing an uploaded file through the $_FILES array
	 * @param string $key the key that will be taken from the $_FILES array
	 * @return array the file in the $_FILES element
	 */
	public function getUploadedFile($key) {
		return isset($this->files[$key]) ? $this->files[$key] : null;
	}

	/**
	 * Shortcut for getting env variables
	 * @param string $key the key that will be taken from the $_ENV array
	 * @return array the value in the $_ENV element
	 */
	public function getEnv($key) {
		return isset($this->env[$key]) ? $this->env[$key] : null;
	}

	/**
	 * Shortcut for getting cookie variables
	 * @param string $key the key that will be taken from the $_COOKIE array
	 * @return array the value in the $_COOKIE element
	 */
	public function getCookie($key) {
		return isset($this->cookies[$key]) ? $this->cookies[$key] : null;
	}

	/**
	 * Returns the request body content.
	 *
	 * If the HTTP request method is PUT and the body
	 * not application/x-www-form-urlencoded or application/json a stream
	 * resource is returned, otherwise an array.
	 *
	 * @return array|string|resource The request body content or a resource to read the body stream.
	 *
	 * @throws \LogicException
	 */
	protected function getContent() {
		// If the content can't be parsed into an array then return a stream resource.
		if ($this->method === 'PUT'
			&& strpos($this->getHeader('Content-Type'), 'application/x-www-form-urlencoded') === false
			&& strpos($this->getHeader('Content-Type'), 'application/json') === false
		) {
			if ($this->content === false) {
				throw new \LogicException(
					'"put" can only be accessed once if not '
					. 'application/x-www-form-urlencoded or application/json.'
				);
			}
			$this->content = false;
			return fopen($this->inputStream, 'rb');
		} else {
			return $this->parameters;
		}
	}

	/**
	 * Checks if the CSRF check was correct
	 * @return bool true if CSRF check passed
	 * @see OC_Util::callRegister()
	 */
	public function passesCSRFCheck() {
		if($this->items['requesttoken'] === false) {
			return false;
		}

		if (isset($this->items['get']['requesttoken'])) {
			$token = $this->items['get']['requesttoken'];
		} elseif (isset($this->items['post']['requesttoken'])) {
			$token = $this->items['post']['requesttoken'];
		} elseif (isset($this->items['server']['HTTP_REQUESTTOKEN'])) {
			$token = $this->items['server']['HTTP_REQUESTTOKEN'];
		} else {
			//no token found.
			return false;
		}

		// Check if the token is valid
		if($token !== $this->items['requesttoken']) {
			// Not valid
			return false;
		} else {
			// Valid token
			return true;
		}
	}

	/**
	 * Returns an ID for the request, value is not guaranteed to be unique and is mostly meant for logging
	 * If `mod_unique_id` is installed this value will be taken.
	 * @return string
	 */
	public function getId() {
		if(isset($this->server['UNIQUE_ID'])) {
			return $this->server['UNIQUE_ID'];
		}

		if(empty($this->requestId)) {
			$this->requestId = $this->secureRandom->getLowStrengthGenerator()->generate(20);
		}

		return $this->requestId;
	}

	/**
	 * Returns the remote address, if the connection came from a trusted proxy
	 * and `forwarded_for_headers` has been configured then the IP address
	 * specified in this header will be returned instead.
	 * Do always use this instead of $_SERVER['REMOTE_ADDR']
	 * @return string IP address
	 */
	public function getRemoteAddress() {
		$remoteAddress = isset($this->server['REMOTE_ADDR']) ? $this->server['REMOTE_ADDR'] : '';
		$trustedProxies = $this->config->getSystemValue('trusted_proxies', []);

		if(is_array($trustedProxies) && in_array($remoteAddress, $trustedProxies)) {
			$forwardedForHeaders = $this->config->getSystemValue('forwarded_for_headers', []);

			foreach($forwardedForHeaders as $header) {
				if(isset($this->server[$header])) {
					foreach(explode(',', $this->server[$header]) as $IP) {
						$IP = trim($IP);
						if (filter_var($IP, FILTER_VALIDATE_IP) !== false) {
							return $IP;
						}
					}
				}
			}
		}

		return $remoteAddress;
	}

	/**
	 * Check overwrite condition
	 * @param string $type
	 * @return bool
	 */
	private function isOverwriteCondition($type = '') {
		$regex = '/' . $this->config->getSystemValue('overwritecondaddr', '')  . '/';
		$remoteAddr = isset($this->server['REMOTE_ADDR']) ? $this->server['REMOTE_ADDR'] : '';
		return $regex === '//' || preg_match($regex, $remoteAddr) === 1
		|| $type !== 'protocol';
	}

	/**
	 * Returns the server protocol. It respects one or more reverse proxies servers
	 * and load balancers
	 * @return string Server protocol (http or https)
	 */
	public function getServerProtocol() {
		if($this->config->getSystemValue('overwriteprotocol') !== ''
			&& $this->isOverwriteCondition('protocol')) {
			return $this->config->getSystemValue('overwriteprotocol');
		}

		if (isset($this->server['HTTP_X_FORWARDED_PROTO'])) {
			if (strpos($this->server['HTTP_X_FORWARDED_PROTO'], ',') !== false) {
				$parts = explode(',', $this->server['HTTP_X_FORWARDED_PROTO']);
				$proto = strtolower(trim($parts[0]));
			} else {
				$proto = strtolower($this->server['HTTP_X_FORWARDED_PROTO']);
			}

			// Verify that the protocol is always HTTP or HTTPS
			// default to http if an invalid value is provided
			return $proto === 'https' ? 'https' : 'http';
		}

		if (isset($this->server['HTTPS'])
			&& $this->server['HTTPS'] !== null
			&& $this->server['HTTPS'] !== 'off') {
			return 'https';
		}

		return 'http';
	}

	/**
	 * Returns the request uri, even if the website uses one or more
	 * reverse proxies
	 * @return string
	 */
	public function getRequestUri() {
		$uri = isset($this->server['REQUEST_URI']) ? $this->server['REQUEST_URI'] : '';
		if($this->config->getSystemValue('overwritewebroot') !== '' && $this->isOverwriteCondition()) {
			$uri = $this->getScriptName() . substr($uri, strlen($this->server['SCRIPT_NAME']));
		}
		return $uri;
	}

	/**
	 * Get raw PathInfo from request (not urldecoded)
	 * @throws \Exception
	 * @return string Path info
	 */
	public function getRawPathInfo() {
		$requestUri = isset($this->server['REQUEST_URI']) ? $this->server['REQUEST_URI'] : '';
		// remove too many leading slashes - can be caused by reverse proxy configuration
		if (strpos($requestUri, '/') === 0) {
			$requestUri = '/' . ltrim($requestUri, '/');
		}

		$requestUri = preg_replace('%/{2,}%', '/', $requestUri);

		// Remove the query string from REQUEST_URI
		if ($pos = strpos($requestUri, '?')) {
			$requestUri = substr($requestUri, 0, $pos);
		}

		$scriptName = $this->server['SCRIPT_NAME'];
		$pathInfo = $requestUri;

		// strip off the script name's dir and file name
		// FIXME: Sabre does not really belong here
		list($path, $name) = \Sabre\HTTP\URLUtil::splitPath($scriptName);
		if (!empty($path)) {
			if($path === $pathInfo || strpos($pathInfo, $path.'/') === 0) {
				$pathInfo = substr($pathInfo, strlen($path));
			} else {
				throw new \Exception("The requested uri($requestUri) cannot be processed by the script '$scriptName')");
			}
		}
		if (strpos($pathInfo, '/'.$name) === 0) {
			$pathInfo = substr($pathInfo, strlen($name) + 1);
		}
		if (strpos($pathInfo, $name) === 0) {
			$pathInfo = substr($pathInfo, strlen($name));
		}
		if($pathInfo === '/'){
			return '';
		} else {
			return $pathInfo;
		}
	}

	/**
	 * Get PathInfo from request
	 * @throws \Exception
	 * @return string|false Path info or false when not found
	 */
	public function getPathInfo() {
		if(isset($this->server['PATH_INFO'])) {
			return $this->server['PATH_INFO'];
		}

		$pathInfo = $this->getRawPathInfo();
		// following is taken from \Sabre\HTTP\URLUtil::decodePathSegment
		$pathInfo = rawurldecode($pathInfo);
		$encoding = mb_detect_encoding($pathInfo, ['UTF-8', 'ISO-8859-1']);

		switch($encoding) {
			case 'ISO-8859-1' :
				$pathInfo = utf8_encode($pathInfo);
		}
		// end copy

		return $pathInfo;
	}

	/**
	 * Returns the script name, even if the website uses one or more
	 * reverse proxies
	 * @return string the script name
	 */
	public function getScriptName() {
		$name = $this->server['SCRIPT_NAME'];
		$overwriteWebRoot =  $this->config->getSystemValue('overwritewebroot');
		if ($overwriteWebRoot !== '' && $this->isOverwriteCondition()) {
			// FIXME: This code is untestable due to __DIR__, also that hardcoded path is really dangerous
			$serverRoot = str_replace('\\', '/', substr(__DIR__, 0, -strlen('lib/private/appframework/http/')));
			$suburi = str_replace('\\', '/', substr(realpath($this->server['SCRIPT_FILENAME']), strlen($serverRoot)));
			$name = '/' . ltrim($overwriteWebRoot . $suburi, '/');
		}
		return $name;
	}

	/**
	 * Checks whether the user agent matches a given regex
	 * @param array $agent array of agent names
	 * @return bool true if at least one of the given agent matches, false otherwise
	 */
	public function isUserAgent(array $agent) {
		foreach ($agent as $regex) {
			if (preg_match($regex, $this->server['HTTP_USER_AGENT'])) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns the unverified server host from the headers without checking
	 * whether it is a trusted domain
	 * @return string Server host
	 */
	public function getInsecureServerHost() {
		$host = 'localhost';
		if (isset($this->server['HTTP_X_FORWARDED_HOST'])) {
			if (strpos($this->server['HTTP_X_FORWARDED_HOST'], ',') !== false) {
				$parts = explode(',', $this->server['HTTP_X_FORWARDED_HOST']);
				$host = trim(current($parts));
			} else {
				$host = $this->server['HTTP_X_FORWARDED_HOST'];
			}
		} else {
			if (isset($this->server['HTTP_HOST'])) {
				$host = $this->server['HTTP_HOST'];
			} else if (isset($this->server['SERVER_NAME'])) {
				$host = $this->server['SERVER_NAME'];
			}
		}
		return $host;
	}


	/**
	 * Returns the server host from the headers, or the first configured
	 * trusted domain if the host isn't in the trusted list
	 * @return string Server host
	 */
	public function getServerHost() {
		// FIXME: Ugly workaround that we need to get rid of
		if (\OC::$CLI && defined('PHPUNIT_RUN')) {
			return 'localhost';
		}

		// overwritehost is always trusted
		$host = $this->getOverwriteHost();
		if ($host !== null) {
			return $host;
		}

		// get the host from the headers
		$host = $this->getInsecureServerHost();

		// Verify that the host is a trusted domain if the trusted domains
		// are defined
		// If no trusted domain is provided the first trusted domain is returned
		$trustedDomainHelper = new TrustedDomainHelper($this->config);
		if ($trustedDomainHelper->isTrustedDomain($host)) {
			return $host;
		} else {
			$trustedList = $this->config->getSystemValue('trusted_domains', []);
			return $trustedList[0];
		}
	}

	/**
	 * Returns the overwritehost setting from the config if set and
	 * if the overwrite condition is met
	 * @return string|null overwritehost value or null if not defined or the defined condition
	 * isn't met
	 */
	private function getOverwriteHost() {
		if($this->config->getSystemValue('overwritehost') !== '' && $this->isOverwriteCondition()) {
			return $this->config->getSystemValue('overwritehost');
		}
		return null;
	}

}
