<?php declare(strict_types=1);


namespace OCA\MusicNC\Db;

/**
 * Enum-like class to define sort order
 */
abstract class SortBy {
	const None = 0;
	const Name = 1;
	const Parent = 2;
	const Newest = 3;
}
