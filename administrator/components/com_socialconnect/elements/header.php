<?php
/**
 * @version     $Id: style.php 2672 2013-04-03 12:50:12Z lefteris.kavadas $
 * @package     SocialConnect
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

require_once JPATH_SITE.'/administrator/components/com_socialconnect/elements/base.php';

class SocialConnectFieldHeader extends SocialConnectField
{
	public function fetchInput()
	{
		$label = (version_compare(JVERSION, '2.5', 'ge')) ? (string)$this->element['label'] : (string)$this->element->attributes('label');
		return '<h4 class="scSettingsHeader">'.JText::_($label).'</h4>';
	}

	public function fetchLabel()
	{
		return null;
	}

}

class JFormFieldHeader extends SocialConnectFieldHeader
{
	var $type = 'header';
}

class JElementHeader extends SocialConnectFieldHeader
{
	var $_name = 'header';
}
