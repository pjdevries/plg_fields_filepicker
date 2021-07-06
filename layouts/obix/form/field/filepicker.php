<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/** @var array $displayData */
$id     = $displayData['id'];
$name   = $displayData['name'];
$config = $displayData['config'];

HTMLHelper::_(
	'stylesheet', 'plg_fields_filepicker/default.min.css',
	['version' => 'auto', 'relative' => true]
);
HTMLHelper::_(
	'script', 'plg_fields_filepicker/filepicker.min.js',
	['defer' => true, 'version' => 'auto', 'relative' => true]
);
HTMLHelper::_(
	'script', 'https://unpkg.com/alpinejs@3.2.1/dist/cdn.min.js',
	['defer' => true, 'version' => 'auto', 'relative' => false]
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
<div class="filepicker"
     x-data="Obix.filepicker(JSON.parse(Joomla.getOptions('filepicker')['<?= $id ?>'].config))">

    <div class="filepicker-top">
        <span><?= Text::_('PLG_FIELD_FILEPICKER_CURRENT_FOLDER') ?></span>
        <template x-for="(segment, index) in folder.pathSegments()">
            <div>
                <span>/</span><span class="path-segment" x-text="segment"
                                    @click="goToFolder(index)"></span>
            </div>
        </template>
        <template x-if="folder.pathSegments().length === 0">
            <span>/</span>
        </template>
    </div>

    <div class="filepicker-entries"
         style="height: <?= $displayData['displayHeight'] ?>;">
        <template x-if="!isBase()">
            <div class="entry folder exit">
                <span class="icon" @click="exitFolder()"></span>
            </div>
        </template>

        <template x-for="entry in folder.entries">
            <div>
                <template x-if="entry.type === 'file'">
                    <div class="entry file" :class="{selected: entry.selected}">
                        <span class="icon"></span><span
                                x-text="entry.name"
                                @click="toggleSelect(entry)"></span>
                    </div>
                </template>

                <template x-if="entry.type === 'folder'">
                    <div class="entry folder"
                         :class="{selected: entry.selected}">
                        <span class="icon"
                              @click="enterFolder(entry)"></span><span
                                x-text="entry.name"
                                @click="toggleSelect(entry)"></span>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <div class="filepicker-bottom">
        <span><?= Text::_('PLG_FIELD_FILEPICKER_SELECTED') ?></span>
        <template x-if="!config.multiple || selectedPaths.length  === 1">
        <span
                x-text="selectedPaths[0]"
                @click="goToSelected(0)"></span>
        </template>

        <template x-if="config.multiple && selectedPaths.length  > 1">
        <span
                x-text="selectedPaths.length > 0 ? selectedPaths[0] : ''"
                @click="goToSelected(selectedPaths[selectedPaths.length - 1])"></span>
        </template>
    </div>

    <template x-for="path in selectedPaths">
        <input type="hidden" id="<?= $id ?>" name="<?= $name ?>"
               :value="path">
    </template>
    <input type="hidden" name="<?= $config['token'] ?>" value="1">
</div>

