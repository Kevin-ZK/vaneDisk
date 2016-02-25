<?php
namespace OC\Preview;

class MP3 extends Provider {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/audio\/mpeg/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getThumbnail($path, $maxX, $maxY, $scalingup, $fileview) {
		$getID3 = new \getID3();

		$tmpPath = $fileview->toTmpFile($path);

		$tags = $getID3->analyze($tmpPath);
		\getid3_lib::CopyTagsToComments($tags);
		if(isset($tags['id3v2']['APIC'][0]['data'])) {
			$picture = @$tags['id3v2']['APIC'][0]['data'];
			unlink($tmpPath);
			$image = new \OC_Image();
			$image->loadFromData($picture);

			if ($image->valid()) {
				$image->scaleDownToFit($maxX, $maxY);

				return $image;
			}
		}

		return $this->getNoCoverThumbnail();
	}

	/**
	 * Generates a default image when the file has no cover
	 *
	 * @return bool|\OCP\IImage false if the default image is missing or invalid
	 */
	private function getNoCoverThumbnail() {
		$icon = \OC::$SERVERROOT . '/core/img/filetypes/audio.png';

		if(!file_exists($icon)) {
			return false;
		}

		$image = new \OC_Image();
		$image->loadFromFile($icon);
		return $image->valid() ? $image : false;
	}

}
