<?php

namespace OC\Http\Client;

use GuzzleHttp\Client as GuzzleClient;
use OCP\Http\Client\IClient;
use OCP\ICertificateManager;
use OCP\IConfig;

/**
 * Class Client
 *
 * @package OC\Http
 */
class Client implements IClient {
	/** @var GuzzleClient */
	private $client;
	/** @var IConfig */
	private $config;
	/** @var ICertificateManager */
	private $certificateManager;

	/**
	 * @param IConfig $config
	 * @param ICertificateManager $certificateManager
	 * @param GuzzleClient $client
	 */
	public function __construct(IConfig $config,
								ICertificateManager $certificateManager,
								GuzzleClient $client) {
		$this->config = $config;
		$this->client = $client;
		$this->certificateManager = $certificateManager;
		$this->setDefaultOptions();
	}

	/**
	 * Sets the default options to the client
	 */
	private function setDefaultOptions() {
		// Either use default bundle or the user bundle if nothing is specified
		if($this->certificateManager->listCertificates() !== []) {
			$dataDir = $this->config->getSystemValue('datadirectory');
			$this->client->setDefaultOption('verify', $dataDir.'/'.$this->certificateManager->getCertificateBundle());
		} else {
			$this->client->setDefaultOption('verify', \OC::$SERVERROOT . '/config/ca-bundle.crt');
		}

		$this->client->setDefaultOption('headers/User-Agent', 'ownCloud Server Crawler');
		if($this->getProxyUri() !== '') {
			$this->client->setDefaultOption('proxy', $this->getProxyUri());
		}
	}

	/**
	 * Get the proxy URI
	 * @return string
	 */
	private function getProxyUri() {
		$proxyHost = $this->config->getSystemValue('proxy', null);
		$proxyUserPwd = $this->config->getSystemValue('proxyuserpwd', null);
		$proxyUri = '';

		if(!is_null($proxyUserPwd)) {
			$proxyUri .= $proxyUserPwd.'@';
		}
		if(!is_null($proxyHost)) {
			$proxyUri .= $proxyHost;
		}

		return $proxyUri;
	}

	/**
	 * Sends a GET request
	 * @param string $uri
	 * @param array $options Array such as
	 *              'query' => [
	 *                  'field' => 'abc',
	 *                  'other_field' => '123',
	 *                  'file_name' => fopen('/path/to/file', 'r'),
	 *              ],
	 *              'headers' => [
	 *                  'foo' => 'bar',
	 *              ],
	 *              'cookies' => ['
	 *                  'foo' => 'bar',
	 *              ],
	 *              'allow_redirects' => [
	 *                   'max'       => 10,  // allow at most 10 redirects.
	 *                   'strict'    => true,     // use "strict" RFC compliant redirects.
	 *                   'referer'   => true,     // add a Referer header
	 *                   'protocols' => ['https'] // only allow https URLs
	 *              ],
	 *              'save_to' => '/path/to/file', // save to a file or a stream
	 *              'verify' => true, // bool or string to CA file
	 *              'debug' => true,
	 *              'timeout' => 5,
	 * @return Response
	 * @throws \Exception If the request could not get completed
	 */
	public function get($uri, array $options = []) {
		$response = $this->client->get($uri, $options);
		return new Response($response);
	}

	/**
	 * Sends a HEAD request
	 * @param string $uri
	 * @param array $options Array such as
	 *              'headers' => [
	 *                  'foo' => 'bar',
	 *              ],
	 *              'cookies' => ['
	 *                  'foo' => 'bar',
	 *              ],
	 *              'allow_redirects' => [
	 *                   'max'       => 10,  // allow at most 10 redirects.
	 *                   'strict'    => true,     // use "strict" RFC compliant redirects.
	 *                   'referer'   => true,     // add a Referer header
	 *                   'protocols' => ['https'] // only allow https URLs
	 *              ],
	 *              'save_to' => '/path/to/file', // save to a file or a stream
	 *              'verify' => true, // bool or string to CA file
	 *              'debug' => true,
	 *              'timeout' => 5,
	 * @return Response
	 */
	public function head($uri, $options = []) {
		$response = $this->client->head($uri, $options);
		return new Response($response);
	}

	/**
	 * Sends a POST request
	 * @param string $uri
	 * @param array $options Array such as
	 *              'body' => [
	 *                  'field' => 'abc',
	 *                  'other_field' => '123',
	 *                  'file_name' => fopen('/path/to/file', 'r'),
	 *              ],
	 *              'headers' => [
	 *                  'foo' => 'bar',
	 *              ],
	 *              'cookies' => ['
	 *                  'foo' => 'bar',
	 *              ],
	 *              'allow_redirects' => [
	 *                   'max'       => 10,  // allow at most 10 redirects.
	 *                   'strict'    => true,     // use "strict" RFC compliant redirects.
	 *                   'referer'   => true,     // add a Referer header
	 *                   'protocols' => ['https'] // only allow https URLs
	 *              ],
	 *              'save_to' => '/path/to/file', // save to a file or a stream
	 *              'verify' => true, // bool or string to CA file
	 *              'debug' => true,
	 *              'timeout' => 5,
	 * @return Response
	 */
	public function post($uri, array $options = []) {
		$response = $this->client->post($uri, $options);
		return new Response($response);
	}

	/**
	 * Sends a PUT request
	 * @param string $uri
	 * @param array $options Array such as
	 *              'body' => [
	 *                  'field' => 'abc',
	 *                  'other_field' => '123',
	 *                  'file_name' => fopen('/path/to/file', 'r'),
	 *              ],
	 *              'headers' => [
	 *                  'foo' => 'bar',
	 *              ],
	 *              'cookies' => ['
	 *                  'foo' => 'bar',
	 *              ],
	 *              'allow_redirects' => [
	 *                   'max'       => 10,  // allow at most 10 redirects.
	 *                   'strict'    => true,     // use "strict" RFC compliant redirects.
	 *                   'referer'   => true,     // add a Referer header
	 *                   'protocols' => ['https'] // only allow https URLs
	 *              ],
	 *              'save_to' => '/path/to/file', // save to a file or a stream
	 *              'verify' => true, // bool or string to CA file
	 *              'debug' => true,
	 *              'timeout' => 5,
	 * @return Response
	 */
	public function put($uri, array $options = []) {
		$response = $this->client->put($uri, $options);
		return new Response($response);
	}

	/**
	 * Sends a DELETE request
	 * @param string $uri
	 * @param array $options Array such as
	 *              'body' => [
	 *                  'field' => 'abc',
	 *                  'other_field' => '123',
	 *                  'file_name' => fopen('/path/to/file', 'r'),
	 *              ],
	 *              'headers' => [
	 *                  'foo' => 'bar',
	 *              ],
	 *              'cookies' => ['
	 *                  'foo' => 'bar',
	 *              ],
	 *              'allow_redirects' => [
	 *                   'max'       => 10,  // allow at most 10 redirects.
	 *                   'strict'    => true,     // use "strict" RFC compliant redirects.
	 *                   'referer'   => true,     // add a Referer header
	 *                   'protocols' => ['https'] // only allow https URLs
	 *              ],
	 *              'save_to' => '/path/to/file', // save to a file or a stream
	 *              'verify' => true, // bool or string to CA file
	 *              'debug' => true,
	 *              'timeout' => 5,
	 * @return Response
	 */
	public function delete($uri, array $options = []) {
		$response = $this->client->delete($uri, $options);
		return new Response($response);
	}


	/**
	 * Sends a options request
	 * @param string $uri
	 * @param array $options Array such as
	 *              'body' => [
	 *                  'field' => 'abc',
	 *                  'other_field' => '123',
	 *                  'file_name' => fopen('/path/to/file', 'r'),
	 *              ],
	 *              'headers' => [
	 *                  'foo' => 'bar',
	 *              ],
	 *              'cookies' => ['
	 *                  'foo' => 'bar',
	 *              ],
	 *              'allow_redirects' => [
	 *                   'max'       => 10,  // allow at most 10 redirects.
	 *                   'strict'    => true,     // use "strict" RFC compliant redirects.
	 *                   'referer'   => true,     // add a Referer header
	 *                   'protocols' => ['https'] // only allow https URLs
	 *              ],
	 *              'save_to' => '/path/to/file', // save to a file or a stream
	 *              'verify' => true, // bool or string to CA file
	 *              'debug' => true,
	 *              'timeout' => 5,
	 * @return Response
	 */
	public function options($uri, array $options = []) {
		$response = $this->client->options($uri, $options);
		return new Response($response);
	}
}
