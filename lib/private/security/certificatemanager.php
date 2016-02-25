<?php

namespace OC\Security;

use OC\Files\Filesystem;
use OCP\ICertificateManager;

/**
 * Manage trusted certificates for users
 */
class CertificateManager implements ICertificateManager {
	/**
	 * @var string
	 */
	protected $uid;

	/**
	 * @var \OC\Files\View
	 */
	protected $view;

	/**
	 * @param string $uid
	 * @param \OC\Files\View $view relative zu data/
	 */
	public function __construct($uid, \OC\Files\View $view) {
		$this->uid = $uid;
		$this->view = $view;
	}

	/**
	 * Returns all certificates trusted by the user
	 *
	 * @return \OCP\ICertificate[]
	 */
	public function listCertificates() {
		$path = $this->getPathToCertificates() . 'uploads/';
		if (!$this->view->is_dir($path)) {
			return array();
		}
		$result = array();
		$handle = $this->view->opendir($path);
		if (!is_resource($handle)) {
			return array();
		}
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
				try {
					$result[] = new Certificate($this->view->file_get_contents($path . $file), $file);
				} catch(\Exception $e) {}
			}
		}
		closedir($handle);
		return $result;
	}

	/**
	 * create the certificate bundle of all trusted certificated
	 */
	public function createCertificateBundle() {
		$path = $this->getPathToCertificates();
		$certs = $this->listCertificates();

		$fh_certs = $this->view->fopen($path . '/rootcerts.crt', 'w');

		// Write user certificates
		foreach ($certs as $cert) {
			$file = $path . '/uploads/' . $cert->getName();
			$data = $this->view->file_get_contents($file);
			if (strpos($data, 'BEGIN CERTIFICATE')) {
				fwrite($fh_certs, $data);
				fwrite($fh_certs, "\r\n");
			}
		}

		// Append the default certificates
		$defaultCertificates = file_get_contents(\OC::$SERVERROOT . '/config/ca-bundle.crt');
		fwrite($fh_certs, $defaultCertificates);
		fclose($fh_certs);
	}

	/**
	 * Save the certificate and re-generate the certificate bundle
	 *
	 * @param string $certificate the certificate data
	 * @param string $name the filename for the certificate
	 * @return \OCP\ICertificate
	 * @throws \Exception If the certificate could not get added
	 */
	public function addCertificate($certificate, $name) {
		if (!Filesystem::isValidPath($name) or Filesystem::isFileBlacklisted($name)) {
			throw new \Exception('Filename is not valid');
		}

		$dir = $this->getPathToCertificates() . 'uploads/';
		if (!$this->view->file_exists($dir)) {
			$this->view->mkdir($dir);
		}

		try {
			$file = $dir . $name;
			$certificateObject = new Certificate($certificate, $name);
			$this->view->file_put_contents($file, $certificate);
			$this->createCertificateBundle();
			return $certificateObject;
		} catch (\Exception $e) {
			throw $e;
		}

	}

	/**
	 * Remove the certificate and re-generate the certificate bundle
	 *
	 * @param string $name
	 * @return bool
	 */
	public function removeCertificate($name) {
		if (!Filesystem::isValidPath($name)) {
			return false;
		}
		$path = $this->getPathToCertificates() . 'uploads/';
		if ($this->view->file_exists($path . $name)) {
			$this->view->unlink($path . $name);
			$this->createCertificateBundle();
		}
		return true;
	}

	/**
	 * Get the path to the certificate bundle for this user
	 *
	 * @return string
	 */
	public function getCertificateBundle() {
		return $this->getPathToCertificates() . 'rootcerts.crt';
	}

	/**
	 * @return string
	 */
	private function getPathToCertificates() {
		$path = is_null($this->uid) ? '/files_external/' : '/' . $this->uid . '/files_external/';

		return $path;
	}
}
