<?php declare(strict_types=1);
namespace OCA\MusicNC\Migration;

use OCP\IConfig;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

class TimestampInit implements IRepairStep {

	/** @var IDBConnection */
	private $db;

	/** @var IConfig */
	private $config;

	public function __construct(IDBConnection $connection, IConfig $config) {
		$this->db = $connection;
		$this->config = $config;
	}

	public function getName() {
		return 'Set creation and update dates for the library entities without one';
	}

	/**
	 * @inheritdoc
	 */
	public function run(IOutput $output) {
		$installedVersion = $this->config->getAppValue('musicnc', 'installed_version');

		if (\version_compare($installedVersion, '0.18.0', '<')) {
			$now = new \DateTime();
			$timestamp = $now->format('Y-m-d H:i:s');

			$tables = ['albums', 'artists', 'genres', 'playlists', 'tracks'];

			foreach ($tables as $tableShortName) {
				$n = $this->setCreated("*PREFIX*music_$tableShortName", $timestamp);
				$m = $this->setUpdated("*PREFIX*music_$tableShortName", $timestamp);
				$output->info("This date was added as creation date for $n and as update date for $m $tableShortName");
			}
		}
	}

	private function setCreated(string $table, string $timestamp) {
		$sql = "UPDATE `$table` SET `created` = ? WHERE `created` IS NULL";
		return $this->db->executeUpdate($sql, [$timestamp]);
	}

	private function setUpdated(string $table, string $timestamp) {
		$sql = "UPDATE `$table` SET `updated` = ? WHERE `updated` IS NULL";
		return $this->db->executeUpdate($sql, [$timestamp]);
	}
}