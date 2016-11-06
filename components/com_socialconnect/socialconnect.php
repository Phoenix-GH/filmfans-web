<?php
/**
 * @version		$Id: socialconnect.php 2437 2013-01-29 14:14:53Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

$view = JRequest::getCmd('view', 'default');

if (JFile::exists(JPATH_COMPONENT.'/controllers/'.$view.'.php'))
{
	JLoader::register('SocialConnectController', JPATH_COMPONENT_ADMINISTRATOR.'/controllers/controller.php');
	JLoader::register('SocialConnectModel', JPATH_COMPONENT_ADMINISTRATOR.'/models/model.php');
	JLoader::register('SocialConnectView', JPATH_COMPONENT_ADMINISTRATOR.'/views/view.php');
	JLoader::register('SocialConnectHelper', JPATH_COMPONENT.'/helpers/socialconnect.php');
	require_once (JPATH_COMPONENT.'/controllers/'.$view.'.php');
	$classname = 'SocialConnectController'.$view;
	$controller = new $classname();
	$controller->execute(JRequest::getWord('task'));
	$controller->redirect();
}
