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
 * @copyright Pauli Järvinen 2017 - 2021
 */

namespace OCA\MusicNC\BusinessLayer;

use OCA\MusicNC\AppFramework\BusinessLayer\BusinessLayer;
use OCA\MusicNC\AppFramework\Core\Logger;

use OCA\MusicNC\Db\Artist;
use OCA\MusicNC\Db\ArtistMapper;
use OCA\MusicNC\Db\MatchMode;
use OCA\MusicNC\Db\SortBy;

use OCA\MusicNC\Utility\Util;

use OCP\IL10N;
use OCP\Files\File;

/**
 * Base class functions with the actually used inherited types to help IDE and Scrutinizer:
 * @method Artist find(int $trackId, string $userId)
 * @method Artist[] findAll(string $userId, int $sortBy=SortBy::None, int $limit=null, int $offset=null)
 * @method Artist[] findAllByName(?string $name, string $userId, int $matchMode=MatchMode::Exact, int $limit=null, int $offset=null)
 * @method Artist[] findById(int[] $ids, string $userId=null, bool $preserveOrder=false)
 * @phpstan-extends BusinessLayer<Artist>
 */
class ArtistBusinessLayer extends BusinessLayer {
	protected $mapper; // eclipse the definition from the base class, to help IDE and Scrutinizer to know the actual type
	private $logger;

	private const FORBIDDEN_CHARS_IN_FILE_NAME = '<>:"/\|?*'; // chars forbidden in Windows, on Linux only '/' is technically forbidden

	public function __construct(ArtistMapper $artistMapper, Logger $logger) {
		parent::__construct($artistMapper);
		$this->mapper = $artistMapper;
		$this->logger = $logger;
	}

	/**
	 * Finds all artists who have at least one album
	 * @param string $userId the name of the user
	 * @param integer $sortBy sort order of the result set
	 * @return Artist[] artists
	 */
	public function findAllHavingAlbums(string $userId, int $sortBy=SortBy::None) : array {
		return $this->mapper->findAllHavingAlbums($userId, $sortBy);
	}

	/**
	 * Returns all artists filtered by genre
	 * @param int $genreId the genre to include
	 * @param string $userId the name of the user
	 * @param int|null $limit
	 * @param int|null $offset
	 * @return Artist[] artists
	 */
	public function findAllByGenre(int $genreId, string $userId, ?int $limit=null, ?int $offset=null) : array {
		return $this->mapper->findAllByGenre($genreId, $userId, $limit, $offset);
	}

	/**
	 * Find most frequently played artists, judged by the total play count of the contained tracks
	 * @return Artist[]
	 */
	public function findFrequentPlay(string $userId, ?int $limit=null, ?int $offset=null) : array {
		$countsPerArtist = $this->mapper->getArtistTracksPlayCount($userId, $limit, $offset);
		$ids = \array_keys($countsPerArtist);
		return $this->findById($ids, $userId, /*preserveOrder=*/true);
	}

	/**
	 * Find most recently played artists
	 * @return Artist[]
	 */
	public function findRecentPlay(string $userId, ?int $limit=null, ?int $offset=null) : array {
		$playTimePerArtist = $this->mapper->getLatestArtistPlayTimes($userId, $limit, $offset);
		$ids = \array_keys($playTimePerArtist);
		return $this->findById($ids, $userId, /*preserveOrder=*/true);
	}

	/**
	 * Find least recently played artists
	 * @return Artist[]
	 */
	public function findNotRecentPlay(string $userId, ?int $limit=null, ?int $offset=null) : array {
		$playTimePerArtist = $this->mapper->getFurthestArtistPlayTimes($userId, $limit, $offset);
		$ids = \array_keys($playTimePerArtist);
		return $this->findById($ids, $userId, /*preserveOrder=*/true);
	}

	/**
	 * Adds an artist if it does not exist already or updates an existing artist
	 * @param string|null $name the name of the artist
	 * @param string $userId the name of the user
	 * @return Artist The added/updated artist
	 */
	public function addOrUpdateArtist(?string $name, string $userId) : Artist {
		$artist = new Artist();
		$artist->setName(Util::truncate($name, 256)); // some DB setups can't truncate automatically to column max size
		$artist->setUserId($userId);
		$artist->setHash(\hash('md5', \mb_strtolower($name ?? '')));
		return $this->mapper->updateOrInsert($artist);
	}

	/**
	 * Use the given file as cover art for an artist if there exists an artist
	 * with name matching the file name.
	 * @param File $imageFile
	 * @param string $userId
	 * @return int[] IDs of the modified artists; usually there should be 0 or 1 of these but
	 *					in some special occasions there could be more
	 */
	public function updateCover(File $imageFile, string $userId, IL10N $l10n) : array {
		$name = \pathinfo($imageFile->getName(), PATHINFO_FILENAME);
		\assert(\is_string($name)); // for scrutinizer

		$matches = $this->findAllByNameMatchingFilename($name, $userId, $l10n);

		$artistIds = [];
		foreach ($matches as $artist) {
			$artist->setCoverFileId($imageFile->getId());
			$this->mapper->update($artist);
			$artistIds[] = $artist->getId();
		}

		return $artistIds;
	}

	/**
	 * Match the given files by file name to the artist names. If there is a matching
	 * artist with no cover image already set, the matched file is set to be used as
	 * cover for this artist.
	 * @param File[] $imageFiles
	 * @param string $userId
	 * @return bool true if any artist covers were updated; false otherwise
	 */
	public function updateCovers(array $imageFiles, string $userId, IL10N $l10n) : bool {
		$updated = false;

		// Construct a lookup table for the images as there may potentially be
		// a huge amount of them. Any of the characters forbidden in Windows file names
		// may be replaced with an underscore, which is taken into account when building
		// the LUT.
		$replacedChars = \str_split(self::FORBIDDEN_CHARS_IN_FILE_NAME);
		\assert(\is_array($replacedChars)); // for scrutinizer
		$imageLut = [];
		foreach ($imageFiles as $imageFile) {
			$imageName = \pathinfo($imageFile->getName(), PATHINFO_FILENAME);
			$lookupName = \str_replace($replacedChars, '_', $imageName);
			$imageLut[$lookupName][] = ['name' => $imageName, 'file' => $imageFile];
		}

		$artists = $this->findAll($userId);

		foreach ($artists as $artist) {
			if ($artist->getCoverFileId() === null) {
				$artistLookupName = \str_replace($replacedChars, '_', $artist->getNameString($l10n));
				$lutEntries = $imageLut[$artistLookupName] ?? [];
				foreach ($lutEntries as $lutEntry) {
					if (self::filenameMatchesArtist($lutEntry['name'], $artist, $l10n)) {
						$artist->setCoverFileId($lutEntry['file']->getId());
						$this->mapper->update($artist);
						$updated = true;
						break;
					}
				}
			}
		}

		return $updated;
	}

	/**
	 * removes the given cover art files from artists
	 * @param integer[] $coverFileIds the file IDs of the cover images
	 * @param string[]|null $userIds the users whose music library is targeted; all users are targeted if omitted
	 * @return Artist[] artists which got modified, empty array if none
	 */
	public function removeCovers(array $coverFileIds, ?array $userIds=null) : array {
		return $this->mapper->removeCovers($coverFileIds, $userIds);
	}

	/**
	 * Find artists by name so that the characters forbidden on Windows file system are allowed to be
	 * replaced with underscores. In Linux, '/' would be the only truly forbidden character in paths
	 * but using characters forbidden in Windows might cause difficulties with interoperability.
	 * Support also finding by the localized "Unknown artist" string.
	 */
	private function findAllByNameMatchingFilename(string $name, string $userId, IL10N $l10n) : array {
		// we want to make '_' match any forbidden character on Linux or Windows but '%' in the
		// search pattern should not be handled as a wildcard but as literal
		$name = \str_replace('%', '\%', $name);

		$potentialMatches = $this->findAllByName($name, $userId, MatchMode::Wildcards);

		$matches = \array_filter($potentialMatches, function(Artist $artist) use ($name, $l10n) : bool {
			return self::filenameMatchesArtist($name, $artist, $l10n);
		});

		if ($name == Artist::unknownNameString($l10n)) {
			$matches = \array_merge($matches, $this->findAllByName(null, $userId));
		}

		return $matches;
	}

	/**
	 * Check if the given file name matches the given artist. The file name should be given without the extension.
	 */
	private static function filenameMatchesArtist(string $filename, Artist $artist, IL10N $l10n) : bool {
		$length = \strlen($filename);
		$artistName = $artist->getNameString($l10n);
		if ($length !== \strlen($artistName)) {
			return false;
		} elseif ($filename == $artistName) {
			return true; // exact match
		} else {
			// iterate over all the bytes and require that all the other bytes are equal but
			// underscores are allowed to match any forbidden filesystem chracter
			$matchedChars = self::FORBIDDEN_CHARS_IN_FILE_NAME . '_';
			for ($i = 0; $i < $length; ++$i) {
				if ($filename[$i] === '_') {
					if (\strpos($matchedChars, $artistName[$i]) === false) {
						return false;
					}
				} elseif ($filename[$i] !== $artistName[$i]) {
					return false;
				}
			}
			return true;
		}
	}
}
