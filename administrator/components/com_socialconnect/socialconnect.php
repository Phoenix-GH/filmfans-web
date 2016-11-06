<?php
/**
 * @version		$Id: socialconnect.php 3438 2013-07-29 16:05:27Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die;

// Check access permission
if (version_compare(JVERSION, '1.6.0', 'ge'))
{
	$user = JFactory::getUser();
	if (!$user->authorise('core.manage', 'com_socialconnect'))
	{
		JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php');
	}
}

// Add styles
$document = JFactory::getDocument();
$document->addStyleSheet('http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700&subset=latin,cyrillic,greek,vietnamese');
$document->addStyleSheet(JURI::base(true).'/components/com_socialconnect/css/font.css');
$document->addStyleSheet(JURI::base(true).'/components/com_socialconnect/css/component.css?v=1.5.1');
$document->addStyleSheet(JURI::base(true).'/components/com_socialconnect/css/style.css?v=1.5.1');


// Load base classes
JLoader::register('SocialConnectController', JPATH_COMPONENT.'/controllers/controller.php');
JLoader::register('SocialConnectModel', JPATH_COMPONENT.'/models/model.php');
JLoader::register('SocialConnectView', JPATH_COMPONENT.'/views/view.php');

// Bootstrap
$view = JRequest::getCmd('view', 'dashboard');
JLoader::register('SocialConnectController'.$view, JPATH_COMPONENT.'/controllers/'.$view.'.php');
$class = 'SocialConnectController'.$view;
$controller = new $class();
$controller->execute(JRequest::getWord('task'));
$controller->redirect();
