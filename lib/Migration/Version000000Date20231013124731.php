<?php

/**
 * Music nc
 *
 * @author spark
 * @copyright 2023 spark <>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace OCA\musicnc\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20181013124731 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('favorites_radio')) {
			$table = $schema->createTable('favorites');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('stationuuid', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('name', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('favicon', 'text');
			$table->addColumn('urlresolved', 'text');
			$table->addColumn('bitrate', 'text');
			$table->addColumn('country', 'text');
			$table->addColumn('language', 'text');
			$table->addColumn('homepage', 'text');
			$table->addColumn('codec', 'text');
			$table->addColumn('tags', 'text');

			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'favorites_user_id_index');
		}

		if (!$schema->hasTable('recent_radio')) {
			$table = $schema->createTable('recent');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('stationuuid', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('name', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('favicon', 'text');
			$table->addColumn('urlresolved', 'text');
			$table->addColumn('bitrate', 'text');
			$table->addColumn('country', 'text');
			$table->addColumn('language', 'text');
			$table->addColumn('homepage', 'text');
			$table->addColumn('codec', 'text');
			$table->addColumn('tags', 'text');

			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'recent_user_id_index');
		}

		return $schema;
	}
}
