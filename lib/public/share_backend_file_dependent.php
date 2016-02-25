<?php
namespace OCP;

/**
 * Interface for share backends that share content that is dependent on files.
 * Extends the Share_Backend interface.
 * @since 5.0.0
 */
interface Share_Backend_File_Dependent extends Share_Backend {
	/**
	 * Get the file path of the item
	 * @param string $itemSource
	 * @param string $uidOwner User that is the owner of shared item
	 * @return string|false
	 * @since 5.0.0
	 */
	public function getFilePath($itemSource, $uidOwner);

}
