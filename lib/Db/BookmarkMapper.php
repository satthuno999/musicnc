<?php declare(strict_types=1);
namespace OCA\MusicNC\Db;

use OCP\IDBConnection;

/**
 * Type hint a base class methdo to help Scrutinizer
 * @method Bookmark findEntity(string $sql, array $params=[], ?int $limit=null, ?int $offset=null)
 * @phpstan-extends BaseMapper<Bookmark>
 */
class BookmarkMapper extends BaseMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'music_bookmarks', Bookmark::class, 'comment');
	}

	public function findByEntry(int $type, int $entryId, string $userId) : Bookmark {
		$sql = $this->selectUserEntities("`type` = ? AND `entry_id` = ?");
		return $this->findEntity($sql, [$userId, $type, $entryId]);
	}

	/**
	 * @see \OCA\MusicNC\Db\BaseMapper::findUniqueEntity()
	 * @param Bookmark $bookmark
	 * @return Bookmark
	 */
	protected function findUniqueEntity(Entity $bookmark) : Entity {
		assert($bookmark instanceof Bookmark);
		return $this->findByEntry($bookmark->getType(), $bookmark->getEntryId(), $bookmark->getUserId());
	}
}
