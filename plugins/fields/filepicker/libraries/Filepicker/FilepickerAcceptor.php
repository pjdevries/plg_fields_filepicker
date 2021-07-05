<?php
/**
 * @package     Filepicker
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Filepicker;

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

		$acceptable
			= // No current or parent directory entry,
			!$iterator->isDot()
			// No hidden files, unless explicitly requested.
			&& ($baseName[0] !== '.' || $this->config->isShowHidden())
			&& ($iterator->isDir()
				|| $this->config->getMode() === ScannerConfig::MODE_ALL
				|| $this->config->getMode() === ScannerConfig::MODE_FILES)
			// No names excluded by a regex pattern.
			&& (empty($this->config->getExclude())
				|| !preg_match(
					$this->config->getExclude(), $relPath
				))
			// No names explicitly excluded.
			&& !isset($this->config->getIgnore()[$baseName])
			// Names included by regex pattern.
			&& (empty($this->config->getInclude())
				|| preg_match(
					$this->config->getInclude(), $relPath
				));

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
