<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Calendar
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$value = $field->value;

if ($value == '')
{
	return;
}

$filepickerId = $field->rawvalue;

// If 'none' is selected, don't display anything.
if (empty($filepickerId) || $filepickerId === 'none')
{
	return;
}

// If 'default' is selected, use default value.
if ($filepickerId === 'default')
{
	$filepickerId = $field->default_value;
}

// Retrieve the menu link.
$db = Factory::getDbo();
$q = $db->getQuery(true)
	->select('*')
	->from('#__menu AS a')
	->where('id = ' . $filepickerId);
$db->setQuery($q);
$menuItemRow = $db->loadObject();
$link = $menuItemRow->link;

//$showLabel = (int)$field->params->get('showlabel', 0);
//$label = $showLabel
//	? htmlentities(Text::_($field->label), ENT_QUOTES | ENT_IGNORE, 'UTF-8')
//	: htmlentities(Text::_($menuItemRow->title), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
$label = htmlentities(Text::_($field->label), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
$class = $field->params->get('label_render_class');

// Construct the url.
$pageUrl = $link . '&Itemid=' . $filepickerId . '&refItemTitle=' . urlencode($item->title) . '&refItemId=' . $item->id;
?>
<a class="<?php echo $class; ?>" href="<?php echo $pageUrl; ?>"><?php echo $label; ?></a>
