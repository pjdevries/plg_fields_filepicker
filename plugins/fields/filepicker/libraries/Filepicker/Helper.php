<?php
/**
 * @package     Filepicker
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Filepicker;

class Helper
{
	public static function uniformPath(string $path): string
	{
		return str_replace('\\', '/', $path);
	}

	public static function fullPath(string $path): string
	{
		$jPathRoot = self::uniformPath(JPATH_ROOT);
		$cleanPath = trim(str_replace($jPathRoot, '', self::uniformPath($path)), '\\/');

		return $jPathRoot . (empty($cleanPath) ? '' : '/') . $cleanPath;
	}

	public static function rootPath(string $path): string
	{
		$jPathRoot = self::uniformPath(JPATH_ROOT);
		$cleanPath = trim(str_replace($jPathRoot, '', self::uniformPath($path)), '\\/');

		return empty($cleanPath) ? '/' : '/' . $cleanPath;
	}
}
