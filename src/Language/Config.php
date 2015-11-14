<?php

namespace Language;

class Config
{
	public static function get($key)
	{
		switch ($key)
		{
			case 'system.paths.root' :
				return realpath(dirname(__FILE__) . '/../');
				break;

			case 'system.translated_applications':
				return ['portal' => ['en', 'hu']];

			default:
				return;
		}
	}
}