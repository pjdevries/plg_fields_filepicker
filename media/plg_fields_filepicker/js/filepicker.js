'use strict';

var Obix = Obix || {};

Obix.text = Obix.text || {
    _: t => typeof Joomla === 'undefined' ? t : Joomla.JText._(t)
};

(function (O) {
    class Path {
        constructor(path) {
            this.path = path;
        }

        segments() {
            const trimmed = this.path.replace(/^[\\\/]|[\\\/]$/, '');

            return trimmed === '' ? [] : trimmed.split('/');
        }

        get basename() {
            return this.path.split('/').pop();
        }

        get dirname() {
            return '/' + this.segments().slice(0, -1).join('/');
        }
    }

    class Entry {
        constructor(type, path = '', ...more) {
            this.type = type;
            this._path = new Path(path);
            this.selectable = false;
            this.selected = false;

            Object.assign(this, more);
        }

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

    class File extends Entry {
        constructor(path, ...more) {
            super('file', path);

            this.size = 0;

            Object.assign(this, more);
        }
    }

    class Folder extends Entry {
        constructor(path, entries = [], ...more) {
            super('folder', path);

            this.entries = entries;
            this.expanded = false;
            this.childCount = 0;
            this.treeChildCount = 0;
            this.selectCount = 0;
            this.treeSelectCount = 0;

            Object.assign(this, more);
        }

        sorted() {
            return this.entries.sort(this.foldersFirstEntrySorter());
        }

        foldersFirstEntrySorter(reversed = false) {
            return (e1, e2) => {
                if (e1.type !== e2.type) {
                    return e1.type === 'folder' ? (reversed ? -1 : 1) : (reversed ? 1 : -1);
                }

                return e1.path.toLowerCase() < e2.path.toLowerCase() ? (reversed ? -1 : 1) : (reversed ? 1 : -1);
            }
        }

        unselect(path) {
            const entry = this.entries.find(entry => entry.path === path);

            if (entry !== undefined) {
                entry.selected = false;
            }
        }
    }

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

    O.filepicker = function (config) {
        return {
            folder: new Folder(config.baseDir),
            selectedPaths: [],
            config: config,
            async init() {
                this.selectedPaths = config.selected;
                await this.load(config.baseDir);
            },
            isBase() {
                return this.folder.path === config.baseDir;
            },
            async enterFolder(entry) {
                await this.load(entry.path);
            },
            async exitFolder() {
                await this.load(this.folder.dirname);
            },
            async goToRoot() {
                await this.load('/');
            },
            async goToFolder(folderPathSegmentIndex) {
                const path = '/' + this.folder.pathSegments().slice(0, folderPathSegmentIndex + 1).join('/');

                await this.load(path);
            },
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
            filesFirstPathSorter(reversed = false) {
                return (s1, s2) => {
                    const segmentCount1 = s1.split('/').length;
                    const segmentCount2 = s2.split('/').length;

                    if (segmentCount1 !== segmentCount2) {
                        return segmentCount1 < segmentCount2 ? (reversed ? -1 : 1) : (reversed ? 1 : -1);
                    }

                    return s1.path.toLowerCase() < s2.path.toLowerCase() ? (reversed ? -1 : 1) : (reversed ? 1 : -1);
                }
            },
            async goToSelected(selectedPathIndex) {
                const path = (new Path(this.selectedPaths[selectedPathIndex])).dirname;

                await this.load(path);
            },
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