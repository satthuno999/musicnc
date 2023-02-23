<?php declare(strict_types=1);



namespace OCA\MusicNC\Db;

use OCP\IDBConnection;

/**
 * Type hint a base class methdo to help Scrutinizer
 * @method PodcastChannel insert(PodcastChannel $channel)
 * @phpstan-extends BaseMapper<PodcastChannel>
 */
class PodcastChannelMapper extends BaseMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'music_podcast_channels', PodcastChannel::class, 'title');
	}

	/**
	 * @return int[]
	 */
	public function findAllIdsWithNoUpdateSince(string $userId, \DateTime $timeLimit) : array {
		$sql = "SELECT `id` FROM `{$this->getTableName()}` WHERE `user_id` = ? AND `update_checked` < ?";
		$result = $this->execute($sql, [$userId, $timeLimit->format(BaseMapper::SQL_DATE_FORMAT)]);

		return \array_map('intval', $result->fetchAll(\PDO::FETCH_COLUMN));
	}

	/**
	 * @see \OCA\MusicNC\Db\BaseMapper::findUniqueEntity()
	 * @param PodcastChannel $channel
	 * @return PodcastChannel
	 */
	protected function findUniqueEntity(Entity $channel) : Entity {
		$sql = $this->selectUserEntities("`rss_hash` = ?");
		return $this->findEntity($sql, [$channel->getUserId(), $channel->getRssHash()]);
	}
}
