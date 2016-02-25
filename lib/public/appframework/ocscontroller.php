<?php

namespace OCP\AppFramework;

use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\OCSResponse;
use OCP\IRequest;


/**
 * Base class to inherit your controllers from that are used for RESTful APIs
 * @since 8.1.0
 */
abstract class OCSController extends ApiController {

	/**
	 * constructor of the controller
	 * @param string $appName the name of the app
	 * @param IRequest $request an instance of the request
	 * @param string $corsMethods comma seperated string of HTTP verbs which
	 * should be allowed for websites or webapps when calling your API, defaults to
	 * 'PUT, POST, GET, DELETE, PATCH'
	 * @param string $corsAllowedHeaders comma seperated string of HTTP headers
	 * which should be allowed for websites or webapps when calling your API,
	 * defaults to 'Authorization, Content-Type, Accept'
	 * @param int $corsMaxAge number in seconds how long a preflighted OPTIONS
	 * request should be cached, defaults to 1728000 seconds
	 * @since 8.1.0
	 */
	public function __construct($appName,
								IRequest $request,
								$corsMethods='PUT, POST, GET, DELETE, PATCH',
								$corsAllowedHeaders='Authorization, Content-Type, Accept',
								$corsMaxAge=1728000){
		parent::__construct($appName, $request, $corsMethods,
							$corsAllowedHeaders, $corsMaxAge);
		$this->registerResponder('json', function ($data) {
			return $this->buildOCSResponse('json', $data);
		});
		$this->registerResponder('xml', function ($data) {
			return $this->buildOCSResponse('xml', $data);
		});
	}


	/**
	 * Unwrap data and build ocs response
	 * @param string $format json or xml
	 * @param array|DataResponse $data the data which should be transformed
	 * @since 8.1.0
	 */
	private function buildOCSResponse($format, $data) {
		if ($data instanceof DataResponse) {
			$data = $data->getData();
		}

		$params = [
			'status' => 'OK',
			'statuscode' => 100,
			'message' => 'OK',
			'data' => [],
			'tag' => '',
			'tagattribute' => '',
			'dimension' => 'dynamic',
			'itemscount' => '',
			'itemsperpage' => ''
		];

		foreach ($data as $key => $value) {
			$params[$key] = $value;
		}

		return new OCSResponse(
			$format, $params['status'], $params['statuscode'],
			$params['message'], $params['data'], $params['tag'],
			$params['tagattribute'], $params['dimension'],
			$params['itemscount'], $params['itemsperpage']
		);
	}

}
