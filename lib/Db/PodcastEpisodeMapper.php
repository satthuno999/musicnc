<?php declare(strict_types=1);


namespace OCA\MusicNC\Db;

use OCP\IDBConnection;

/**
 * Type hint a base class methdo to help Scrutinizer
 * @method PodcastEpisode updateOrInsert(PodcastEpisode $episode)
 * @phpstan-extends BaseMapper<PodcastEpisode>
 */
class PodcastEpisodeMapper extends BaseMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'music_podcast_episodes', PodcastEpisode::class, 'title');
	}

	/**
	 * @param int[] $channelIds
	 * @return PodcastEpisode[]
	 */
	public function findAllByChannel(array $channelIds, string $userId, ?int $limit=null, ?int $offset=null) : array {
		$channelCount = \count($channelIds);
		if ($channelCount === 0) {
			return [];
		} else {
			$condition = '`channel_id` IN ' . $this->questionMarks($channelCount);
			$sorting = 'ORDER BY `id` DESC';
			$sql = $this->selectUserEntities($condition, $sorting);
			return $this->findEntities($sql, \array_merge([$userId], $channelIds), $limit, $offset);
		}
	}

	public function deleteByChannel(int $channelId, string $userId) : void {
		$this->deleteByCond('`channel_id` = ? AND `user_id` = ?', [$channelId, $userId]);
	}

	public function deleteByChannelExcluding(int $channelId, array $excludedIds, string $userId) : void {
		$excludeCount = \count($excludedIds);
		if ($excludeCount === 0) {
			$this->deleteByChannel($channelId, $userId);
		} else {
			$this->deleteByCond(
				'`channel_id` = ? AND `user_id` = ? AND `id` NOT IN ' . $this->questionMarks($excludeCount),
				\array_merge([$channelId, $userId], $excludedIds)
			);
		}
	}

	/**
	 * @see \OCA\MusicNC\Db\BaseMapper::findUniqueEntity()
	 * @param PodcastEpisode $episode
	 * @return PodcastEpisode
	 */
	protected function findUniqueEntity(Entity $episode) : Entity {
		$sql = $this->selectUserEntities("`guid_hash` = ? AND `channel_id` = ?");
		return $this->findEntity($sql, [$episode->getUserId(), $episode->getGuidHash(), $episode->getChannelId()]);
	}
}
