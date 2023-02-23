<?php declare(strict_types=1);



namespace OCA\MusicNC\Db;

/**
 * Enum-like class to define matching mode for the search functions
 */
abstract class MatchMode {
	const Exact = 0;		// the whole pattern must be matched exactly, still ignoring case
	const Wildcards = 1;	// the pattern may contain wildcards '%' or '_'
	const Substring = 2;	// the pattern is matched as substring(s), supporting also wildcards;
							// quotation may be used to pass a single substring which may contain whitespace
}
