<?php
/**
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace OC\Core\Avatar;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\IAvatarManager;
use OCP\ICache;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IUserManager;
use OCP\IUserSession;

/**
 * Class AvatarController
 *
 * @package OC\Core\Avatar
 */
class AvatarController extends Controller {

	/** @var IAvatarManager */
	protected $avatarManager;

	/** @var \OC\Cache\File */
	protected $cache;

	/** @var IL10N */
	protected $l;

	/** @var IUserManager */
	protected $userManager;

	/** @var IUserSession */
	protected $userSession;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param IAvatarManager $avatarManager
	 * @param \OC\Cache\File $cache
	 * @param IL10N $l10n
	 * @param IUserManager $userManager
	 * @param IUserSession $userSession
	 */
	public function __construct($appName,
								IRequest $request,
								IAvatarManager $avatarManager,
								\OC\Cache\File $cache,
								IL10N $l10n,
								IUserManager $userManager,
								IUserSession $userSession) {
		parent::__construct($appName, $request);

		$this->avatarManager = $avatarManager;
		$this->cache = $cache;
		$this->l = $l10n;
		$this->userManager = $userManager;
		$this->userSession = $userSession;
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $userId
	 * @param int $size
	 * @return DataResponse|DataDisplayResponse
	 */
	public function getAvatar($userId, $size) {
		if ($size > 2048) {
			$size = 2048;
		} elseif ($size <= 0) {
			$size = 64;
		}

		$avatar = $this->avatarManager->getAvatar($userId);
		$image = $avatar->get($size);

		if ($image instanceof \OCP\IImage) {
			$resp = new DataDisplayResponse($image->data(),
				Http::STATUS_OK,
				['Content-Type' => $image->mimeType()]);
			$resp->setETag(crc32($image->data()));
		} else {
			$user = $this->userManager->get($userId);
			$userName = $user ? $user->getDisplayName() : '';
			$resp = new DataResponse([
				'data' => [
					'displayname' => $userName,
				],
			]);
		}

		$resp->addHeader('Pragma', 'public');
		$resp->cacheFor(0);
		$resp->setLastModified(new \DateTime('now', new \DateTimeZone('GMT')));

		return $resp;
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $path
	 * @return DataResponse
	 */
	public function postAvatar($path) {
		$userId = $this->userSession->getUser()->getUID();
		$files = $this->request->getUploadedFile('files');

		if (isset($path)) {
			$path = stripslashes($path);
			$view = new \OC\Files\View('/'.$userId.'/files');
			if ($view->filesize($path) > 20*1024*1024) {
				return new DataResponse(['data' => ['message' => $this->l->t('File is too big')]],
					Http::STATUS_BAD_REQUEST);
			}
			$fileName = $view->getLocalFile($path);
		} elseif (!is_null($files)) {
			if (
				$files['error'][0] === 0 &&
				 is_uploaded_file($files['tmp_name'][0]) &&
				!\OC\Files\Filesystem::isFileBlacklisted($files['tmp_name'][0])
			) {
				if ($files['size'][0] > 20*1024*1024) {
					return new DataResponse(['data' => ['message' => $this->l->t('File is too big')]],
						Http::STATUS_BAD_REQUEST);
				}
				$this->cache->set('avatar_upload', file_get_contents($files['tmp_name'][0]), 7200);
				$view = new \OC\Files\View('/'.$userId.'/cache');
				$fileName = $view->getLocalFile('avatar_upload');
				unlink($files['tmp_name'][0]);
			} else {
				return new DataResponse(['data' => ['message' => $this->l->t('Invalid file provided')]],
										Http::STATUS_BAD_REQUEST);
			}
		} else {
			//Add imgfile
			return new DataResponse(['data' => ['message' => $this->l->t('No image or file provided')]],
									Http::STATUS_BAD_REQUEST);
		}

		try {
			$image = new \OC_Image();
			$image->loadFromFile($fileName);
			$image->fixOrientation();

			if ($image->valid()) {
				$mimeType = $image->mimeType();
				if ($mimeType !== 'image/jpeg' && $mimeType !== 'image/png') {
					return new DataResponse(['data' => ['message' => $this->l->t('Unknown filetype')]]);
				}

				$this->cache->set('tmpAvatar', $image->data(), 7200);
				return new DataResponse(['data' => 'notsquare']);
			} else {
				return new DataResponse(['data' => ['message' => $this->l->t('Invalid image')]]);
			}
		} catch (\Exception $e) {
			return new DataResponse(['data' => ['message' => $e->getMessage()]]);
		}
	}

	/**
	 * @NoAdminRequired
     *
	 * @return DataResponse
	 */
	public function deleteAvatar() {
		$userId = $this->userSession->getUser()->getUID();

		try {
			$avatar = $this->avatarManager->getAvatar($userId);
			$avatar->remove();
			return new DataResponse();
		} catch (\Exception $e) {
			return new DataResponse(['data' => ['message' => $e->getMessage()]], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @NoAdminRequired
	 *
	 * @return DataResponse|DataDisplayResponse
	 */
	public function getTmpAvatar() {
		$tmpAvatar = $this->cache->get('tmpAvatar');
		if (is_null($tmpAvatar)) {
			return new DataResponse(['data' => [
										'message' => $this->l->t("No temporary profile picture available, try again")
									]],
									Http::STATUS_NOT_FOUND);
		}

		$image = new \OC_Image($tmpAvatar);

		$resp = new DataDisplayResponse($image->data(),
				Http::STATUS_OK,
				['Content-Type' => $image->mimeType()]);

		$resp->setETag(crc32($image->data()));
		$resp->cacheFor(0);
		$resp->setLastModified(new \DateTime('now', new \DateTimeZone('GMT')));
		return $resp;
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param array $crop
	 * @return DataResponse
	 */
	public function postCroppedAvatar($crop) {
		$userId = $this->userSession->getUser()->getUID();

		if (is_null($crop)) {
			return new DataResponse(['data' => ['message' => $this->l->t("No crop data provided")]],
									Http::STATUS_BAD_REQUEST);
		}

		if (!isset($crop['x'], $crop['y'], $crop['w'], $crop['h'])) {
			return new DataResponse(['data' => ['message' => $this->l->t("No valid crop data provided")]],
									Http::STATUS_BAD_REQUEST);
		}

		$tmpAvatar = $this->cache->get('tmpAvatar');
		if (is_null($tmpAvatar)) {
			return new DataResponse(['data' => [
										'message' => $this->l->t("No temporary profile picture available, try again")
									]],
									Http::STATUS_BAD_REQUEST);
		}

		$image = new \OC_Image($tmpAvatar);
		$image->crop($crop['x'], $crop['y'], round($crop['w']), round($crop['h']));
		try {
			$avatar = $this->avatarManager->getAvatar($userId);
			$avatar->set($image);
			// Clean up
			$this->cache->remove('tmpAvatar');
			return new DataResponse(['status' => 'success']);
		} catch (\OC\NotSquareException $e) {
			return new DataResponse(['data' => ['message' => $this->l->t('Crop is not square')]],
									Http::STATUS_BAD_REQUEST);

		}catch (\Exception $e) {
			return new DataResponse(['data' => ['message' => $e->getMessage()]],
									Http::STATUS_BAD_REQUEST);
		}
	}
}
