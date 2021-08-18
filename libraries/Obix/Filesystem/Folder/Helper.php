<?php
/**
 * @package    Obix Library
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2020 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

namespace Obix\Filesystem\Folder;

defined('_JEXEC') or die;

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
