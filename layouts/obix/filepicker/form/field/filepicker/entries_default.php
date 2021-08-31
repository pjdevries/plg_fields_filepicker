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
?>
<div class="fp-entries">
    <div v-if="!isBase()" class="fp-folder-exit">
        <span class="fp-icon fp-folder-exit-icon" @click="exitFolder()"></span>
    </div>

    <?php
    /*
        Don't change order of <span>'s. Use CSS for that to change order of flexbox items.
        Don't remove seemingly redundant HTML comments. They prevent unwanted whitespace between <span>'s.
    */
    ?>
    <div v-for="entry in folder.sorted()" :key="entry.path">
        <div v-if="entry.type === 'file'" class="fp-entry fp-file-entry"
             :class="{'fp-selectable': entry.selectable, 'fp-selected': entry.selected}">
            <span class="fp-icon fp-check-icon" @click="toggleSelect(entry)"></span><!--
            --><span class="fp-label" @click="toggleSelect(entry)">{{ entry.name }}</span><!--
            --><span class="fp-icon fp-file-entry-icon"></span>
        </div>

        <div v-if="entry.type === 'folder'" class="fp-entry fp-folder-entry"
             :class="{'fp-selectable': entry.selectable, 'fp-selected': entry.selected}">
            <span v-if="entry.selectable" class="fp-icon fp-check-icon" @click="toggleSelect(entry)"></span><!--
            --><span class="fp-label" @click="enterFolder(entry)">{{ entry.name }}</span><!--
            --><span class="fp-icon fp-folder-entry-icon" @click="enterFolder(entry)"></span>
        </div>
    </div>
</div>