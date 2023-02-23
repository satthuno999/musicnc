<?php declare(strict_types=1);


namespace OCA\MusicNC\Db;

/**
 * Base class for all the entities belonging to the data model of the Music app
 * 
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getCreated()
 * @method setCreated(string $timestamp)
 * @method string getUpdated()
 * @method setUpdated(string $timestamp)
 */
class Entity extends \OCP\AppFramework\Db\Entity {
	public $userId;
	public $created;
	public $updated;
}
