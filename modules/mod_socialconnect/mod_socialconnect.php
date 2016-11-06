<?php
/**
 * @version		$Id: mod_socialconnect.php 2437 2013-01-29 14:14:53Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
$user = JFactory::getUser();
SocialConnectHelper::loadHeadData($params, 'module');
SocialConnectHelper::setUserData($user);
$variables = SocialConnectHelper::setVariables($params);
foreach ($variables as $key => $value)
{
	$$key = $value;
}
$layout = ($user->guest) ? 'default' : 'authenticated';
$alignmentClass = $params->get('alignment', 'left') == 'right' ? 'socialConnectRight' : 'socialConnectLeft';
require (JModuleHelper::getLayoutPath('mod_socialconnect', $params->get('template', 'default').'/'.$layout));
