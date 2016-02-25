<?php

namespace OC\Share;

class Constants {

	const SHARE_TYPE_USER = 0;
	const SHARE_TYPE_GROUP = 1;
	const SHARE_TYPE_LINK = 3;
	const SHARE_TYPE_EMAIL = 4;   // ToDo Check if it is still in use otherwise remove it
	const SHARE_TYPE_CONTACT = 5; // ToDo Check if it is still in use otherwise remove it
	const SHARE_TYPE_REMOTE = 6;  // ToDo Check if it is still in use otherwise remove it

	const FORMAT_NONE = -1;
	const FORMAT_STATUSES = -2;
	const FORMAT_SOURCES = -3;  // ToDo Check if it is still in use otherwise remove it

	const RESPONSE_FORMAT = 'json'; // default resonse format for ocs calls

	const TOKEN_LENGTH = 15; // old (oc7) length is 32, keep token length in db at least that for compatibility

	const BASE_PATH_TO_SHARE_API = '/ocs/v1.php/cloud/shares';

	protected static $shareTypeUserAndGroups = -1;
	protected static $shareTypeGroupUserUnique = 2;
	protected static $backends = array();
	protected static $backendTypes = array();
	protected static $isResharingAllowed;
}
