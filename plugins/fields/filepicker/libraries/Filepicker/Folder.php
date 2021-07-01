<?php
/**
 * @package     Filepicker
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Filepicker;

class Folder extends Entry
{
	protected $entries = [];

	protected $expanded = false;

	/**
	 * Folder constructor.
	 *
	 * @param array $entries
	 */
	public function __construct(string $path, array $entries = [])
	{
		parent::__construct($path, Entry::TYPE_FOLDER);

		$this->setEntries($entries);
	}

	private function recursive(callable $callback, &$cargo = null)
	{
		return array_walk($this->entries, function (&$entry) use ($callback, &$cargo) {
			$callback($entry, $cargo);

			if ($entry->isFolder())
			{
				return $this->recursive($callback, $cargo);
			}
		});
	}

	public function childCount(): int
	{
		return count($this->entries);
	}

	public function treeChildCount(): int
	{
		return array_reduce($this->entries, function (int $runningTotal, $entry) {
			if ($entry->isFolder())
			{
				$runningTotal += $entry->childCount() + $entry->treeChildCount();
			}

			return $runningTotal;
		}, 0);
	}

	public function selectCount(): int
	{
		return array_reduce($this->entries, function (int $runningTotal, $entry) {
			return $entry->isSelected() ? $runningTotal + 1 : $runningTotal;
		}, 0);
	}

	public function treeSelectCount(): int
	{
		return array_reduce($this->entries, function (int $runningTotal, $entry) {
			if ($entry->isFolder())
			{
				$runningTotal += $entry->treeSelectCount();
			}

			return $entry->isSelected() ? $runningTotal + 1 : $runningTotal;
		}, 0);
	}

	/**
	 * @return array
	 */
	public function getEntries(): array
	{
		return $this->entries;
	}

	/**
	 * @param array $entries
	 *
	 * @return Folder
	 */
	public function setEntries(array $entries): Folder
	{
		$this->entries = $entries;

		return $this;
	}

	/**
	 * @param Entry $entry
	 *
	 * @return Folder
	 */
	public function addEntry(Entry $entry): Folder
	{
		$this->entries[] = $entry;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isExpanded(): bool
	{
		return $this->expanded;
	}

	/**
	 * @param bool $expanded
	 *
	 * @return Folder
	 */
	public function setExpanded(bool $expanded): Folder
	{
		$this->expanded = $expanded;

		return $this;
	}

	public function jsonSerialize()
	{
		$json = [];

		foreach ($this as $key => $value)
		{
			$json[$key] = $value;
		}

		$json['childCount'] = $this->childCount();
		$json['treeChildCount'] = $this->treeChildCount();
		$json['selectCount'] = $this->selectCount();
		$json['treeSelectCount'] = $this->treeSelectCount();

		return $json;
	}
}
