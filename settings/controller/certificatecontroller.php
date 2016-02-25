<?php

namespace OC\Settings\Controller;

use OCP\App\IAppManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\ICertificateManager;
use OCP\IL10N;
use OCP\IRequest;

/**
 * @package OC\Settings\Controller
 */
class CertificateController extends Controller {
	/** @var ICertificateManager */
	private $certificateManager;
	/** @var IL10N */
	private $l10n;
	/** @var IAppManager */
	private $appManager;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param ICertificateManager $certificateManager
	 * @param IL10N $l10n
	 * @param IAppManager $appManager
	 */
	public function __construct($appName,
								IRequest $request,
								ICertificateManager $certificateManager,
								IL10N $l10n,
								IAppManager $appManager) {
		parent::__construct($appName, $request);
		$this->certificateManager = $certificateManager;
		$this->l10n = $l10n;
		$this->appManager = $appManager;
	}

	/**
	 * Add a new personal root certificate to the users' trust store
	 *
	 * @NoAdminRequired
	 * @NoSubadminRequired
	 * @return array
	 */
	public function addPersonalRootCertificate() {

		if ($this->isCertificateImportAllowed() === false) {
			return new DataResponse('Individual certificate management disabled', Http::STATUS_FORBIDDEN);
		}

		$file = $this->request->getUploadedFile('rootcert_import');
		if(empty($file)) {
			return new DataResponse(['message' => 'No file uploaded'], Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		try {
			$certificate = $this->certificateManager->addCertificate(file_get_contents($file['tmp_name']), $file['name']);
			return new DataResponse([
				'name' => $certificate->getName(),
				'commonName' => $certificate->getCommonName(),
				'organization' => $certificate->getOrganization(),
				'validFrom' => $certificate->getIssueDate()->getTimestamp(),
				'validTill' => $certificate->getExpireDate()->getTimestamp(),
				'validFromString' => $this->l10n->l('date', $certificate->getIssueDate()),
				'validTillString' => $this->l10n->l('date', $certificate->getExpireDate()),
				'issuer' => $certificate->getIssuerName(),
				'issuerOrganization' => $certificate->getIssuerOrganization(),
			]);
		} catch (\Exception $e) {
			return new DataResponse('An error occurred.', Http::STATUS_UNPROCESSABLE_ENTITY);
		}
	}

	/**
	 * Removes a personal root certificate from the users' trust store
	 *
	 * @NoAdminRequired
	 * @NoSubadminRequired
	 * @param string $certificateIdentifier
	 * @return DataResponse
	 */
	public function removePersonalRootCertificate($certificateIdentifier) {

		if ($this->isCertificateImportAllowed() === false) {
			return new DataResponse('Individual certificate management disabled', Http::STATUS_FORBIDDEN);
		}

		$this->certificateManager->removeCertificate($certificateIdentifier);
		return new DataResponse();
	}

	/**
	 * check if certificate import is allowed
	 *
	 * @return bool
	 */
	protected function isCertificateImportAllowed() {
		$externalStorageEnabled = $this->appManager->isEnabledForUser('files_external');
		if ($externalStorageEnabled) {
			$backends = \OC_Mount_Config::getPersonalBackends();
			if (!empty($backends)) {
				return true;
			}
		}
		return false;
	}

}
