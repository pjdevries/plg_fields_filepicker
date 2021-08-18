<?php
/**
 * @package    Obix Library
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2020 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

namespace Obix\Filesystem\Folder\Scanner;

defined('_JEXEC') or die;

use Obix\Filesystem\Folder\Scanner\Acceptor\ScannerFilterAcceptor;

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
