<?php
/**
 * @version		$Id: mod_socialconnect.php 3438 2013-07-29 16:05:27Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

require_once (dirname(__FILE__).'/helper.php');

$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true).'/administrator/modules/mod_socialconnect/tmpl/css/template.css?v=1.5.1');

if (version_compare(JVERSION, '3.0', 'ge'))
{
	JHtml::_('jquery.framework');
}
else if (version_compare(JVERSION, '2.5', 'ge'))
{
	JHtml::_('behavior.framework');
}
else
{
	JHtml::_('behavior.mootools');
}

if (version_compare(JVERSION, '3.0', 'ge'))
{
	$js = "jQuery(document).ready(function() {
				jQuery('#socialConnectCountersLoggedInUsersButton').on('click', function(event) {
					event.preventDefault();
					jQuery('#socialConnectCounters').toggleClass('socialConnectModal');
				});
			});";
}
else
{
	$js = "window.addEvent('domready', function() {
				$('socialConnectCountersLoggedInUsersButton').addEvent('click', function(event) {
					event.preventDefault();
					$('socialConnectCounters').toggleClass('socialConnectModal');
				});
			});";
}
$document->addScriptDeclaration($js);

if ($params->get('socialConnectCache', 1) && $mainframe->getCfg('caching'))
{
	$cache = JFactory::getCache('mod_socialconnect_admin');
	$cache->setLifeTime($params->get('cache_time', 900));
	$cache->call(array(
		'modSocialConnectHelper',
		'display'
	));
}
else
{
	modSocialConnectHelper::display($params);
}
