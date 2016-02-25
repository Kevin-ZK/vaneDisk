<?php

namespace OCP;

/** @deprecated 8.0.0 Use \OCP\Constants::PERMISSION_CREATE instead */
const PERMISSION_CREATE = 4;

/** @deprecated 8.0.0 Use \OCP\Constants::PERMISSION_READ instead */
const PERMISSION_READ = 1;

/** @deprecated 8.0.0 Use \OCP\Constants::PERMISSION_UPDATE instead */
const PERMISSION_UPDATE = 2;

/** @deprecated 8.0.0 Use \OCP\Constants::PERMISSION_DELETE instead */
const PERMISSION_DELETE = 8;

/** @deprecated 8.0.0 Use \OCP\Constants::PERMISSION_SHARE instead */
const PERMISSION_SHARE = 16;

/** @deprecated 8.0.0 Use \OCP\Constants::PERMISSION_ALL instead */
const PERMISSION_ALL = 31;

/** @deprecated 8.0.0 Use \OCP\Constants::FILENAME_INVALID_CHARS instead */
const FILENAME_INVALID_CHARS = "\\/<>:\"|?*\n";

/**
 * Class Constants
 *
 * @package OCP
 * @since 8.0.0
 */
class Constants {
	/**
	 * CRUDS permissions.
	 * @since 8.0.0
	 */
	const PERMISSION_CREATE = 4;
	const PERMISSION_READ = 1;
	const PERMISSION_UPDATE = 2;
	const PERMISSION_DELETE = 8;
	const PERMISSION_SHARE = 16;
	const PERMISSION_ALL = 31;

	/**
	 * @since 8.0.0
	 */
	const FILENAME_INVALID_CHARS = "\\/<>:\"|?*\n";
}
