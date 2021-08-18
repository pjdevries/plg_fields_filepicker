<?php
/**
 * @package    Obix Library
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2020 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

namespace Obix\Filesystem\Folder\Scanner\Acceptor;

defined('_JEXEC') or die;

use Obix\Filesystem\Folder\Scanner\ScannerFilterIterator;

interface ScannerFilterAcceptor
{
	public function accept(ScannerFilterIterator $iterator): bool;
}
