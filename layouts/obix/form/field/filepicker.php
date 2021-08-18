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

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

/** @var array $displayData */
$id     = $displayData['id'];
$name   = $displayData['name'];
$config = $displayData['config'];
$style = $displayData['style'] ?? 'default';

$styleSheet = $style . '.min.css';
$entriesLayout = 'entries_' . $style;

HTMLHelper::_(
    'stylesheet', 'plg_fields_filepicker/general.min.css',
    ['version' => 'auto', 'relative' => true]
);
HTMLHelper::_(
    'stylesheet', 'plg_fields_filepicker/' . $styleSheet,
    ['version' => 'auto', 'relative' => true]
);
HTMLHelper::_(
    'script', 'https://unpkg.com/petite-vue', [], ['defer' => true]
);
HTMLHelper::_(
    'script', 'plg_fields_filepicker/filepicker.min.js',
    ['version' => 'auto', 'relative' => true]
);

Factory::getDocument()->addScriptOptions(
	'filepicker',
	[
		$id => [
			'config' => json_encode($config),
		],
	]
);
?>

<div id="filepicker-<?= $id ?>" class="filepicker" @mounted="init()">
	<?php echo LayoutHelper::render('obix.form.field.filepicker.top', $displayData); ?>

	<?php echo LayoutHelper::render('obix.form.field.filepicker.' . $entriesLayout, $displayData); ?>

	<?php echo LayoutHelper::render('obix.form.field.filepicker.bottom', $displayData); ?>

    <input v-for="path in selectedPaths" type="hidden" id="<?= $id ?>" name="<?= $name ?>"
           :value="path">
    <input type="hidden" name="<?= $config['token'] ?>" value="1">
</div>

<script>
    document.addEventListener('DOMContentLoaded', event => {
        let filePicker = Obix.filePicker(JSON.parse(Joomla.getOptions('filepicker')['<?= $id ?>'].config));

        PetiteVue.createApp(filePicker).mount("#filepicker-<?= $id ?>");
    });
</script>