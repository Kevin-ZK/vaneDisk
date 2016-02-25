<?php

\OCP\JSON::checkAppEnabled('gallery');

OCP\Util::addStyle('gallery', 'styles');
OCP\Util::addStyle('gallery', 'mobile');

$token = isset($_GET['t']) ? (string)$_GET['t'] : '';

if ($token) {
	$linkItem = \OCP\Share::getShareByToken($token, false);
	if (is_array($linkItem) && isset($linkItem['uid_owner'])) {
		// seems to be a valid share
		$type = $linkItem['item_type'];
		$fileSource = $linkItem['file_source'];
		$shareOwner = $linkItem['uid_owner'];
		$path = null;
		$rootLinkItem = \OCP\Share::resolveReShare($linkItem);
		$fileOwner = $rootLinkItem['uid_owner'];
		$albumName = trim($linkItem['file_target'], '//');
		$ownerDisplayName = \OC_User::getDisplayName($fileOwner);

		// stupid copy and paste job
		if (isset($linkItem['share_with'])) {
			// Authenticate share_with
			$url = OCP\Util::linkToPublic('gallery') . '&t=' . $token;
			if (isset($_GET['file'])) {
				$url .= '&file=' . urlencode($_GET['file']);
			} else {
				if (isset($_GET['dir'])) {
					$url .= '&dir=' . urlencode($_GET['dir']);
				}
			}
			if (isset($_POST['password'])) {
				$password = $_POST['password'];
				if ($linkItem['share_type'] == OCP\Share::SHARE_TYPE_LINK) {
					// Check Password
					$newHash = '';
					if(\OC::$server->getHasher()->verify($password, $linkItem['share_with'], $newHash)) {
						\OC::$server->getSession()->set('public_link_authenticated', $linkItem['id']);

						/**
						 * FIXME: Migrate old hashes to new hash format
						 * Due to the fact that there is no reasonable functionality to update the password
						 * of an existing share no migration is yet performed there.
						 * The only possibility is to update the existing share which will result in a new
						 * share ID and is a major hack.
						 *
						 * In the future the migration should be performed once there is a proper method
						 * to update the share's password. (for example `$share->updatePassword($password)`
						 *
						 * @link https://github.com/owncloud/core/issues/10671
						 */
						if(!empty($newHash)) {

						}
					} else {
						OCP\Util::addStyle('files_sharing', 'authenticate');
						$tmpl = new OCP\Template('files_sharing', 'authenticate', 'guest');
						$tmpl->assign('URL', $url);
						$tmpl->assign('wrongpw', true);
						$tmpl->printPage();
						exit();
					}
				} else {
					OCP\Util::writeLog('share', 'Unknown share type '.$linkItem['share_type']
						.' for share id '.$linkItem['id'], \OCP\Util::ERROR);
					header('HTTP/1.0 404 Not Found');
					$tmpl = new OCP\Template('', '404', 'guest');
					$tmpl->printPage();
					exit();
				}

			} else {
				// Check if item id is set in session
				if ( ! \OC::$server->getSession()->exists('public_link_authenticated')
					|| \OC::$server->getSession()->get('public_link_authenticated') !== $linkItem['id']
				) {
					// Prompt for password
					OCP\Util::addStyle('files_sharing', 'authenticate');
					$tmpl = new OCP\Template('files_sharing', 'authenticate', 'guest');
					$tmpl->assign('URL', $url);
					$tmpl->printPage();
					exit();
				}
			}
		}


		// render template
		$tmpl = new \OCP\Template('gallery', 'public', 'base');
		OCP\Util::addScript('gallery', 'album');
		OCP\Util::addScript('gallery', 'gallery');
		OCP\Util::addScript('gallery', 'thumbnail');
		OCP\Util::addStyle('gallery', 'public');
		$tmpl->assign('token', $token);
		$tmpl->assign('requesttoken', \OCP\Util::callRegister());
		$tmpl->assign('displayName', $ownerDisplayName);
		$tmpl->assign('albumName', $albumName);

		$tmpl->printPage();
		exit;
	}
}

$tmpl = new OCP\Template('', '404', 'guest');
$tmpl->printPage();
