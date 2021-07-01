<?php
use Joomla\CMS\Factory;

// no direct access
defined('_JEXEC') or die;

class plgfieldsfilepickerInstallerScript
{
	public function postflight($route, JAdapterInstance $adapter)
	{
        // Enable plugin on first installation only.
        if ($route === 'install')
        {
            $db = Factory::getDbo();
            $query = sprintf('UPDATE %s SET %s = 1 WHERE %s = %s AND %s = %s',
                $db->quoteName('#__extensions'),
                $db->quoteName('enabled'),
                $db->quoteName('type'), $db->quote('plugin'),
	            $db->quoteName('name'), $db->quote('PLG_FIELDS_FILEPICKER')
            );
            $db->setQuery($query);
            $db->execute();
        }
    }
}
