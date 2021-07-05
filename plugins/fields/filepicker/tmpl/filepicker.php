<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var \stdClass $field */
$fieldValues = (array) $field->value ?: [];

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