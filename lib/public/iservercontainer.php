<?php
namespace OCP;


/**
 * Class IServerContainer
 * @package OCP
 *
 * This container holds all vanedisk services
 * @since 6.0.0
 */
interface IServerContainer {

	/**
	 * The contacts manager will act as a broker between consumers for contacts information and
	 * providers which actual deliver the contact information.
	 *
	 * @return \OCP\Contacts\IManager
	 * @since 6.0.0
	 */
	public function getContactsManager();

	/**
	 * The current request object holding all information about the request currently being processed
	 * is returned from this method.
	 * In case the current execution was not initiated by a web request null is returned
	 *
	 * @return \OCP\IRequest
	 * @since 6.0.0
	 */
	public function getRequest();

	/**
	 * Returns the preview manager which can create preview images for a given file
	 *
	 * @return \OCP\IPreview
	 * @since 6.0.0
	 */
	public function getPreviewManager();

	/**
	 * Returns the tag manager which can get and set tags for different object types
	 *
	 * @see \OCP\ITagManager::load()
	 * @return \OCP\ITagManager
	 * @since 6.0.0
	 */
	public function getTagManager();

	/**
	 * Returns the root folder of vanedisk's data directory
	 *
	 * @return \OCP\Files\IRootFolder
	 * @since 6.0.0 - between 6.0.0 and 8.0.0 this returned \OCP\Files\Folder
	 */
	public function getRootFolder();

	/**
	 * Returns a view to vanedisk's files folder
	 *
	 * @param string $userId user ID
	 * @return \OCP\Files\Folder
	 * @since 6.0.0 - parameter $userId was added in 8.0.0
	 */
	public function getUserFolder($userId = null);

	/**
	 * Returns an app-specific view in vanedisk's data directory
	 *
	 * @return \OCP\Files\Folder
	 * @since 6.0.0
	 */
	public function getAppFolder();

	/**
	 * Returns a user manager
	 *
	 * @return \OCP\IUserManager
	 * @since 8.0.0
	 */
	public function getUserManager();

	/**
	 * Returns a group manager
	 *
	 * @return \OCP\IGroupManager
	 * @since 8.0.0
	 */
	public function getGroupManager();

	/**
	 * Returns the user session
	 *
	 * @return \OCP\IUserSession
	 * @since 6.0.0
	 */
	public function getUserSession();

	/**
	 * Returns the navigation manager
	 *
	 * @return \OCP\INavigationManager
	 * @since 6.0.0
	 */
	public function getNavigationManager();

	/**
	 * Returns the config manager
	 *
	 * @return \OCP\IConfig
	 * @since 6.0.0
	 */
	public function getConfig();

	/**
	 * Returns a Crypto instance
	 *
	 * @return \OCP\Security\ICrypto
	 * @since 8.0.0
	 */
	public function getCrypto();

	/**
	 * Returns a Hasher instance
	 *
	 * @return \OCP\Security\IHasher
	 * @since 8.0.0
	 */
	public function getHasher();

	/**
	 * Returns a SecureRandom instance
	 *
	 * @return \OCP\Security\ISecureRandom
	 * @since 8.1.0
	 */
	public function getSecureRandom();

	/**
	 * Returns an instance of the db facade
	 * @deprecated 8.1.0 use getDatabaseConnection
	 * @return \OCP\IDb
	 * @since 7.0.0
	 */
	public function getDb();

	/**
	 * Returns the app config manager
	 *
	 * @return \OCP\IAppConfig
	 * @since 7.0.0
	 */
	public function getAppConfig();

	/**
	 * get an L10N instance
	 * @param string $app appid
	 * @param string $lang
	 * @return \OCP\IL10N
	 * @since 6.0.0 - parameter $lang was added in 8.0.0
	 */
	public function getL10N($app, $lang = null);

	/**
	 * @return \OC\Encryption\Manager
	 * @since 8.1.0
	 */
	public function getEncryptionManager();

	/**
	 * @return \OC\Encryption\File
	 * @since 8.1.0
	 */
	public function getEncryptionFilesHelper();

	/**
	 * @return \OCP\Encryption\Keys\IStorage
	 * @since 8.1.0
	 */
	public function getEncryptionKeyStorage();

	/**
	 * Returns the URL generator
	 *
	 * @return \OCP\IURLGenerator
	 * @since 6.0.0
	 */
	public function getURLGenerator();

	/**
	 * Returns the Helper
	 *
	 * @return \OCP\IHelper
	 * @since 6.0.0
	 */
	public function getHelper();

	/**
	 * Returns an ICache instance
	 *
	 * @return \OCP\ICache
	 * @since 6.0.0
	 */
	public function getCache();

	/**
	 * Returns an \OCP\CacheFactory instance
	 *
	 * @return \OCP\ICacheFactory
	 * @since 7.0.0
	 */
	public function getMemCacheFactory();

	/**
	 * Returns the current session
	 *
	 * @return \OCP\ISession
	 * @since 6.0.0
	 */
	public function getSession();

	/**
	 * Returns the activity manager
	 *
	 * @return \OCP\Activity\IManager
	 * @since 6.0.0
	 */
	public function getActivityManager();

	/**
	 * Returns the current session
	 *
	 * @return \OCP\IDBConnection
	 * @since 6.0.0
	 */
	public function getDatabaseConnection();

	/**
	 * Returns an avatar manager, used for avatar functionality
	 *
	 * @return \OCP\IAvatarManager
	 * @since 6.0.0
	 */
	public function getAvatarManager();

	/**
	 * Returns an job list for controlling background jobs
	 *
	 * @return \OCP\BackgroundJob\IJobList
	 * @since 7.0.0
	 */
	public function getJobList();

	/**
	 * Returns a logger instance
	 *
	 * @return \OCP\ILogger
	 * @since 8.0.0
	 */
	public function getLogger();

	/**
	 * Returns a router for generating and matching urls
	 *
	 * @return \OCP\Route\IRouter
	 * @since 7.0.0
	 */
	public function getRouter();

	/**
	 * Returns a search instance
	 *
	 * @return \OCP\ISearch
	 * @since 7.0.0
	 */
	public function getSearch();

	/**
	 * Get the certificate manager for the user
	 *
	 * @param string $userId (optional) if not specified the current loggedin user is used
	 * @return \OCP\ICertificateManager | null if $userId is null and no user is logged in
	 * @since 8.0.0
	 */
	public function getCertificateManager($userId = null);

	/**
	 * Create a new event source
	 *
	 * @return \OCP\IEventSource
	 * @since 8.0.0
	 */
	public function createEventSource();

	/**
	 * Returns an instance of the HTTP helper class
	 * @return \OC\HTTPHelper
	 * @deprecated 8.1.0 Use \OCP\Http\Client\IClientService
	 * @since 8.0.0
	 */
	public function getHTTPHelper();

	/**
	 * Returns an instance of the HTTP client service
	 *
	 * @return \OCP\Http\Client\IClientService
	 * @since 8.1.0
	 */
	public function getHTTPClientService();

	/**
	 * Get the active event logger
	 *
	 * @return \OCP\Diagnostics\IEventLogger
	 * @since 8.0.0
	 */
	public function getEventLogger();

	/**
	 * Get the active query logger
	 *
	 * The returned logger only logs data when debug mode is enabled
	 *
	 * @return \OCP\Diagnostics\IQueryLogger
	 * @since 8.0.0
	 */
	public function getQueryLogger();

	/**
	 * Get the manager for temporary files and folders
	 *
	 * @return \OCP\ITempManager
	 * @since 8.0.0
	 */
	public function getTempManager();

	/**
	 * Get the app manager
	 *
	 * @return \OCP\App\IAppManager
	 * @since 8.0.0
	 */
	public function getAppManager();

	/**
	 * Get the webroot
	 *
	 * @return string
	 * @since 8.0.0
	 */
	public function getWebRoot();

	/**
	 * @return \OCP\Files\Config\IMountProviderCollection
	 * @since 8.0.0
	 */
	public function getMountProviderCollection();

	/**
	 * Get the IniWrapper
	 *
	 * @return \bantu\IniGetWrapper\IniGetWrapper
	 * @since 8.0.0
	 */
	public function getIniWrapper();
	/**
	 * @return \OCP\Command\IBus
	 * @since 8.1.0
	 */
	public function getCommandBus();

	/**
	 * Creates a new mailer
	 *
	 * @return \OCP\Mail\IMailer
	 * @since 8.1.0
	 */
	public function getMailer();

	/**
	 * Get the locking provider
	 *
	 * @return \OCP\Lock\ILockingProvider
	 * @since 8.1.0
	 */
	public function getLockingProvider();
}
