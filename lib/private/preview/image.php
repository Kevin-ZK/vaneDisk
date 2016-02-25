<?php
namespace OC\Preview;

abstract class Image extends Provider {

	/**
	 * {@inheritDoc}
	 */
	public function getThumbnail($path, $maxX, $maxY, $scalingup, $fileview) {
		//get fileinfo
		$fileInfo = $fileview->getFileInfo($path);
		if (!$fileInfo) {
			return false;
		}

		$maxSizeForImages = \OC::$server->getConfig()->getSystemValue('preview_max_filesize_image', 50);
		$size = $fileInfo->getSize();

		if ($maxSizeForImages !== -1 && $size > ($maxSizeForImages * 1024 * 1024)) {
			return false;
		}

		$image = new \OC_Image();

		if ($fileInfo['encrypted'] === true) {
			$fileName = $fileview->toTmpFile($path);
		} else {
			$fileName = $fileview->getLocalFile($path);
		}
		$image->loadFromFile($fileName);
		$image->fixOrientation();
		if ($image->valid()) {
			$image->scaleDownToFit($maxX, $maxY);

			return $image;
		}
		return false;
	}

}
