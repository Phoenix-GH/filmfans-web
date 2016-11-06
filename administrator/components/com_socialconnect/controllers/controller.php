<?php
/**
 * @version		$Id: controller.php 2437 2013-01-29 14:14:53Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die ;

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.application.component.controller');

if (version_compare(JVERSION, '3.0', 'ge'))
{
	class SocialConnectController extends JControllerLegacy
	{
		public function display($cachable = false, $urlparams = array())
		{
			parent::display($cachable, $urlparams);
		}

	}

}
elseif (version_compare(JVERSION, '2.5', 'ge'))
{
	class SocialConnectController extends JController
	{
		public function display($cachable = false, $urlparams = false)
		{
			parent::display($cachable, $urlparams);
		}

	}

}
else
{
	class SocialConnectController extends JController
	{
		public function display($cachable = false)
		{
			parent::display($cachable);
		}

	}

}
