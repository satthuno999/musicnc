<?php declare(strict_types=1);
namespace OCA\MusicNC\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method setUserId(string $userId)
 * @method string getToken()
 * @method setToken(string $token)
 * @method int getExpiry()
 * @method setExpiry(int $expiry)
 */
class AmpacheSession extends Entity {
	public $userId;
	public $token;
	public $expiry;

	public function __construct() {
		$this->addType('expiry', 'int');
	}
}
