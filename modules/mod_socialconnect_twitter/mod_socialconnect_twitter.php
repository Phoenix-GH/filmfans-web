<?php
/**
 * @version		$Id: mod_socialconnect_twitter.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
require_once (dirname(__FILE__).'/helper.php');
$tweets = modSocialConnectTwitterHelper::getLatestTweets($params);
$moduleClassSuffix = $params->get('moduleclass_sfx');
if ($params->get('loadCSS'))
{
	SocialConnectHelper::loadModuleCSS('mod_socialconnect_twitter', $params->get('template', 'default').'/css/style.css');
}
require (JModuleHelper::getLayoutPath('mod_socialconnect_twitter', $params->get('template', 'default').'/default'));
