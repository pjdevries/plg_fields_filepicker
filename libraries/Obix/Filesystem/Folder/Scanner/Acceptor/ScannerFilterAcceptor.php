<?php
/**
 * @package     Filepicker
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Obix\Filesystem\Folder\Scanner\Acceptor;

use Obix\Filesystem\Folder\Scanner\ScannerFilterIterator;

interface ScannerFilterAcceptor
{
	public function accept(ScannerFilterIterator $iterator): bool;
}
