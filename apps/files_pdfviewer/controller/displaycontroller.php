<?php

namespace OCA\Files_PdfViewer\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IURLGenerator;

class DisplayController extends Controller {

	/** @var IURLGenerator */
	private $urlGenerator;

	/**
	 * @param string $AppName
	 * @param IRequest $request
	 * @param IURLGenerator $urlGenerator
	 */
	public function __construct($AppName,
								IRequest $request,
								IURLGenerator $urlGenerator) {
		parent::__construct($AppName, $request);
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @PublicPage
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function showPdfViewer() {
		$params = [
			'urlGenerator' => $this->urlGenerator
		];
		$response = new TemplateResponse($this->appName, 'viewer', $params, 'blank');

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedChildSrcDomain('\'self\'');
		$policy->addAllowedFontDomain('data:');
		$response->setContentSecurityPolicy($policy);

		return $response;
	}

}
