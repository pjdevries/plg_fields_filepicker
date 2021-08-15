<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights
 *              reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Obix\Form\Field;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Obix\Filesystem\Folder\Scanner\ScannerConfig;

\JLoader::registerNamespace('Obix', JPATH_LIBRARIES);

class FilePickerField extends FormField
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $type = 'FilePicker';

	/**
	 * The filter.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $include = '';

	protected $includeFiles = '';

	protected $includeFolders = '';

	/**
	 * The exclude.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $exclude = '';

	protected $excludeFiles = '';

	protected $excludeFolders = '';

	protected $ignore
		= [
			'CVS',
			'.svn',
			'.git',
			'.idea',
			'.DS_Store',
			'__MACOSX',
		];

	/**
	 * The recursive.
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $recursive = false;

	/**
	 * The hideNone.
	 *
	 * @var    boolean
	 * @since  3.2
	 */
	protected $hideNone = false;

	/**
	 * The hideDefault.
	 *
	 * @var    boolean
	 * @since  3.2
	 */
	protected $hideDefault = false;

	/**
	 * The directory.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $directory = '/';

	protected $mode = ScannerConfig::MODE_FILES;

	protected $showHidden = false;

	protected $multiple;

	protected $secure;

	public function __construct($form = null)
	{
		parent::__construct($form);

		$lang      = Factory::getLanguage();
		$extension = 'plg_fields_filepicker';
		$base_dir  = JPATH_PLUGINS . '/fields/filepicker';
		$lang->load($extension, $base_dir/*, $language_tag, $reload*/);
	}

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.7.0
	 */
	protected function getInput(): string
	{
		$displayData = [
			'id'            => $this->id,
			'name'          => $this->name,
			'fieldname'     => $this->fieldname,
			'group'         => $this->group,
			'config'        => [
				'baseDir'        => $this->directory,
				'multiple'       => $this->multiple,
				'recursive'      => $this->recursive,
				'mode'           => $this->mode,
				'showHidden'     => $this->showHidden,
				'include'        => $this->include,
				'includeFiles'   => $this->includeFiles,
				'includeFolders' => $this->includeFolders,
				'exclude'        => $this->exclude,
				'excludeFiles'   => $this->excludeFiles,
				'excludeFolders' => $this->excludeFolders,
				'ignore'         => $this->ignore,
				'secure'         => $this->secure,
				'token'          => $this->secure ? Factory::getSession()
					->getFormToken() : '',
				'fetchUri'       => Uri::base()
					. '?option=com_ajax&format=json&group=fields&plugin=filepicker&format=json',
				'selected'       => $this->value,
			],
		];

		$this->initView($displayData);

		$html = (new FileLayout('obix.form.field.filepicker'))->render(
			$displayData
		);

		return $html;
	}

	protected function initView(array $displayData)
	{
		$languageStrings = [
			'PLG_FIELD_FILEPICKER_SELECT',
			'PLG_FIELD_FILEPICKER_UNSELECT',
			'PLG_FIELD_FILEPICKER_SHOW_ALL',
			'PLG_FIELD_FILEPICKER_SHOW_SELECTED',
			'PLG_FIELDS_FILEPICKER_ACCESS_DENIED',
			'PLG_FIELDS_FILEPICKER_AJAX_ERROR',
		];
		array_walk(
			$languageStrings,
			function (string $langString) {
				Text::script($langString);
			}
		);
	}

	/**
	 * Method to get certain otherwise inaccessible properties from the form
	 * field object.
	 *
	 * @param   string  $name  The property name for which to get the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'include':
		case 'exclude':
		case 'ignore':
		case 'recursive':
		case 'hideNone':
		case 'hideDefault':
		case 'directory':
		case 'mode':
		case 'showHidden':
		case 'multiple':
		case 'secure':
		case 'displayHeight':
			return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form
	 * field object.
	 *
	 * @param   string  $name   The property name for which to set the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
		case 'include':
		case 'includeFiles':
		case 'includeFolders':
		case 'exclude':
		case 'excludeFiles':
		case 'excludeFolders':
		case 'ignore':
		case 'directory':
			$this->$name = (string) $value;
			break;

		case 'mode':
			$value       = (string) $value;
			$this->$name = ScannerConfig::modeId($value);
			break;

		case 'hideNone':
		case 'hideDefault':
		case 'recursive':
		case 'showHidden':
		case 'multiple':
		case 'secure':
			$value       = (string) $value;
			$this->$name = ($value === 'true' || $value === $name
				|| $value === '1');
			break;

		default:
			parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object
	 *                                      representing the `<field>` tag for
	 *                                      the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value.
	 *                                      This acts as an array container for
	 *                                      the field. For example if the field
	 *                                      has name="foo" and the group value
	 *                                      is set to "bar" then the full field
	 *                                      name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$value = empty($value)
			? []
			: (is_array($value)
				? $value
				: explode(
					',', $value
				));

		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->include = (string) $this->element['include'];
			$this->includeFiles = (string) $this->element['includeFiles'];
			$this->includeFolders = (string) $this->element['includeFolders'];
			$this->exclude = (string) $this->element['exclude'];
			$this->excludeFiles = (string) $this->element['excludeFiles'];
			$this->excludeFolders = (string) $this->element['excludeFolders'];
			if (!empty($ignore = (string) $this->element['ignore']))
			{
				$this->ignore = explode(',', $ignore);
			}
			$this->hideNone      = $this->parBool('hide_none', 'hideNone');
			$this->hideDefault   = $this->parBool(
				'hide_default', 'hideDefault'
			);
			$this->directory     = (string) $this->element['directory'];
			$this->mode          = ScannerConfig::modeId(
				(string) $this->element['mode']
			);
			$this->showHidden    = $this->parBool('showHidden', 'showHidden');
			$this->recursive     = $this->parBool('recursive', 'recursive');
			$this->multiple      = $this->parBool('multiple', 'multiple');
			$this->secure        = $this->parBool('secure', 'secure');
		}

		return $return;
	}

	private function parBool(string $elementName, string $optionalValue = '')
	{
		$value = (string) $this->element[$elementName];

		return ($value === 'true' || $value == '1'
			|| $value === $optionalValue);
	}

}
