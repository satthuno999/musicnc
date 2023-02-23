<?php declare(strict_types=1);
namespace OCA\MusicNC\Db;

use OCA\MusicNC\Utility\Util;

/**
 * @method int getType()
 * @method void setType(int $type)
 * @method int getEntryId()
 * @method void setEntryId(int $entryId)
 * @method int getPosition()
 * @method void setPosition(int $position)
 * @method ?string getComment()
 * @method void setComment(?string $comment)
 */
class Bookmark extends Entity {
	public $type;
	public $entryId;
	public $position;
	public $comment;

	public const TYPE_TRACK = 1;
	public const TYPE_PODCAST_EPISODE = 2;

	public function __construct() {
		$this->addType('type', 'int');
		$this->addType('entryId', 'int');
		$this->addType('position', 'int');
	}

	public function toSubsonicApi() : array {
		return [
			'position' => $this->getPosition(),
			'username' => $this->userId,
			'comment' => $this->getComment() ?: '',
			'created' => Util::formatZuluDateTime($this->getCreated()),
			'changed' => Util::formatZuluDateTime($this->getUpdated())
		];
	}
}
