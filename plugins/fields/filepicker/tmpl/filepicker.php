<?php
/**
 * @package    Obix File & Folder Picker Form field and Custom field Plugin
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2020 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

defined('_JEXEC') or die;

/** @var \stdClass $field */
$fieldValues = array_filter((array) $field->value ?: [], 'strlen');

if (!count($fieldValues))
{
	return;
}

if (count($fieldValues) === 1) : ?>
    <span class="field-value"><?php echo $fieldValues[0]; ?></span>
	<?php return;
endif; ?>

<ul>
	<?php foreach ($fieldValues as $value) : ?>
        <li><?= $value ?></li>
	<?php endforeach; ?>
</ul>