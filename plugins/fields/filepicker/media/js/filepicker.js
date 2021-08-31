/**
 * @package    Obix File & Folder Picker Form field and Custom field Plugin
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2020 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

'use strict';

// Setup Obix namespace.
var Obix = Obix || {};

// Translatable text shorthand.
Obix.text = Obix.text || {
    _: t => typeof Joomla === 'undefined' ? t : Joomla.JText._(t)
};

// Contain all code in Obix namespace.
(function (O) {
    // Basic path manipulation functionality.
    class Path {
        constructor(path) {
            this.path = path;
        }

        // Returns an array of path segments.
        segments() {
            const trimmed = this.path.replace(/^[\\\/]|[\\\/]$/, '');

            return trimmed === '' ? [] : trimmed.split('/');
        }

        // Returns trailing name of path
        get basename() {
            return this.path.split('/').pop();
        }

        // Returns parent directory's path.
        get dirname() {
            return '/' + this.segments().slice(0, -1).join('/');
        }
    }

    // Filesystem entry, which can represent a file or folder.
    class Entry {
        constructor(type, path = '', ...more) {
            // Entry type: 'file' or 'folder'.
            this.type = type;
            // Path object.
            this._path = new Path(path);
            // Whether the entry is selectable.
            this.selectable = false;
            // Whether the entry is selected.
            this.selected = false;

            Object.assign(this, more);
        }

        // Returns a new Entry object from Ajax response data.
        static fromResponseData(entryData) {
            const entry = entryData.type === 'file'
                ? new File(entryData.path, entryData)
                : new Folder(entryData.path, entryData);

            return entry;
        }

        get path() {
            return this._path.path;
        }

        get name() {
            return this._path.basename;
        }

        get basename() {
            return this._path.basename;
        }

        get dirname() {
            return this._path.dirname;
        }

        toggleSelect() {
            this.selected = !this.selected;
        }

        pathSegments() {
            return this._path.segments();
        }
    }

    // File entry.
    class File extends Entry {
        constructor(path, ...more) {
            super('file', path);

            Object.assign(this, more);
        }
    }

    // Folder entry.
    class Folder extends Entry {
        constructor(path, entries = [], ...more) {
            super('folder', path);

            // Array of folder entries, i.e. File and/or Fodler objects.
            this.entries = entries;

            Object.assign(this, more);
        }

        // Returns sorted array of folder entries.
        sorted() {
            return this.entries.sort(this.foldersFirstEntrySorter());
        }

        // Method to sort entries with folders first, files next, both
        // sorted alphabetically.
        foldersFirstEntrySorter(reversed = false) {
            return (e1, e2) => {
                if (e1.type !== e2.type) {
                    return e1.type === 'folder' ? (reversed ? -1 : 1) : (reversed ? 1 : -1);
                }

                return e1.path.toLowerCase() < e2.path.toLowerCase() ? (reversed ? -1 : 1) : (reversed ? 1 : -1);
            }
        }

        // Deselect a folder entry.
        unselect(path) {
            const entry = this.entries.find(entry => entry.path === path);

            if (entry !== undefined) {
                entry.selected = false;
            }
        }
    }

    // Fetch entries for a specific folder from the host.
    const fetchEntries = async function (config, folderPath) {
        const data = {
            option: 'com_ajax',
            group: 'fields',
            plugin: 'Filepicker',
            format: 'json',
            folder: folderPath,
            config: JSON.stringify(config)
        };
        data[config.token] = '1';
        const query = Object.entries(data)
            .map((element) => element[0] + '=' + encodeURIComponent(element[1])).join('&');
        const response = await fetch(config.fetchUri, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: query
        });

        if (!response.ok) {
            throw new Error(O.text._('PLG_FIELDS_FILEPICKER_AJAX_ERROR'));
        }

        const responseData = await response.json();

        if (!responseData.success) {
            throw new Error(O.text._(response.message));
        }

        return responseData;
    };

    // Creates a FilePicker component.
    O.filePicker = function (config) {
        return {
            folder: new Folder(config.baseDir),
            baseDirPath: new Path(config.baseDir),
            selectedPaths: [],
            config: config,

            // Initialise the component and load entries for base folder.
            async init() {
                this.selectedPaths = config.selected;
                await this.load(config.baseDir);
            },

            // Check if current folder is the base folder.
            isBase() {
                return this.folder.path === config.baseDir;
            },

            // Navigate into a folder after clicking on it in the folder entries area.
            async enterFolder(entry) {
                await this.load(entry.path);
            },

            // Leave current folder.
            async exitFolder() {
                await this.load(this.folder.dirname);
            },

            // Navigate to base folder.
            async goToRoot() {
                await this.load('/');
            },

            // Determine if a path segment represents the current folder.
            isCurrent(pathSegmentIndex) {
                return pathSegmentIndex === this.baseDirPath.segments().length - 1;
            },

            // Determine is a path segment can be navigated to.
            // Navigation higher up the base folder is not allowed.
            isNavigatable(pathSegmentIndex) {
                return pathSegmentIndex > this.baseDirPath.segments().length - 2;
            },

            // Navigate to folder after clicking on a current path segment
            // in the top bar.
            async goToFolder(folderPathSegmentIndex) {
                const path = '/' + this.folder.pathSegments().slice(0, folderPathSegmentIndex + 1).join('/');

                await this.load(path);
            },

            // Navigate to folder after clicking on a selected entry
            // in the bottom bar.
            async goToSelected(selectedPathIndex) {
                const path = (new Path(this.selectedPaths[selectedPathIndex])).dirname;

                await this.load(path);
            },

            // Toggle entry selection.
            toggleSelect(entry) {
                if (!(config.mode === 'all'
                    || (config.mode === 'files' && entry.type === 'file')
                    || (config.mode === 'folders' && entry.type === 'folder'))) {
                    return;
                }

                entry.toggleSelect();

                // Single select: unselect currently selected entry.
                if (!config.multiple) {
                    this.folder.unselect(this.selectedPaths[0]);
                }

                const toggledEntryIndex = this.selectedPaths.indexOf(entry.path);

                // Single select && toggled entry is now unselected.
                if (!config.multiple && !entry.selected) {
                    // Clear selected paths.
                    this.selectedPaths = [];
                    return;
                }

                // Single select && toggled entry is now selected.
                if (!config.multiple && entry.selected) {
                    // Replace existing path (if any) with toggled entry path.
                    this.selectedPaths[0] = entry.path;
                    return;
                }

                // Multi select && toggled entry is now unselected && toggled entry path exists in selected paths
                if (!entry.selected && toggledEntryIndex > -1) {
                    // Remove toggled entry path from selected paths.
                    this.selectedPaths.splice(toggledEntryIndex, 1);
                    return;
                }

                // Multi select && toggled entry is now selected && toggled entry path does not exist in selected paths.
                if (entry.selected && toggledEntryIndex === -1) {
                    // Add toggled entry path to selected paths.
                    this.selectedPaths.push(entry.path);
                }

                // Multi select && (
                //  (toggled entry unselected && toggled entry path does not exist in selected paths)
                //  || (toggled entry selected && toggled entry path exists in selected paths))
                // Nothing to do.

                this.selectedPaths.sort(this.filesFirstPathSorter());
            },

            // Method to sort path segments with files first, folders next, both
            // sorted alphabetically.
            filesFirstPathSorter(reversed = false) {
                return (s1, s2) => {
                    const segmentCount1 = s1.split('/').length;
                    const segmentCount2 = s2.split('/').length;

                    if (segmentCount1 !== segmentCount2) {
                        return segmentCount1 < segmentCount2 ? (reversed ? -1 : 1) : (reversed ? 1 : -1);
                    }

                    return s1.toLowerCase() < s2.toLowerCase() ? (reversed ? -1 : 1) : (reversed ? 1 : -1);
                }
            },

            // Load folder entries based on a path.
            async load(path) {
                let response = await fetchEntries(config, path);

                const entries = response.data[0].entries.map(entryData => {
                    const entry = Entry.fromResponseData(entryData);

                    entry.selectable = config.mode === 'all'
                        || (config.mode === 'files' && entry.type === 'file')
                        || (config.mode === 'folders' && entry.type === 'folder');
                    entry.selected = this.selectedPaths.indexOf(entry.path) > -1;

                    return entry;
                });

                this.folder = new Folder(path, entries);
            }
        }
    }
})(Obix);