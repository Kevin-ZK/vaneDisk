<?php

namespace OCA\TemplateEditor\Controller;

use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCA\TemplateEditor\MailTemplate;

class AdminSettingsController extends ApiController {

	public function __construct($appName, IRequest $request) {
		parent::__construct($appName, $request);
	}

	/**
	 * @param string $theme
	 * @param string $template
	 * @return \OCA\TemplateEditor\Http\MailTemplateResponse
	 */
	public function renderTemplate( $theme, $template ) {
		try {
			$template = new MailTemplate( $theme, $template );
			return $template->getResponse();
		} catch (\Exception $ex) {
			return new JSONResponse(array('message' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * @param string $theme
	 * @param string $template
	 * @param string $content
	 * @return JSONResponse
	 */
	public function updateTemplate( $theme, $template, $content ) {
		try {
			$template = new MailTemplate( $theme, $template );
			$template->setContent( $content );
			return new JSONResponse();
		} catch (\Exception $ex) {
			return new JSONResponse(array('message' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * @param string $theme
	 * @param string $template
	 * @return JSONResponse
	 */
	public function resetTemplate( $theme, $template ) {
		try {
			$template = new MailTemplate( $theme, $template );
			$template->reset();
			return new JSONResponse();
		} catch (\Exception $ex) {
			return new JSONResponse(array('message' => $ex->getMessage()), $ex->getCode());
		}
	}

}
