<?php

/**
 * ownCloud - Music app
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Pauli Järvinen <pauli.jarvinen@gmail.com>
 * @copyright Morris Jobke 2013, 2014
 * @copyright Pauli Järvinen 2016 - 2021
 */

namespace OCA\MusicNC\Controller;

use \OCA\MusicNC\Tests\Utility\ControllerTestUtility;
use \OCP\AppFramework\Http\JSONResponse;

use OCA\MusicNC\DB\Track;

class ApiControllerTest extends ControllerTestUtility {
	private $trackBusinessLayer;
	private $artistBusinessLayer;
	private $albumBusinessLayer;
	private $collectionHelper;
	private $request;
	private $controller;
	private $userId = 'john';
	private $appname = 'Musicnc';
	private $urlGenerator;
	private $l10n;
	private $scanner;
	private $coverHelper;
	private $detailsHelper;
	private $lastfmService;
	private $maintenance;
	private $userMusicFolder;
	private $userFolder;
	private $logger;

	protected function setUp() : void {
		$this->request = $this->getMockBuilder('\OCP\IRequest')
			->disableOriginalConstructor()
			->getMock();
		$this->urlGenerator = $this->getMockBuilder('\OCP\IURLGenerator')
			->disableOriginalConstructor()
			->getMock();
		$this->urlGenerator
			->method('linkToRoute')
			->will($this->returnCallback([$this, 'linkToRouteMock']));
		$this->l10n = $this->getMockBuilder('\OCP\IL10N')
			->disableOriginalConstructor()
			->getMock();
		$this->userFolder = $this->getMockBuilder('\OCP\Files\Folder')
			->disableOriginalConstructor()
			->getMock();
		$this->trackBusinessLayer = $this->getMockBuilder('\OCA\MusicNC\BusinessLayer\TrackBusinessLayer')
			->disableOriginalConstructor()
			->getMock();
		$this->artistBusinessLayer = $this->getMockBuilder('\OCA\MusicNC\BusinessLayer\ArtistBusinessLayer')
			->disableOriginalConstructor()
			->getMock();
		$this->albumBusinessLayer = $this->getMockBuilder('\OCA\MusicNC\BusinessLayer\AlbumBusinessLayer')
			->disableOriginalConstructor()
			->getMock();
		$this->genreBusinessLayer = $this->getMockBuilder('\OCA\MusicNC\BusinessLayer\GenreBusinessLayer')
			->disableOriginalConstructor()
			->getMock();
		$this->scanner = $this->getMockBuilder('\OCA\MusicNC\Utility\Scanner')
			->disableOriginalConstructor()
			->getMock();
		$this->collectionHelper = $this->getMockBuilder('\OCA\MusicNC\Utility\CollectionHelper')
			->disableOriginalConstructor()
			->getMock();
		$this->coverHelper = $this->getMockBuilder('\OCA\MusicNC\Utility\CoverHelper')
			->disableOriginalConstructor()
			->getMock();
		$this->detailsHelper = $this->getMockBuilder('\OCA\MusicNC\Utility\DetailsHelper')
			->disableOriginalConstructor()
			->getMock();
		$this->lastfmService = $this->getMockBuilder('\OCA\MusicNC\Utility\LastfmService')
			->disableOriginalConstructor()
			->getMock();
		$this->maintenance = $this->getMockBuilder('\OCA\MusicNC\Db\Maintenance')
			->disableOriginalConstructor()
			->getMock();
		$this->librarySettings = $this->getMockBuilder('\OCA\MusicNC\Utility\LibrarySettings')
			->disableOriginalConstructor()
			->getMock();
		$this->logger = $this->getMockBuilder('\OCA\MusicNC\AppFramework\Core\Logger')
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new ApiController(
			$this->appname,
			$this->request,
			$this->urlGenerator,
			$this->trackBusinessLayer,
			$this->artistBusinessLayer,
			$this->albumBusinessLayer,
			$this->genreBusinessLayer,
			$this->scanner,
			$this->collectionHelper,
			$this->coverHelper,
			$this->detailsHelper,
			$this->lastfmService,
			$this->maintenance,
			$this->librarySettings,
			$this->userId,
			$this->l10n,
			$this->userFolder,
			$this->logger);
	}

	public function testTrackByFileId() {
		$trackId = 1;
		$fileId = 3;

		$track = new Track();
		$track->setId($trackId);
		$track->setTitle('The title');
		$track->setArtistId(3);
		$track->setArtistName('The track artist');
		$track->setAlbumId(1);
		$track->setNumber(4);
		$track->setDisk(1);
		$track->setLength(123);
		$track->setFileId($fileId);
		$track->setMimetype('audio/mp3');
		$track->setBitrate(123);

		$this->trackBusinessLayer->expects($this->once())
			->method('findByFileId')
			->with($this->equalTo($fileId), $this->equalTo($this->userId))
			->will($this->returnValue($track));

		$result = [
			'title' => 'The title',
			'artistName' => 'The track artist',
			'id' => 1,
			'number' => 4,
			'disk' => 1,
			'artistId' => 3,
			'length' => 123,
			'files' => [
				'audio/mp3' => $fileId
			]
		];

		$response = $this->controller->trackByFileId($fileId);

		$this->assertEquals($result, $response->getData());
		$this->assertTrue($response instanceof JSONResponse);
	}

}
