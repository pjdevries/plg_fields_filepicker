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

        unselect(path) {
            const entry = this.entries.find(entry => entry.path === path);

            if (entry !== undefined) {
                entry.selected = false;
            }
        }
    }

    O.filepicker = function (config) {
        const fetchEntries = async function (uri, folderPath) {
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
            const response = await fetch(uri, {
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

        return {
            config: config,
            folder: new Folder(config.baseDir),
            selectedPaths: [],
            async init() {
                this.selectedPaths = config.selected;
                this.folder = await this.load(config.baseDir);
            },
            isBase() {
                return this.folder.path === config.baseDir;
            },
            async enterFolder(entry) {
                this.folder = await this.load(entry.path);
            },
            async exitFolder() {
                this.folder = await this.load(this.folder.dirname);
            },
            async goToFolder(folderPathSegmentIndex) {
                const path = '/' + this.folder.pathSegments().slice(0, folderPathSegmentIndex + 1).join('/');

                this.folder = await this.load(path);
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
            },
            async goToSelected(selectedPathIndex) {
                const path = (new Path(this.selectedPaths[selectedPathIndex])).dirname;

                this.folder = await this.load(path);
            },
            async load(path) {
                let response = await fetchEntries(config.fetchUri, path);

                const entries = response.data[0].entries.map(entryData => {
                    const entry = Entry.fromResponseData(entryData);

                    entry.selected = this.selectedPaths.indexOf(entry.path) > -1;

                    return entry;
                });

                return new Folder(path, entries);
            }
        }
    }
})(Obix);