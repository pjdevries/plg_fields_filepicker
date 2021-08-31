# Obix File & Folder Picker Form field and Custom field Plugin

This custom field plugins allows selection of files and/or 
folders in the host's file system. It provides a Form field
for use in XML form definitions and a Custom field to be used
like any other Custom field. 

## Raison d'être

Every once in a while, one may need a form field to browse the server
file system and select one or more files and/or folders. Joomla core 
contains a [filelist](https://docs.joomla.org/Filelist_form_field_type) 
and [folderlist](https://docs.joomla.org/Folderlist_form_field_type) 
field type, but more often than not they fall short.

## Installation and configuration

Installation of the plugin is the same as for any other Joomla!
extension. If it is installed for the first time, as opposed
to ugraded, it's supposed to be activated automatically. Doesn't
do any harm to check though :)

Two types of fields are being installed: 
- A Joomla! [Form field](https://docs.joomla.org/Form_field) and
- a Joomla! [Custom field](https://docs.joomla.org/Category:Custom_Fields).

## Form field

The `filepicker` form field combines the base functionality of 
Joomla!'s core [filelist](https://docs.joomla.org/Filelist_form_field_type)
and [folderlist](https://docs.joomla.org/Folderlist_form_field_type) 
form field types, but with some extra's.

The form field provides a list of files and/or folders from a 
specified base directory. Depending on the chosen style
- Folders can be navigated into by clicking the folder icon and/or filename.
- Files and/or folders can be selected by clicking the filename or a checkbox.  

When in a folder other than the base directory, the top element of the file
list consists of an icon which allows navigating back, one folder level up. 

On top of the file list there is a row showing the path of the current folder, 
relative to the website root. Folder segments of the path can be clicked, 
allowing direct navigation to that folder.

Below the file list there is a row showing selected files and/or folders, 
if any. Clicking any of those items, allows direct navigation to 
the folder in which the clicked item resides. 

Behaviour of the form field can be controlled by the following attributes:

- `baseDir` is the base folder, relative to the website root, where navigation
starts and whose content is initially displayed. The default is `/`, i.e. 
the website root folder.
- `secure` is a boolean switch, indicating whether the server file system is
accessible to everybody or to logged in users only. The default is `1` (yes).
- `mode` is a setting, indicating whether files, folders or both files and 
folders can be selected. The default is `all` (both files and folders).
- `style` allows selecting the UI/UX style of the folder list.
- `showHidden` is a boolean switch, indicating whether hidden files are 
displayed and available for selection.
- `multiple` is a boolean switch, indicating whether multiple files and/or
folders can be selected.
- `include` is a boolean switch, indicating whether only files with names
matching a certain pattern should be included in the file list. 
- `includeFiles` is a valid regular expression, matching names
 of _files_ to be included (see https://www.php.net/manual/en/pcre.pattern.php).
- `includeFolders` is a valid regular expression, matching names
  of _folders_ to be included (see https://www.php.net/manual/en/pcre.pattern.php).
- `exclude` is a boolean switch, indicating whether files with names
  matching a certain pattern should be excluded from the file list.
- `excludeFiles` is a valid regular expression matching names
  of _files_ to be excluded (see https://www.php.net/manual/en/pcre.pattern.php).
- `excludeFolders` is a valid regular expression matching names
  of _folders_ to be excluded (see https://www.php.net/manual/en/pcre.pattern.php).
- `ignore` is a comma separated list of file- and/or foldernames to always ignore,
irrespective of the folder in which they are found and ignoring the above
include and/or exclude patterns.

Proper usage of the form field needs registration of the `Obix` namespace, 
in order to access the library's  filesystem functions, and a class alias
for the form field:
- `\JLoader::registerNamespace('Obix', JPATH_LIBRARIES)`
- `\JLoader::registerAlias('JFormFieldFilePicker','\\Obix\\Form\\Field\\FilePickerField', '5.0')`

## Main differences with the core Joomla! fields

Apart from the user interface, the `filepicker` form field differs from 
Joomla!'s core [filelist](https://docs.joomla.org/Filelist_form_field_type)
and [folderlist](https://docs.joomla.org/Folderlist_form_field_type)
form field types in the following ways:

- `filepicker` allows navigation between folders.
- `filepicker` allows selection of multiple files and/or folders.
- `filepickers` does not strip the extension from filenames.

## Custom field

The included `FilePicker` custom field behaves the same and can be used in
the same manner as regular, core Joomla! custom fields. For more information 
see:

- https://docs.joomla.org/J3.x:Adding_custom_fields
- https://docs.joomla.org/J3.x:Adding_custom_fields/Parameters_for_all_Custom_Fields
- https://docs.joomla.org/J3.x:Adding_custom_fields/Overrides
- ...

Settings for applied `FilePicker` custom fields are consistent with 
the `filepicker` form field on which the custom field is based. 

## Minimal requirements

Joomla! 3.9 or 4.0
PHP 7.4