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

use Joomla\CMS\Language\Text;
?>
<div class="fp-bottom" :class="{'fp-one': selectedPaths.length  === 1, 'fp-more': selectedPaths.length  > 1}">
    <span class="fp-bottom-label">{{ selectedPaths.length }} <?= Text::_('PLG_FIELD_FILEPICKER_SELECTED') ?></span>
    <ul class="fp-selected-paths">
        <li v-for="(path, index) in selectedPaths" @click="goToSelected(index)" class="fp-selected">{{ path }}</li>
    </ul>
</div>
