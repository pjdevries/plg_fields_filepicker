<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

use Joomla\CMS\Version;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/** @var array $displayData */
$id     = $displayData['id'];
$name   = $displayData['name'];
$config = $displayData['config'];

if (Version::MAJOR_VERSION <= 3)
{
	HTMLHelper::_(
		'stylesheet', 'plg_fields_filepicker/default.min.css',
		['version' => 'auto', 'relative' => true]
	);
	HTMLHelper::_(
		'script', 'https://unpkg.com/petite-vue', [], ['defer' => true]
	);
	HTMLHelper::_(
		'script', 'plg_fields_filepicker/filepicker.min.js',
		['version' => 'auto', 'relative' => true]
	);
}
else
{
	$wa = Factory::getDocument()->getWebAssetManager();
	$wa
		->registerAndUseStyle('plg_fields_filepicker.default', 'plg_fields_filepicker/default.min.css')
		->registerAndUseScript('petite-vue', 'https://unpkg.com/petite-vue', [], ['defer' => true], [])
		->registerAndUseScript(
			'plg_fields_filepicker.filepicker', 'plg_fields_filepicker/filepicker.min.js',
			['relative' => true, 'version' => 'auto'], [], []
		);
}

Factory::getDocument()->addScriptOptions(
	'filepicker',
	[
		$id => [
			'config' => json_encode($config),
		],
	]
);
?>
<div id="filepicker-<?= $id ?>" class="filepicker">
    <div class="fp-top">
        <span class="fp-top-label"><?= Text::_('PLG_FIELD_FILEPICKER_CURRENT_FOLDER') ?></span>

        <div class="fp-path">
            <span>/</span>

            <template v-for="(segment, index) in folder.pathSegments()">
            <span class="fp-path-segment"
                  @click="goToFolder(index)">{{ (index > 0 ? '/' : '') + segment }}</span>
            </template>
        </div>
    </div>

    <div class="fp-entries" @mounted="init()">
        <div v-if="!isBase()" class="fp-entry fp-folder-exit">
            <span class="fp-icon" @click="exitFolder()"></span>
        </div>

        <div v-for="entry in folder.sorted()" :key="entry.path">
            <div v-if="entry.type === 'file'" class="fp-entry fp-file-entry"
                 :class="{'fp-selectable': entry.selectable, 'fp-selected': entry.selected}">
                <span class="fp-icon"></span><span class="fp-label"
                                                   @click="toggleSelect(entry)">{{ entry.name }}</span>
            </div>

            <div v-if="entry.type === 'folder'" class="fp-entry fp-folder-entry"
                 :class="{'fp-selectable': entry.selectable, 'fp-selected': entry.selected}">
                <span class="fp-icon"
                      @click="enterFolder(entry)"></span><span class="fp-label"
                                                               @click="toggleSelect(entry)">{{ entry.name }}</span>
            </div>
        </div>
    </div>

    <div class="fp-bottom" :class="{'fp-one': selectedPaths.length  === 1, 'fp-more': selectedPaths.length  > 1}">
        <span class="fp-bottom-label">{{ selectedPaths.length }} <?= Text::_('PLG_FIELD_FILEPICKER_SELECTED') ?></span>
        <div class="fp-selected-paths">
            <span v-for="(path, index) in selectedPaths" @click="goToSelected(index)" class="fp-selected">{{ path }}</span>
        </div>
    </div>

    <input v-for="path in selectedPaths" type="hidden" id="<?= $id ?>" name="<?= $name ?>"
           :value="path">
    <input type="hidden" name="<?= $config['token'] ?>" value="1">
</div>

<script>
    document.addEventListener('DOMContentLoaded', event => {
        let filepicker = Obix.filepicker(JSON.parse(Joomla.getOptions('filepicker')['<?= $id ?>'].config));

        PetiteVue.createApp(filepicker).mount("#filepicker-<?= $id ?>");
    });
</script>