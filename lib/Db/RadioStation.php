<?php declare(strict_types=1);



namespace OCA\MusicNC\Db;

/**
 * @method string getName()
 * @method void setName(string $name)
 * @method string getStreamUrl()
 * @method setStreamUrl(string $url)
 * @method string getHomeUrl()
 * @method setHomeUrl(string $url)
 */
class RadioStation extends Entity {
	public $name;
	public $streamUrl;
	public $homeUrl;

	public function toApi() : array {
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'stream_url' => $this->getStreamUrl(),
			'home_url' => $this->getHomeUrl(),
			'created' => $this->getCreated(),
			'updated' => $this->getUpdated()
		];
	}
}
