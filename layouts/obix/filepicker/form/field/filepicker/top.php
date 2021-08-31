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

<div class="fp-top">
    <span class="fp-top-label"><?= Text::_('PLG_FIELD_FILEPICKER_CURRENT_FOLDER') ?></span>

    <div class="fp-path">
        <span>/</span>

        <span v-for="(segment, index) in folder.pathSegments()" class="fp-path-segment"
              :class="{'fp-navigatable': isNavigatable(index)}"
              @click="if (isNavigatable(index)) goToFolder(index)">{{ (index > 0 ? '/' : '') + segment }}</span>
    </div>
</div>
