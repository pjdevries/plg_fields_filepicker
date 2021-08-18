<?php
/**
 * @package    Obix Library
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2020 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

namespace Obix\Filesystem\Folder\Scanner\Acceptor;

defined('_JEXEC') or die;

use Obix\Filesystem\Folder\Scanner\Acceptor\ScannerFilterAcceptor;
use Obix\Filesystem\Folder\Helper;
use Obix\Filesystem\Folder\Scanner\ScannerConfig;
use Obix\Filesystem\Folder\Scanner\ScannerFilterIterator;

class FilepickerAcceptor implements ScannerFilterAcceptor
{

	private $config = [];

	public function __construct(ScannerConfig $config)
	{
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function accept(ScannerFilterIterator $outerIterator): bool
	{
		/** @var \FilesystemIterator $iterator */
		$iterator = $outerIterator->getInnerIterator();
		$baseName = $iterator->getBasename();
		$relPath  = Helper::rootPath($iterator->getPathname());

		$notIsDot      = !$iterator->isDot();
		$showIfHidden  = $baseName[0] !== '.' || $this->config->isShowHidden();
		$modeCompliant = $iterator->isDir()
			|| $this->config->getMode() === ScannerConfig::MODE_ALL
			|| $this->config->getMode() === ScannerConfig::MODE_FILES;
		$included      = !$this->config->isInclude()
			|| ($iterator->isDir()
				&& (empty($this->config->getIncludeFolders())
					|| preg_match($this->config->getIncludeFolders(), $relPath)
				))
			|| (!$iterator->isDir()
				&& (empty($this->config->getIncludeFiles())
					|| preg_match($this->config->getIncludeFiles(), $relPath)
				));
		$notExcluded   = !($this->config->isExclude()
			&& (
				($iterator->isDir()
					&& !empty($this->config->getExcludeFolders())
					&& preg_match($this->config->getExcludeFolders(), $relPath)
				)
				|| (!$iterator->isDir()
					&& !empty($this->config->getExcludeFiles())
					&& preg_match($this->config->getExcludeFiles(), $relPath)
				)
			));
		$notIgnored    = !(isset($this->config->getIgnore()[$baseName]));

		$acceptable
			= $notIsDot
			&& $showIfHidden
			&& $modeCompliant
			&& $notExcluded
			&& $notIgnored
			&& $included;

		return $acceptable;
	}

	/**
	 * @return ScannerConfig
	 */
	public function getConfig(): ScannerConfig
	{
		return $this->config;
	}

	/**
	 * @param   ScannerConfig  $config
	 *
	 * @return FilepickerAcceptor
	 */
	public function setConfig(ScannerConfig $config): FilepickerAcceptor
	{
		$this->config = $config;

		return $this;
	}

}
