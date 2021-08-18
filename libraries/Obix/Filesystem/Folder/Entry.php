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

abstract class Entry implements \JsonSerializable
{
	const TYPE_FILE = 'file';
	const TYPE_FOLDER = 'folder';

	protected $name = '';

	protected $path = '';

	protected $type = '';

	protected $selected = false;

	/**
	 * Entry constructor.
	 *
	 * @param string $path
	 * @param string $type
	 */
	public function __construct(string $path, string $type)
	{
		$this->setPath($path);
		$this->setType($type);
	}

	public function isFile(): bool
	{
		return $this->type === self::TYPE_FILE;
	}

	public function isFolder(): bool
	{
		return $this->type === self::TYPE_FOLDER;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return Entry
	 */
	public function setName(string $name): Entry
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 *
	 * @return Entry
	 */
	public function setPath(string $path): Entry
	{
		$this->path = $path;
		$this->name = basename($path);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 *
	 * @return Entry
	 */
	public function setType(string $type): Entry
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSelected(): bool
	{
		return $this->selected;
	}

	/**
	 * @param bool $selected
	 *
	 * @return Entry
	 */
	public function setSelected(bool $selected): Entry
	{
		$this->selected = $selected;

		return $this;
	}
}
