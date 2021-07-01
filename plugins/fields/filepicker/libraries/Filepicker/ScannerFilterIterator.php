<?php
/**
 * @package     Filepicker
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Filepicker;

class ScannerFilterIterator extends \FilterIterator
{
	private $acceptor = null;

	public function __construct(\FilesystemIterator $iterator, ScannerFilterAcceptor $acceptor)
	{
		parent::__construct($iterator);

		$this->acceptor = $acceptor;
	}

	/**
	 * @inheritDoc
	 */
	public function accept()
	{
		return $this->acceptor ? $this->acceptor->accept($this) : true;
	}
}
