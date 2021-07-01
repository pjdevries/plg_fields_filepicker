<?php
/**
 * @package     Filepicker
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Filepicker;

interface ScannerFilterAcceptor
{
	public function accept(ScannerFilterIterator $iterator): bool;
}
