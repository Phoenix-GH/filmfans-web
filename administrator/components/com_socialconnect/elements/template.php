<?php
/**
 * @version     $Id: template.php 2437 2013-01-29 14:14:53Z lefteris.kavadas $
 * @package     SocialConnect
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

require_once JPATH_SITE.'/administrator/components/com_socialconnect/elements/base.php';

class SocialConnectFieldTemplate extends SocialConnectField
{
	public function fetchInput()
	{
		jimport('joomla.filesystem.folder');
		$mainframe = JFactory::getApplication();
		$fieldName = (version_compare(JVERSION, '1.6.0', 'ge')) ? $this->name : $this->options['control'].'['.$this->name.']';
		$extension = (version_compare(JVERSION, '1.6.0', 'ge')) ? (string)$this->element->attributes()->extension : $this->element->attributes('extension');
		$type = (JString::strpos($extension, 'com_') === 0) ? 'component' : 'module';
		$basePath = ($type == 'component') ? JPATH_SITE.'/components/'.$extension.'/templates' : JPATH_SITE.'/modules/'.$extension.'/tmpl';
		$baseFolders = JFolder::folders($basePath);

		$db = JFactory::getDBO();
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
		}
		else
		{
			$query = "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0";
		}
		$db->setQuery($query);
		$defaulTemplate = $db->loadResult();

		$templatePath = JPATH_SITE.'/templates/'.$defaulTemplate.'/html/'.$extension;

		if (JFolder::exists($templatePath))
		{
			$templateFolders = JFolder::folders($templatePath);
			$folders = @array_merge($templateFolders, $baseFolders);
			$folders = @array_unique($folders);
		}
		else
		{
			$folders = $baseFolders;
		}

		$exclude = 'default';
		$options = array();
		foreach ($folders as $folder)
		{
			if (preg_match(chr(1).$exclude.chr(1), $folder))
			{
				continue;
			}
			$options[] = JHTML::_('select.option', $folder, $folder);
		}

		array_unshift($options, JHTML::_('select.option', '', JText::_('JW_SC_USE_DEFAULT')));

		return JHTML::_('select.genericlist', $options, $fieldName, 'class="inputbox"', 'value', 'text', $this->value);
	}

}

class JFormFieldTemplate extends SocialConnectFieldTemplate
{
	var $type = 'template';
}

class JElementTemplate extends SocialConnectFieldTemplate
{
	var $_name = 'template';
}
