<?php declare(strict_types=1);


namespace OCA\MusicNC\Db;

use OCP\IDBConnection;

/**
 * @method RadioStation findEntity(string $sql, array $params)
 * @phpstan-extends BaseMapper<RadioStation>
 */
class RadioStationMapper extends BaseMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'music_radio_stations', RadioStation::class, 'name');
	}

	/**
	 * @return RadioStation
	 */
	public function findByStreamUrl(string $url, string $userId) : RadioStation {
		$sql = $this->selectUserEntities("`stream_url` = ?");
		return $this->findEntity($sql, [$userId, $url]);
	}

	/**
	 * @see \OCA\MusicNC\Db\BaseMapper::findUniqueEntity()
	 */
	protected function findUniqueEntity(Entity $station) : Entity {
		// The radio_stations table has no unique constraints, and hence, this function
		// should never be called.
		throw new \BadMethodCallException('not supported');
	}
}
