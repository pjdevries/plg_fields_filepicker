<?php
/**
 * @package        ${PACKAGE_NAME}
 *
 * @author         Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @link           www.obix.nl
 * @date           5-12-2019
 * @copyright  (C) 2019, Obix webtechniek. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Obix\Filesystem\Folder\Scanner\Scanner;
use Obix\Filesystem\Folder\Scanner\ScannerConfig;
use Obix\Filesystem\Folder\Acceptor\FilepickerAcceptor;

\JLoader::registerNamespace('Obix', JPATH_LIBRARIES);
\JLoader::import('components.com_fields.libraries.fieldsplugin', JPATH_ADMINISTRATOR);

class PlgFieldsFilepicker extends \FieldsPlugin
{
	public function __construct(&$subject, $config = [])
	{
		parent::__construct($subject, $config);
	}

	public function onCustomFieldsPrepareDom($field, DOMElement $parent, JForm $form)
	{
		$fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

		if (!$fieldNode)
		{
			return $fieldNode;
		}

		// The 'name' and 'label' attributes are taken from the Name and Label fields
		// when a user creates a new instance of the custom field.
		$fieldNode->setAttribute('type', 'filepicker');
		$fieldNode->setAttribute('published', $field->fieldparams->get('published', '1'));
		$fieldNode->setAttribute('client_id', $field->fieldparams->get('client_id', '0'));
		$fieldNode->setAttribute('language', $field->fieldparams->get('language', '*'));

		$fieldNode->setAttribute('directory', ((array)$field->fieldparams->get('baseDir', '/'))[0]);
		$fieldNode->setAttribute('mode', $field->fieldparams->get('mode', 'all'));
		$fieldNode->setAttribute('show_hidden', $field->fieldparams->get('showHidden', '0'));
		$fieldNode->setAttribute('recursive', $field->fieldparams->get('recursive', '1'));
		$fieldNode->setAttribute('multiple', $field->fieldparams->get('multiple', '0'));
		$fieldNode->setAttribute('include', $field->fieldparams->get('include', ''));
		$fieldNode->setAttribute('exclude', $field->fieldparams->get('exclude', ''));
		$fieldNode->setAttribute('ignore', $field->fieldparams->get('ignore', ''));
		$fieldNode->setAttribute('display_height', $field->fieldparams->get('displayHeight', '300px'));

		return $fieldNode;
	}

	public function onCustomFieldsPrepareField($context, $item, $field)
	{
		$field->item = $item;

		return parent::onCustomFieldsPrepareField($context, $item, $field);
	}

	public function onAjaxFilepicker()
	{
		$app = Factory::getApplication();
		$jInput = $app->input;

		/** @var ScannerConfig $config */
		$config = ScannerConfig::fromJson($jInput->getString('config', ''))
			->setRecursive(false);

		$app->setHeader('Access-Control-Allow-Origin', '*', true);

		if ($config->isSecure())
		{
			$sessionToken = Factory::getSession()->getFormToken();
			$requestToken = $jInput->getString($sessionToken, '');

			if (!$requestToken)
			{
				throw new \Exception(Text::_('PLG_FIELDS_FILEPICKER_ACCESS_DENIED'));
			}
		}

		$selected           = json_decode($jInput->getString('selected', '[]'));
		$folder             = $jInput->getString('folder', '');
		$filePickerAcceptor = new FilepickerAcceptor($config);
		$scanner = new Scanner($config->isRecursive(), $filePickerAcceptor);
		$result = $scanner
			->setBaseDir(JPATH_ROOT)
			->scan($folder, $selected);

		return $result;
	}
}
