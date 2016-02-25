<?php

namespace OCP\Encryption;

/**
 * Interface IEncryptionModule
 *
 * @package OCP\Encryption
 * @since 8.1.0
 */
interface IEncryptionModule {

	/**
	 * @return string defining the technical unique id
	 * @since 8.1.0
	 */
	public function getId();

	/**
	 * In comparison to getKey() this function returns a human readable (maybe translated) name
	 *
	 * @return string
	 * @since 8.1.0
	 */
	public function getDisplayName();

	/**
	 * start receiving chunks from a file. This is the place where you can
	 * perform some initial step before starting encrypting/decrypting the
	 * chunks
	 *
	 * @param string $path to the file
	 * @param string $user who read/write the file (null for public access)
	 * @param string $mode php stream open mode
	 * @param array $header contains the header data read from the file
	 * @param array $accessList who has access to the file contains the key 'users' and 'public'
	 *
	 * $return array $header contain data as key-value pairs which should be
	 *                       written to the header, in case of a write operation
	 *                       or if no additional data is needed return a empty array
	 * @since 8.1.0
	 */
	public function begin($path, $user, $mode, array $header, array $accessList);

	/**
	 * last chunk received. This is the place where you can perform some final
	 * operation and return some remaining data if something is left in your
	 * buffer.
	 *
	 * @param string $path to the file
	 * @return string remained data which should be written to the file in case
	 *                of a write operation
	 * @since 8.1.0
	 */
	public function end($path);

	/**
	 * encrypt data
	 *
	 * @param string $data you want to encrypt
	 * @return mixed encrypted data
	 * @since 8.1.0
	 */
	public function encrypt($data);

	/**
	 * decrypt data
	 *
	 * @param string $data you want to decrypt
	 * @return mixed decrypted data
	 * @since 8.1.0
	 */
	public function decrypt($data);

	/**
	 * update encrypted file, e.g. give additional users access to the file
	 *
	 * @param string $path path to the file which should be updated
	 * @param string $uid of the user who performs the operation
	 * @param array $accessList who has access to the file contains the key 'users' and 'public'
	 * @return boolean
	 * @since 8.1.0
	 */
	public function update($path, $uid, array $accessList);

	/**
	 * should the file be encrypted or not
	 *
	 * @param string $path
	 * @return boolean
	 * @since 8.1.0
	 */
	public function shouldEncrypt($path);

	/**
	 * get size of the unencrypted payload per block.
	 * ownCloud read/write files with a block size of 8192 byte
	 *
	 * @return integer
	 * @since 8.1.0
	 */
	public function getUnencryptedBlockSize();

	/**
	 * check if the encryption module is able to read the file,
	 * e.g. if all encryption keys exists
	 *
	 * @param string $path
	 * @param string $uid user for whom we want to check if he can read the file
	 * @return boolean
	 * @since 8.1.0
	 */
	public function isReadable($path, $uid);

}
