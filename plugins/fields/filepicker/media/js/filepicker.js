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

            Object.assign(this, more);
        }

        static fromResponseData(entryData, selected) {
            const entry = entryData.type === 'file'
                ? new File(entryData.path, entryData)
                : new Folder(entryData.path, entryData);

            entry.selected = selected.indexOf(entry.path) > -1;

            return entry;
        }

        get path() {
            return this._path.path;
        }

        pathSegments() {
            return this._path.segments();
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

        select(path) {
            this.entries.forEach(entry => entry.selected = entry.path === path ? !entry.selected : false);
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

            return response.json();
        };

        return {
            folder: new Folder(config.baseDir),
            selected: [],
            async init() {
                const baseDir = config.selected.length > 0
                    ? (new Path(config.selected[0])).dirname
                    : config.baseDir
                this.selected = config.selected;
                this.folder = await this.load(baseDir);
            },
            isBase() {
                return this.folder.path === config.baseDir;
            },
            async enterFolder(path) {
                this.folder = await this.load(path);
            },
            async exitFolder() {
                this.folder = await this.load(this.folder.dirname);
            },
            async goToFolder(index) {
                const path = '/' + this.folder.pathSegments().slice(0, index + 1).join('/');

                this.folder = await this.load(path);
            },
            select(path) {
                this.folder.select(path);
                this.selected[0] = this.selected[0] === path ? '' : path;
            },
            async load(path) {
                let response = await fetchEntries(config.fetchUri, path);

                if (!response.success) {
                    throw new Error(O.text._(response.message));
                }

                const entries = response.data[0].entries.map(entryData => Entry.fromResponseData(entryData, this.selected));

                return new Folder(path, entries);
            }
        }
    }
})(Obix);