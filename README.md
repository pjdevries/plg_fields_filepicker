# Obix File & Folder Picker Form field and Custom field Plugin

This custom field plugins allows selection of files and/or 
folders in the host's file system. It provides a Form field
for use in XML form definitions and a Custom field to be used
like any other Custom field. 

## Rationale 

## Installation and configuration

Installation of the plugin is the same as for any other Joomla!
extension. If it is installed for the first time, as opposed
to ugraded, it's supposed to be activated automatically. Doesn't
do any harm to check though :)

## Form field
baseDir
secure
mode
showHidden
multiple
include
includeFiles
includeFolders
exclude
excludeFiles
excludeFolders
ignore

\JLoader::registerNamespace('Obix', JPATH_LIBRARIES);
\JLoader::registerAlias('JFormFieldFilePicker','\\Obix\\Form\\Field\\FilePickerField', '5.0');

Path: absolute (leading slash), relative to website root.

Differences with core Filelist and Folderlist
- Allows to navigate between folders.
- In- and exclude folders and files separately.
- Select multiple.
- No 'stripext'.

Differences with core Folderlist

## Custom field

https://docs.joomla.org/J3.x:Adding_custom_fields
https://docs.joomla.org/J3.x:Adding_custom_fields/Parameters_for_all_Custom_Fields
https://docs.joomla.org/J3.x:Adding_custom_fields/Overrides

## Minimal requirements
Joomla! 3.9 or 4.0
PHP 7.4