<?php declare(strict_types=1);

namespace OCA\MusicNC\Db;

use OCP\IDBConnection;

/**
 * Note: Despite the name, this mapper and the related database table are
 *       used both for Subsonic and Ampache users. Also, this isn't really
 *       a mapper either, since this does not extend OCP\AppFramework\Db\Mapper.
 */
class AmpacheUserMapper {
	private $db;

	public function __construct(IDBConnection $db) {
		$this->db = $db;
	}

	/**
	 * @return string[]
	 */
	public function getPasswordHashes(string $userId) : array {
		$sql = 'SELECT `hash` FROM `*PREFIX*music_ampache_users` '.
			'WHERE `user_id` = ?';
		$params = [$userId];
		$result = $this->db->executeQuery($sql, $params);
		$rows = $result->fetchAll();

		$hashes = [];
		foreach ($rows as $value) {
			$hashes[] = $value['hash'];
		}

		return $hashes;
	}

	/**
	 * @return ?int ID of the added key on null on failure (which is highly unexpected)
	 */
	public function addUserKey(string $userId, string $hash, ?string $description) : ?int {
		$sql = 'INSERT INTO `*PREFIX*music_ampache_users` '.
			'(`user_id`, `hash`, `description`) VALUES (?, ?, ?)';
		$params = [$userId, $hash, $description];
		$this->db->executeUpdate($sql, $params);

		$sql = 'SELECT `id` FROM `*PREFIX*music_ampache_users` '.
				'WHERE `user_id` = ? AND `hash` = ?';
		$params = [$userId, $hash];
		$result = $this->db->executeQuery($sql, $params);
		$row = $result->fetch();

		if ($row === false) {
			return null;
		}

		return (int)$row['id'];
	}

	public function removeUserKey(string $userId, int $id) : void {
		$sql = 'DELETE FROM `*PREFIX*music_ampache_users` '.
				'WHERE `user_id` = ? AND `id` = ?';
		$params = [$userId, $id];
		$this->db->executeUpdate($sql, $params);
	}

	public function getAll(string $userId) : array {
		$sql = 'SELECT `id`, `hash`, `description` FROM `*PREFIX*music_ampache_users` '.
			'WHERE `user_id` = ?';
		$params = [$userId];
		$result = $this->db->executeQuery($sql, $params);
		$rows = $result->fetchAll();

		return $rows;
	}
}
