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
