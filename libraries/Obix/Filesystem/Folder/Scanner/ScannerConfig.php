<?php
/**
 * @package     Filepicker
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Obix\Filesystem\Folder\Scanner;

use Joomla\CMS\Factory;
use Joomla\Input\Input;
use Obix\Filesystem\Folder\Helper;

class ScannerConfig implements \JsonSerializable
{

	const MODE_ALL = 'all';
	const MODE_FILES = 'files';
	const MODE_FOLDERS = 'folders';

	private static $modes
		= [
			'0'       => self::MODE_ALL,
			'all'     => self::MODE_ALL,
			'1'       => self::MODE_FILES,
			'files'   => self::MODE_FILES,
			'2'       => self::MODE_FOLDERS,
			'folders' => self::MODE_FOLDERS,
		];

	/**
	 * Inclusion filters.
	 *
	 * @var    bool
	 * @since  3.2
	 */
	protected $include = false;

	protected $includeFiles = '';

	protected $includeFolders = '';

	/**
	 * Exclusion filters.
	 *
	 * @var    bool
	 * @since  3.2
	 */
	protected $exclude = false;

	protected $excludeFiles = '';

	protected $excludeFolders = '';

	protected $ignore
		= [
			'CVS',
			'.svn',
			'.git',
			'.idea',
			'.DS_Store',
			'__MACOSX',
		];

	/**
	 * Recursive.
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $recursive = false;

	/**
	 * Directory.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $directory = '/';

	protected $secure = false;

	protected $multiple = false;

	protected $showHidden = false;

	protected $mode = self::MODE_FILES;

	protected function setters()
	{
		return [
			'directory'   => function (string $value) {
				$directory = Helper::fullPath($value);
				$this->setDirectory($directory);
			},
			'secure'      => function (string $value) {
				$this->setMultiple($this->bool($value, 'secure'));
			},
			'multiple'    => function (string $value) {
				$this->setMultiple($this->bool($value, 'multiple'));
			},
			'recursive'   => function (string $value) {
				$this->setRecursive($this->bool($value, 'recursive'));
			},
			'show_hidden' => function (string $value) {
				$this->setShowHidden($this->bool($value, 'showHidden'));
			},
			'mode'        => function (string $value) {
				$this->setMode(self::$modes[$value] ?? self::MODE_FILES);
			},
			'include'     => function (string $value) {
				$this->setInclude($this->bool($value, 'include'));
			},
			'include_files'   => function (string $value) {
				$this->setIncludeFiles($value);
			},
			'include_folders'   => function (string $value) {
				$this->setIncludeFolders($value);
			},
			'exclude'     => function (string $value) {
				$this->setExclude($this->bool($value, 'exclude'));
			},
			'exclude_files'   => function (string $value) {
				$this->setExcludeFiles($value);
			},
			'exclude_folders'   => function (string $value) {
				$this->setExcludeFolders($value);
			},
			'ignore'      => function (string $value) {
				$this->setIgnore(self::list($value));
			},
		];
	}

	public static function fromInput(Input $input): ScannerConfig
	{
		Factory::getApplication()->input;
		$config = new self();

		foreach ($config->setters() as $name => $setter)
		{
			$setter($input->getString($name, ''));
		}

		return $config;
	}

	public static function fromJson(string $json): ScannerConfig
	{
		$config = new self();

		foreach (json_decode($json) as $name => $value)
		{
			$config->$name = $value;
		}

		return $config;
	}

	public function jsonSerialize()
	{
		$props = [];

		foreach ($this as $name => $value)
		{
			$props[$name] = $value;
		}

		return $props;
	}

	public static function fromFieldElement(\SimpleXMLElement $element
	): ScannerConfig
	{
		$config = new self();

		foreach ($config->setters() as $name => $setter)
		{
			$setter((string) $element[$name] ?? '');
		}

		return $config;
	}

	private function bool(string $value, string $optionalValue = ''): bool
	{
		return ($value === 'true' || $value == '1'
			|| $value === $optionalValue);
	}

	private function list(string $value): array
	{
		return explode(',', $value ?: '');
	}

	public static function modeId(
		string $mode, string $default = self::MODE_FILES
	): string
	{
		return self::$modes[$mode] ?? $default;
	}

	/**
	 * @return string[]
	 */
	public function getIgnore(): array
	{
		return $this->ignore;
	}

	/**
	 * @param   string[]  $ignore
	 *
	 * @return ScannerConfig
	 */
	public function setIgnore(array $ignore): ScannerConfig
	{
		$this->ignore = $ignore;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isRecursive(): bool
	{
		return $this->recursive;
	}

	/**
	 * @param   bool  $recursive
	 *
	 * @return ScannerConfig
	 */
	public function setRecursive(bool $recursive): ScannerConfig
	{
		$this->recursive = $recursive;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDirectory(): string
	{
		return $this->directory;
	}

	/**
	 * @param   string  $directory
	 *
	 * @return ScannerConfig
	 */
	public function setDirectory(string $directory): ScannerConfig
	{
		$this->directory = $directory;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMode()
	{
		return $this->mode;
	}

	/**
	 * @param   mixed  $mode
	 *
	 * @return ScannerConfig
	 */
	public function setMode($mode)
	{
		$this->mode = $mode;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isShowHidden(): bool
	{
		return $this->showHidden;
	}

	/**
	 * @param   bool  $showHidden
	 *
	 * @return ScannerConfig
	 */
	public function setShowHidden(bool $showHidden): ScannerConfig
	{
		$this->showHidden = $showHidden;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isMultiple(): bool
	{
		return $this->multiple;
	}

	/**
	 * @param   bool  $multiple
	 *
	 * @return ScannerConfig
	 */
	public function setMultiple(bool $multiple)
	{
		$this->multiple = $multiple;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSecure(): bool
	{
		return $this->secure;
	}

	/**
	 * @param   bool  $secure
	 *
	 * @return ScannerConfig
	 */
	public function setSecure(bool $secure): ScannerConfig
	{
		$this->secure = $secure;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isInclude(): bool
	{
		return $this->include;
	}

	/**
	 * @param   bool  $include
	 *
	 * @return ScannerConfig
	 */
	public function setInclude(bool $include): ScannerConfig
	{
		$this->include = $include;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getIncludeFiles(): string
	{
		return $this->includeFiles;
	}

	/**
	 * @param   string  $includeFiles
	 *
	 * @return ScannerConfig
	 */
	public function setIncludeFiles(string $includeFiles): ScannerConfig
	{
		$this->includeFiles = $includeFiles;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getIncludeFolders(): string
	{
		return $this->includeFolders;
	}

	/**
	 * @param   string  $includeFolders
	 *
	 * @return ScannerConfig
	 */
	public function setIncludeFolders(string $includeFolders): ScannerConfig
	{
		$this->includeFolders = $includeFolders;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isExclude(): bool
	{
		return $this->exclude;
	}

	/**
	 * @param   bool  $exclude
	 *
	 * @return ScannerConfig
	 */
	public function setExclude(bool $exclude): ScannerConfig
	{
		$this->exclude = $exclude;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getExcludeFiles(): string
	{
		return $this->excludeFiles;
	}

	/**
	 * @param   string  $excludeFiles
	 *
	 * @return ScannerConfig
	 */
	public function setExcludeFiles(string $excludeFiles): ScannerConfig
	{
		$this->excludeFiles = $excludeFiles;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getExcludeFolders(): string
	{
		return $this->excludeFolders;
	}

	/**
	 * @param   string  $excludeFolders
	 *
	 * @return ScannerConfig
	 */
	public function setExcludeFolders(string $excludeFolders): ScannerConfig
	{
		$this->excludeFolders = $excludeFolders;

		return $this;
	}

}
