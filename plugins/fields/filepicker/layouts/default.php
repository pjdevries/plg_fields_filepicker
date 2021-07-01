<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

/** @var array $displayData */
$id     = $displayData['id'];
$config = $displayData['config'];

$doc = Factory::getDocument();
$doc->addStyleSheet(
	Uri::root() . 'plugins/fields/filepicker/media/css/default.css'
);
$doc->addScript(
	Uri::root() . 'plugins/fields/filepicker/media/js/filepicker.js',
	[],
	[
		'defer'    => true,
		'version'  => 'auto',
		'relative' => false,
	]
);
$doc->addScript(
	'https://unpkg.com/alpinejs@3.2.1/dist/cdn.min.js',
	[],
	[
		'defer'    => true,
		'version'  => 'auto',
		'relative' => false,
	]
);

$doc->addScriptOptions(
	'filepicker',
	[
		$id => [
			'config' => json_encode($config),
		],
	]
);
?>
<div id="<?php echo $id; ?>" class="filepicker"
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
         style="height: <?= $displayData['displayHeight'] ?>px;">
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
                                @click="select(entry.path)"></span>
                    </div>
                </template>

                <template x-if="entry.type === 'folder'">
                    <div class="entry folder" :class="{selected: entry.selected}">
                        <span class="icon"
                              @click="enterFolder(entry.path)"></span><span
                                x-text="entry.name"
                                @click="select(entry.path)"></span>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <div class="filepicker-bottom">
        <span><?= Text::_('PLG_FIELD_FILEPICKER_SELECTED') ?></span><span
                x-text="selected.length > 0 ? selected[0] : ''"></span>
    </div>

    <input type="hidden" name="jform[params][<?= $id ?>]" :value="selected">
    <input type="hidden" name="<?= $config['token'] ?>" value="1">
</div>

