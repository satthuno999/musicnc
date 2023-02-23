<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Vũ Xuân Bình <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\MusicNC\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version010400Date20230224223000 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();
		$this->migrateMusicTracks($schema);
		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}

	//migrate table	
	private function migrateMusicTracks(ISchemaWrapper $schema) {
		$table = $schema->getTable('music_tracks');
		$this->setColumns($table, [
			[ 'play_count',		'integer',	['notnull' => true, 'unsigned' => true, 'default' => 0] ],
			[ 'last_played',	'datetime', ['notnull' => false] ]
		]);
	}
	//function ulities
	private function setColumn($table, string $name, string $type, array $args) {
		if (!$table->hasColumn($name)) {
			$table->addColumn($name, $type, $args);
		}
	}

	private function setColumns($table, array $nameTypeArgsPerCol) {
		foreach ($nameTypeArgsPerCol as $nameTypeArgs) {
			list($name, $type, $args) = $nameTypeArgs;
			$this->setColumn($table, $name, $type, $args);
		}
	}

}
