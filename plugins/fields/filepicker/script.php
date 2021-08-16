<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\PluginAdapter;

// no direct access
defined('_JEXEC') or die;

class plgfieldsfilepickerInstallerScript
{

	public function postflight($route, PluginAdapter $adapter)
	{
		// Enable plugin on first installation only.
		switch ($route)
		{
		case 'install':
			$this->activatePlugin();
			break;

		case 'uninstall':
			$this->removeEmptyFolders();
			break;
		}
	}

	private function activatePlugin()
	{
		$db    = Factory::getDbo();
		$query = sprintf(
			'UPDATE %s SET %s = 1 WHERE %s = %s AND %s = %s',
			$db->quoteName('#__extensions'),
			$db->quoteName('enabled'),
			$db->quoteName('type'), $db->quote('plugin'),
			$db->quoteName('name'), $db->quote('PLG_FIELDS_FILEPICKER')
		);
		$db->setQuery($query);
		$db->execute();
	}

	private function removeEmptyFolders(): void
	{
		$folderPaths = [
			'layouts'   => [
				'layouts/obix/form/field',
			],
			'libraries' => [
				'libraries/Obix/Filesystem/Folder/Scanner/Acceptor',
				'libraries/Obix/Filesystem/Folder/Scanner',
				'libraries/Obix/Filesystem/Folder',
				'libraries/Obix/Form/Field',
			],
			'media'     => [
				'media/plg_fields_filepicker/css',
				'media/plg_fields_filepicker/images',
				'media/plg_fields_filepicker/js',
			],
		];

		foreach ($folderPaths as $base => $paths)
		{
			foreach ($paths as $path)
			{
				$this->removeEmptyFolder(JPATH_ROOT . '/' . $base, JPATH_ROOT . '/' . $path);
			}
		}
	}

	private function removeEmptyFolder(string $base, string $folderPath): void
	{
		do
		{
			if (!($this->isEmptyFolder($folderPath) && @rmdir($folderPath)))
			{
				break;
			}

			$folderPath = dirname($folderPath);
		} while ($folderPath > $base);
	}

	private function isEmptyFolder(string $folderPath): bool
	{
		if (!(is_dir($folderPath) && is_readable($folderPath)))
		{
			return false;
		}

		return !(new \FilesystemIterator($folderPath))->valid();
	}

}
