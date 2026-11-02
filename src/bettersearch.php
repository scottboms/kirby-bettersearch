<?php

use Kirby\Cms\App;
use Kirby\Cms\Collection;
use Kirby\Cms\Page;
use Kirby\Cms\User;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Toolkit\Str;

/**
 * Fork of the native search() component
 * Only searches for full matches
 */
function bettersearch(
	App $kirby,
	Collection $collection,
	?string $query = null,
	string|array $params = [],
	?callable $customIndexer = null
): Collection {
	$collection = clone $collection;
	$query      = trim($query ?? '');

	// Empty search query
	if ($query === '') {
		return $collection->limit(0);
	}

	if (is_string($params) === true) {
		$params = ['fields' => Str::split($params, '|')];
	}

	$options = [
		'fields'    => [],
		'score'     => [],
		...$params
	];

	if (is_array($options['fields']) === false) {
		throw new InvalidArgumentException(
			message: 'The "fields" search option must be an array'
		);
	}

	if (is_array($options['score']) === false) {
		throw new InvalidArgumentException(
			message: 'The "score" search option must be an array'
		);
	}

	$fields  = array_map(fn ($field) => Str::lower((string)$field), $options['fields']);
	$pattern = '!' . preg_quote($query, '!') . '!iu';
	$scores  = [];
	$results = $collection->filter(function ($item) use ($fields, $pattern, $options, &$scores, $customIndexer) {
		$data = $customIndexer !== null
			? $customIndexer($item)
			: $item->content()->toArray();

		if (is_array($data) === false) {
			throw new InvalidArgumentException(
				message: 'The custom indexer must return an array'
			);
		}

		$keys   = array_keys($data);
		$keys[] = 'id';
		$score  = array_change_key_case($options['score'], CASE_LOWER);

		if ($item instanceof User) {
			$keys[] = 'name';
			$keys[] = 'email';
			$keys[] = 'role';
		} elseif ($item instanceof Page) {
			// apply the default score for pages
			$score = [
				'id'    => 64,
				'title' => 64,
				...$score
			];
		}

		if ($fields !== []) {
			$keys = array_filter(
				$keys,
				fn ($key) => in_array(Str::lower((string)$key), $fields, true)
			);
		}

		$scoring = [
			'hits'  => 0,
			'score' => 0
		];

		foreach ($keys as $key) {
			$field = Str::lower((string)$key);
			$weight = $score[$field] ?? 1;
			$value  = (string)($data[$key] ?? $item->$key());

			// check for exact query matches
			if ($matches = preg_match_all($pattern, $value)) {
				$scoring['score'] += 2 * $weight;
				$scoring['hits']  += $matches;
			}
		}

		$scores[$item->id()] = $scoring;
		return $scoring['hits'] > 0;
	});

	return $results->sort(
		fn ($item) => $scores[$item->id()]['score'],
		'desc'
	);
}
