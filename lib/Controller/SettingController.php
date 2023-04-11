<?php declare(strict_types=1);

/**
 * ownCloud - Music app
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Pauli Järvinen <pauli.jarvinen@gmail.com>
 * @copyright Morris Jobke 2013, 2014
 * @copyright Pauli Järvinen 2017 - 2023
 */

namespace OCA\MusicNC\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\Security\ISecureRandom;

use OCA\MusicNC\AppFramework\Core\Logger;
use OCA\MusicNC\Db\AmpacheUserMapper;
use OCA\MusicNC\Http\ErrorResponse;
use OCA\MusicNC\Utility\AppInfo;
use OCA\MusicNC\Utility\LibrarySettings;
use OCA\MusicNC\Utility\Scanner;
use OCA\MusicNC\Utility\Util;

class SettingController extends Controller {
	const DEFAULT_PASSWORD_LENGTH = 10;
	/* Character set without look-alike characters. Similar but even more stripped set would be found
	 * on Nextcloud as ISecureRandom::CHAR_HUMAN_READABLE but that's not availalbe on ownCloud. */
	const API_KEY_CHARSET = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

	private $ampacheUserMapper;
	private $scanner;
	private $userId;
	private $librarySettings;
	private $secureRandom;
	private $urlGenerator;
	private $logger;

	public function __construct(string $appName,
								IRequest $request,
								AmpacheUserMapper $ampacheUserMapper,
								Scanner $scanner,
								?string $userId,
								LibrarySettings $librarySettings,
								ISecureRandom $secureRandom,
								IURLGenerator $urlGenerator,
								Logger $logger) {
		parent::__construct($appName, $request);

		$this->ampacheUserMapper = $ampacheUserMapper;
		$this->scanner = $scanner;
		$this->userId = $userId ?? ''; // ensure non-null to satisfy Scrutinizer; the null case should happen only when the user has already logged out
		$this->librarySettings = $librarySettings;
		$this->secureRandom = $secureRandom;
		$this->urlGenerator = $urlGenerator;
		$this->logger = $logger;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @UseSession to keep the session reserved while execution in progress
	 */
	public function userPath(string $value) {
		$prevPath = $this->librarySettings->getPath($this->userId);
		$success = $this->librarySettings->setPath($this->userId, $value);

		if ($success) {
			$this->scanner->updatePath($prevPath, $value, $this->userId);
		}

		return new JSONResponse(['success' => $success]);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function userExcludedPaths(array $value) {
		$success = $this->librarySettings->setExcludedPaths($this->userId, $value);
		return new JSONResponse(['success' => $success]);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function enableScanMetadata(bool $value) {
		$this->librarySettings->setScanMetadataEnabled($this->userId, $value);
		return new JSONResponse(['success' => true]);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function ignoredArticles(array $value) {
		$this->librarySettings->setIgnoredArticles($this->userId, $value);
		return new JSONResponse(['success' => true]);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getAll() {
		return [
			'path' => $this->librarySettings->getPath($this->userId),
			'excludedPaths' => $this->librarySettings->getExcludedPaths($this->userId),
			'scanMetadata' => $this->librarySettings->getScanMetadataEnabled($this->userId),
			'ignoredArticles' => $this->librarySettings->getIgnoredArticles($this->userId),
			'ampacheUrl' => $this->getAmpacheUrl(),
			'subsonicUrl' => $this->getSubsonicUrl(),
			'ampacheKeys' => $this->getUserKeys(),
			'appVersion' => AppInfo::getVersion(),
			'user' => $this->userId
		];
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getUserKeys() {
		return $this->ampacheUserMapper->getAll($this->userId);
	}

	private function getAmpacheUrl() {
		return \str_replace('/server/xml.server.php', '',
				$this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('musicnc.ampache.xmlApi')));
	}

	private function getSubsonicUrl() {
		return \str_replace('/rest/dummy', '',
				$this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute(
						'musicnc.subsonic.handleRequest', ['method' => 'dummy'])));
	}

	private function storeUserKey($description, $password) {
		$hash = \hash('sha256', $password);
		$description = Util::truncate($description, 64); // some DB setups can't truncate automatically to column max size
		return $this->ampacheUserMapper->addUserKey($this->userId, $hash, $description);
	}

	/**
	 * @NoAdminRequired
	 */
	public function createUserKey($length, $description) {
		if ($length == null || $length < self::DEFAULT_PASSWORD_LENGTH) {
			$length = self::DEFAULT_PASSWORD_LENGTH;
		}

		$password = $this->secureRandom->generate($length, self::API_KEY_CHARSET);

		$id = $this->storeUserKey($description, $password);

		if ($id === null) {
			return new ErrorResponse(Http::STATUS_INTERNAL_SERVER_ERROR, 'Error while saving the credentials');
		}

		return new JSONResponse(['id' => $id, 'password' => $password, 'description' => $description], Http::STATUS_CREATED);
	}

	/**
	 * The CORS-version of the key creation function is targeted for external clients. We need separate function
	 * because the CORS middleware blocks the normal internal access on Nextcloud versions older than 25 as well
	 * as on ownCloud 10.0, at least (but not on OC 10.4+).
	 *
	 * @NoAdminRequired
	 * @CORS
	 */
	public function createUserKeyCors($length, $description) {
		return $this->createUserKey($length, $description);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function removeUserKey($id) {
		$this->ampacheUserMapper->removeUserKey($this->userId, (int)$id);
		return new JSONResponse(['success' => true]);
	}
}
