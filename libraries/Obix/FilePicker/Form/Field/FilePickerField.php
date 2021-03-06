<?php
/**
 * @package    Obix Library
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2020 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

namespace Obix\FilePicker\Form\Field;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Obix\Filesystem\Folder\Scanner\ScannerConfig;

\JLoader::registerNamespace('Obix', JPATH_LIBRARIES);

/**
 * Implements a Joomla! form field to select files and/or folders from the host's file system.
 */
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
	 * Setting to include only specific files or folders.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $include = '';

	/**
	 * Regex pattern for inclusion of files.
	 *
	 * @var string
	 */
	protected $includeFiles = '';

	/**
	 * Regex pattern for inclusion of folders.
	 *
	 * @var string
	 */
	protected $includeFolders = '';

	/**
	 * Setting to exclude specific files or folders.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $exclude = '';

	/**
	 * Regex pattern for exclusion of files.
	 *
	 * @var string
	 */
	protected $excludeFiles = '';

	/**
	 * Regex pattern for exclusion of files.
	 *
	 * @var string
	 */
	protected $excludeFolders = '';

	/**
	 * List of files and/or folders to ignore irrespective of
	 * the in- and exclusion settings above.
	 *
	 * @var string[]
	 */
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
	 * Base directory for navigation.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $directory = '/';

	/**
	 * Allow selection of files, folders or both.
	 *
	 * @var string
	 */
	protected $mode = ScannerConfig::MODE_FILES;

	/**
	 * Selection of UI/UX style.
	 *
	 * @var string
	 */
	protected $style = 'default';

	/**
	 * Display and allow selection of hidden files and/or folders.
	 *
	 * @var bool
	 */
	protected $showHidden = false;

	/**
	 * Allow selection of multiple files and/or folders.
	 *
	 * @var
	 */
	protected $multiple;

	/**
	 * Disallow non logged in usage.
	 *
	 * @var
	 */
	protected $secure;

	/**
	 * @param   null  $form
	 */
	public function __construct($form = null)
	{
		parent::__construct($form);

		$lang      = Factory::getLanguage();
		$extension = 'plg_fields_filepicker';
		$base_dir  = JPATH_PLUGINS . '/fields/filepicker';
		$lang->load($extension, $base_dir/*, $language_tag, $reload*/);
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput(): string
	{
		$displayData = [
			'id'        => $this->id,
			'name'      => $this->name,
			'style'     => $this->style,
			'fieldname' => $this->fieldname,
			'group'     => $this->group,
			'config'    => [
				'baseDir'        => $this->directory,
				'multiple'       => $this->multiple,
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

		$html = (new FileLayout('obix.filepicker.form.field.filepicker'))->render(
			$displayData
		);

		return $html;
	}

	/**
	 * @param   array  $displayData
	 */
	protected function initView(array $displayData): void
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
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'include':
		case 'exclude':
		case 'ignore':
		case 'hideNone':
		case 'hideDefault':
		case 'directory':
		case 'mode':
		case 'style':
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
	 */
	public function __set($name, $value): void
	{
		switch ($name)
		{
		case 'style':
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
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null): bool
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
			$this->include        = (string) $this->element['include'];
			$this->includeFiles   = (string) $this->element['includeFiles'];
			$this->includeFolders = (string) $this->element['includeFolders'];
			$this->exclude        = (string) $this->element['exclude'];
			$this->excludeFiles   = (string) $this->element['excludeFiles'];
			$this->excludeFolders = (string) $this->element['excludeFolders'];
			if (!empty($ignore = (string) $this->element['ignore']))
			{
				$this->ignore = explode(',', $ignore);
			}
			$this->directory   = (string) $this->element['directory'];
			$this->mode        = ScannerConfig::modeId(
				(string) $this->element['mode']
			);
			$this->style       = (string) $this->element['style'] ?: 'default';
			$this->showHidden  = $this->parBool('showHidden', 'showHidden');
			$this->multiple    = $this->parBool('multiple', 'multiple');
			$this->secure      = $this->parBool('secure', 'secure');
		}

		return $return;
	}

	/**
	 * Method to parse a boolean parameter value.
	 *
	 * @param   string  $elementName
	 * @param   string  $optionalValue
	 *
	 * @return bool
	 */
	private function parBool(string $elementName, string $optionalValue = ''): bool
	{
		$value = (string) $this->element[$elementName];

		return ($value === 'true' || $value == '1'
			|| $value === $optionalValue);
	}

}
