<?php

namespace OCP;

/**
 * Manage trusted certificates for users
 * @since 8.0.0
 */
interface ICertificateManager {
	/**
	 * Returns all certificates trusted by the user
	 *
	 * @return \OCP\ICertificate[]
	 * @since 8.0.0
	 */
	public function listCertificates();

	/**
	 * @param string $certificate the certificate data
	 * @param string $name the filename for the certificate
	 * @return \OCP\ICertificate
	 * @throws \Exception If the certificate could not get added
	 * @since 8.0.0 - since 8.1.0 throws exception instead of returning false
	 */
	public function addCertificate($certificate, $name);

	/**
	 * @param string $name
	 * @since 8.0.0
	 */
	public function removeCertificate($name);

	/**
	 * Get the path to the certificate bundle for this user
	 *
	 * @return string
	 * @since 8.0.0
	 */
	public function getCertificateBundle();
}
