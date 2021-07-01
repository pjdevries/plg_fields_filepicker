<?php
/**
 * @package     Filepicker
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Filepicker;

class File extends Entry
{
	protected $size = 0;

	/**
	 * File constructor.
	 *
	 * @param int $size
	 */
	public function __construct(string $path, int $size)
	{
		parent::__construct($path, Entry::TYPE_FILE);

		$this->setSize($size);
	}

	/**
	 * @return int
	 */
	public function getSize(): int
	{
		return $this->size;
	}

	/**
	 * @param int $size
	 *
	 * @return File
	 */
	public function setSize(int $size): File
	{
		$this->size = $size;

		return $this;
	}

	public function jsonSerialize()
	{
		$json = [];

		foreach ($this as $key => $value)
		{
			$json[$key] = $value;
		}

		return $json;
	}
}
