<?php
/**
 * PHP Server instalation
 * @package lib-curl
 * @version 0.0.1
 */

namespace LibCurl\Server;

class PHP
{
	static function curl(): array{
		$exists = function_exists('curl_init');

		return [
			'success' => $exists,
			'info'    => curl_version()['version']
		];
	}
}