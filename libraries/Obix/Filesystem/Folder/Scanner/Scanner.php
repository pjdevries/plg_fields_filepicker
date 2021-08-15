<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
namespace Obix\Filesystem\Folder\Scanner;

use Obix\Filesystem\Folder\Scanner\Acceptor\ScannerFilterAcceptor;
use Obix\Filesystem\Folder\Entry;
use Obix\Filesystem\Folder\File;
use Obix\Filesystem\Folder\Folder;
use Obix\Filesystem\Folder\Helper;

class Scanner
{
	private $recursive = false;

	private $acceptor = null;

	private $baseDir = '';

	public function __construct(bool $recursive, ScannerFilterAcceptor $acceptor = null)
	{
		$this->recursive = $recursive;
		$this->acceptor = $acceptor;
	}

	public function scan(string $dir, array $selected): Folder
	{
		if ($this->baseDir)
		{
			$dir = $this->baseDir . $dir;
		}

		$dirIterator = new \FilesystemIterator($dir);
		$filterIterator = new ScannerFilterIterator($dirIterator, $this->acceptor);

		$folder = new Folder($dir);

		if (isset($selected[$dir]))
		{
			$folder->setSelected(true);
		}

		$entries = [];

		/** @var \SplFileInfo $info */
		foreach ($filterIterator as $info)
		{
			$pathName = Helper::uniformPath($info->getPathname());

			if ($this->baseDir)
			{
				$pathName = substr($pathName, strlen($this->baseDir));
			}

			$entry = $info->isFile()
				? new File($pathName, $info->getSize())
				: ($this->recursive
					? $this->scan($pathName, $selected)
					: new Folder($pathName));

			if (isset($selected[$pathName]))
			{
				$entry->setSelected(true);
			}

			if ($info->isDir() && $entry->treeSelectCount() > 0)
			{
				$entry->setExpanded(true);
			}

			$entries[] = $entry;
		}

		// Sort directories first, alphabetically ascending second.
		usort($entries, function (Entry $entry1, Entry $entry2) {
			if ($entry1->getType() === $entry2->getType())
			{
				return strcasecmp($entry1->getPath(), $entry2->getPath());
			}

			return $entry1->isFolder() ? -1 : 1;
		});

		return $folder->setEntries($entries);
	}

	/**
	 * @return bool
	 */
	public function isRecursive(): bool
	{
		return $this->recursive;
	}

	/**
	 * @param bool $recursive
	 *
	 * @return Scanner
	 */
	public function setRecursive(bool $recursive): Scanner
	{
		$this->recursive = $recursive;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBaseDir(): string
	{
		return $this->baseDir;
	}

	/**
	 * @param   string  $baseDir
	 *
	 * @return Scanner
	 */
	public function setBaseDir(string $baseDir): Scanner
	{
		$this->baseDir = '/' . trim($baseDir, '\\/');

		return $this;
	}
}
