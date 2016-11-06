<?php
/**
 * @version     $Id: style.php 3438 2013-07-29 16:05:27Z lefteris.kavadas $
 * @package     SocialConnect
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

require_once JPATH_SITE.'/administrator/components/com_socialconnect/elements/base.php';

class SocialConnectFieldStyle extends SocialConnectField
{
	public function fetchInput()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_socialconnect/css/style.css?v=1.5.1');
		return NULL;
	}

	public function fetchLabel()
	{
		return NULL;
	}

}

class JFormFieldStyle extends SocialConnectFieldStyle
{
	var $type = 'style';
}

class JElementStyle extends SocialConnectFieldStyle
{
	var $_name = 'style';
}
