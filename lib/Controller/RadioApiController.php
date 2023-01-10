<?php declare(strict_types=1);

/**
 * ownCloud - Music app
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Pauli Järvinen <pauli.jarvinen@gmail.com>
 * @copyright Pauli Järvinen 2020 - 2022
 */

namespace OCA\Music\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;

use OCP\Files\Folder;
use OCP\IConfig;
use OCP\IRequest;

use OCA\Music\AppFramework\BusinessLayer\BusinessLayerException;
use OCA\Music\AppFramework\Core\Logger;
use OCA\Music\BusinessLayer\RadioStationBusinessLayer;
use OCA\Music\Http\ErrorResponse;
use OCA\Music\Http\FileResponse;
use OCA\Music\Utility\HttpUtil;
use OCA\Music\Utility\PlaylistFileService;
use OCA\Music\Utility\Util;
use OCA\Music\Utility\RadioService;

class RadioApiController extends Controller {
	private $config;
	private $businessLayer;
	private $service;
	private $playlistFileService;
	private $userId;
	private $userFolder;
	private $logger;

	public function __construct(string $appname,
								IRequest $request,
								IConfig $config,
								RadioStationBusinessLayer $businessLayer,
								RadioService $service,
								PlaylistFileService $playlistFileService,
								?string $userId,
								?Folder $userFolder,
								Logger $logger) {
		parent::__construct($appname, $request);
		$this->config = $config;
		$this->businessLayer = $businessLayer;
		$this->service = $service;
		$this->playlistFileService = $playlistFileService;
		$this->userId = $userId ?? ''; // ensure non-null to satisfy Scrutinizer; may be null when resolveStreamUrl used on public share
		$this->userFolder = $userFolder;
		$this->logger = $logger;
	}

	/**
	 * lists all radio stations
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getAll() {
		$stations = $this->businessLayer->findAll($this->userId);
		return Util::arrayMapMethod($stations, 'toApi');
	}

	/**
	 * creates a station
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function create($name, $streamUrl, $homeUrl) {
		if ($streamUrl === null) {
			return new ErrorResponse(Http::STATUS_BAD_REQUEST, "Mandatory argument 'streamUrl' not given");
		} else {
			$station = $this->businessLayer->create($this->userId, $name, $streamUrl, $homeUrl);
			return $station->toApi();
		}
	}

	/**
	 * deletes a station
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function delete(int $id) {
		try {
			$this->businessLayer->delete($id, $this->userId);
			return [];
		} catch (BusinessLayerException $ex) {
			return new ErrorResponse(Http::STATUS_NOT_FOUND, $ex->getMessage());
		}
	}

	/**
	 * get a single radio station
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function get(int $id) {
		try {
			$station = $this->businessLayer->find($id, $this->userId);
			return $station->toAPI();
		} catch (BusinessLayerException $ex) {
			return new ErrorResponse(Http::STATUS_NOT_FOUND, $ex->getMessage());
		}
	}

	/**
	 * update a station
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function update(int $id, string $name = null, string $streamUrl = null, string $homeUrl = null) {
		if ($name === null && $streamUrl === null && $homeUrl === null) {
			return new ErrorResponse(Http::STATUS_BAD_REQUEST, "at least one of the args ['name', 'streamUrl', 'homrUrl'] must be given");
		}

		try {
			$station = $this->businessLayer->find($id, $this->userId);
			if ($name !== null) {
				$station->setName($name);
			}
			if ($streamUrl !== null) {
				$station->setStreamUrl($streamUrl);
			}
			if ($homeUrl !== null) {
				$station->setHomeUrl($homeUrl);
			}
			$this->businessLayer->update($station);

			return new JSONResponse($station->toApi());
		} catch (BusinessLayerException $ex) {
			return new ErrorResponse(Http::STATUS_NOT_FOUND, $ex->getMessage());
		}
	}

	/**
	 * export all radio stations to a file
	 *
	 * @param string $name target file name without the file extension
	 * @param string $path parent folder path
	 * @param string $oncollision action to take on file name collision,
	 *								supported values:
	 *								- 'overwrite' The existing file will be overwritten
	 *								- 'keepboth' The new file is named with a suffix to make it unique
	 *								- 'abort' (default) The operation will fail
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function exportAllToFile(string $name, string $path, string $oncollision) {
		if ($this->userFolder === null) {
			// This shouldn't get actually run. The folder may be null in case the user has already logged out.
			// But in that case, the framework should block the execution before it reaches here.
			return new ErrorResponse(Http::STATUS_UNAUTHORIZED, 'no valid user folder got');
		}
		try {
			$exportedFilePath = $this->playlistFileService->exportRadioStationsToFile(
					$this->userId, $this->userFolder, $path, $name . '.m3u8', $oncollision);
			return new JSONResponse(['wrote_to_file' => $exportedFilePath]);
		} catch (\OCP\Files\NotFoundException $ex) {
			return new ErrorResponse(Http::STATUS_NOT_FOUND, 'folder not found');
		} catch (\RuntimeException $ex) {
			return new ErrorResponse(Http::STATUS_CONFLICT, $ex->getMessage());
		} catch (\OCP\Files\NotPermittedException $ex) {
			return new ErrorResponse(Http::STATUS_FORBIDDEN, 'user is not allowed to write to the target file');
		}
	}

	/**
	 * import radio stations from a file
	 * @param string $filePath path of the file to import
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function importFromFile(string $filePath) {
		if ($this->userFolder === null) {
			// This shouldn't get actually run. The folder may be null in case the user has already logged out.
			// But in that case, the framework should block the execution before it reaches here.
			return new ErrorResponse(Http::STATUS_UNAUTHORIZED, 'no valid user folder got');
		}
		try {
			$result = $this->playlistFileService->importRadioStationsFromFile($this->userId, $this->userFolder, $filePath);
			$result['stations'] = Util::arrayMapMethod($result['stations'], 'toApi');
			return $result;
		} catch (\OCP\Files\NotFoundException $ex) {
			return new ErrorResponse(Http::STATUS_NOT_FOUND, 'playlist file not found');
		} catch (\UnexpectedValueException $ex) {
			return new ErrorResponse(Http::STATUS_UNSUPPORTED_MEDIA_TYPE, $ex->getMessage());
		}
	}

	/**
	 * reset all the radio stations of the user
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function resetAll() {
		$this->businessLayer->deleteAll($this->userId);
		return new JSONResponse(['success' => true]);
	}

	/**
	* get metadata for a channel
	*
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function getChannelInfo(int $id, ?string $type=null) {
		try {
			$station = $this->businessLayer->find($id, $this->userId);
			$streamUrl = $station->getStreamUrl();

			switch ($type) {
				case 'icy':
					$metadata = $this->service->readIcyMetadata($streamUrl, 3, 1);
					break;
				case 'shoutcast-v1':
					$metadata = $this->service->readShoutcastV1Metadata($streamUrl);
					break;
				case 'shoutcast-v2':
					$metadata = $this->service->readShoutcastV2Metadata($streamUrl);
					break;
				case 'icecast':
					$metadata = $this->service->readIcecastMetadata($streamUrl);
					break;
				default:
					$metadata = $this->service->readIcyMetadata($streamUrl, 3, 1)
							?? $this->service->readShoutcastV2Metadata($streamUrl)
							?? $this->service->readIcecastMetadata($streamUrl)
							?? $this->service->readShoutcastV1Metadata($streamUrl);
					break;
			}

			return new JSONResponse($metadata);
		} catch (BusinessLayerException $ex) {
			return new ErrorResponse(Http::STATUS_NOT_FOUND, $ex->getMessage());
		}
	}

	/**
	 * get stream URL for a radio station
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function stationStreamUrl(int $id) {
		try {
			$station = $this->businessLayer->find($id, $this->userId);
			$streamUrl = $station->getStreamUrl();
			return new JSONResponse($this->service->resolveStreamUrl($streamUrl));
		} catch (BusinessLayerException $ex) {
			return new ErrorResponse(Http::STATUS_NOT_FOUND, $ex->getMessage());
		}
	}

	/**
	 * get the actual stream URL from the given public URL
	 *
	 * Available without login since no user data is handled and this may be used on link-shared folder.
	 *
	 * @PublicPage
	 * @NoCSRFRequired
	 */
	public function resolveStreamUrl(string $url) {
		return new JSONResponse($this->service->resolveStreamUrl(\rawurldecode($url)));
	}

	/**
	 * get manifest of a HLS stream
	 *
	 * This fetches the manifest file from the given URL and returns a modified version of it.
	 * The front-end can't easily stream directly from the original source because of the Content-Security-Policy.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function hlsManifest(string $url) {
		if ($this->hlsEnabled()) {
			list('content' => $content, 'status_code' => $status, 'content_type' => $contentType)
				= $this->service->getHlsManifest(\rawurldecode($url));

			return new FileResponse([
				'content' => $content,
				'mimetype' => $contentType
			], $status);
		} else {
			return new ErrorResponse(Http::STATUS_FORBIDDEN, 'the cloud admin has disabled HLS streaming');
		}
	}

	/**
	 * get one segment of a HLS stream
	 *
	 * The segment is fetched from the given URL and relayed as such to the client.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function hlsSegment(string $url) {
		if ($this->hlsEnabled()) {
			list('content' => $content, 'status_code' => $status, 'content_type' => $contentType)
				= HttpUtil::loadFromUrl(\rawurldecode($url));

			return new FileResponse([
				'content' => $content,
				'mimetype' => $contentType ?? 'application/octet-stream'
			], $status);
		} else {
			return new ErrorResponse(Http::STATUS_FORBIDDEN, 'the cloud admin has disabled HLS streaming');
		}
	}

	private function hlsEnabled() : bool {
		return (bool)$this->config->getSystemValue('music.enable_radio_hls', true);
	}
}
