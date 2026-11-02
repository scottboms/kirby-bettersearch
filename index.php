<?php

use Kirby\Cms\App;

require_once __DIR__ . '/src/bettersearch.php';

// Load plugin-local Composer dependencies when present
@include_once __DIR__ . '/vendor/autoload.php';

$version = App::version() ?? '0.0.0';
if (
	version_compare($version, '6.0.0-beta.1', '<') ||
	version_compare($version, '7.0.0', '>=')
) {
	throw new RuntimeException('Better Search 6 requires Kirby 6');
}

App::plugin(
	name: 'bvdputte/bettersearch',
	extends: [
		'pagesMethods' => [
			'bettersearch' => function ($query, $params = [], $customIndexer = null) {
				return bettersearch(kirby(), $this, $query, $params, $customIndexer);
			}
		],
		'siteMethods' => [
			'bettersearch' => function ($query, $params = [], $customIndexer = null) {
				return bettersearch(kirby(), $this->index(), $query, $params, $customIndexer);
			}
		]
	],
	version: '6.0.0'
);
