<?php

namespace OCP;

/**
 * Interface ICertificate
 *
 * @package OCP
 * @since 8.0.0
 */
interface ICertificate {
	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function getName();

	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function getCommonName();

	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function getOrganization();

	/**
	 * @return \DateTime
	 * @since 8.0.0
	 */
	public function getIssueDate();

	/**
	 * @return \DateTime
	 * @since 8.0.0
	 */
	public function getExpireDate();

	/**
	 * @return bool
	 * @since 8.0.0
	 */
	public function isExpired();

	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function getIssuerName();

	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function getIssuerOrganization();
}
