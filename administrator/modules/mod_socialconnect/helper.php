<?php
/**
 * @version		$Id: helper.php 3315 2013-07-09 16:26:36Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die ;

class modSocialConnectHelper
{
	public static function display()
	{
		$params = JComponentHelper::getParams('com_socialconnect');
		$services = array();

		$service = new stdClass;
		$service->name = 'Joomla';
		$service->title = JText::_('JW_SC_BACKEND_MOD_'.JString::strtoupper($service->name).'_COUNT');
		$services[] = $service;

		$providers = array();
		$providers['Facebook'] = JPluginHelper::isEnabled('authentication', 'socialconnectfacebook') && $params->get('facebookApplicationId') && $params->get('facebookApplicationSecret');
		$providers['Twitter'] = JPluginHelper::isEnabled('authentication', 'socialconnecttwitter') && $params->get('twitterConsumerKey') && $params->get('twitterConsumerSecret');
		$providers['Google'] = JPluginHelper::isEnabled('authentication', 'socialconnectgoogle') && $params->get('googleClientId') && $params->get('googleClientSecret') && ($params->get('googleAuthType') == 'google' || $params->get('googleAuthType') == 'both');
		$providers['GooglePlus'] = JPluginHelper::isEnabled('authentication', 'socialconnectgoogle') && $params->get('googleClientId') && $params->get('googleClientSecret') && ($params->get('googleAuthType') == 'google+' || $params->get('googleAuthType') == 'both');
		$providers['LinkedIn'] = JPluginHelper::isEnabled('authentication', 'socialconnectlinkedin') && $params->get('linkedInApiKey') && $params->get('linkedInApiSecret');
		$providers['Github'] = JPluginHelper::isEnabled('authentication', 'socialconnectgithub') && $params->get('githubClientId') && $params->get('githubClientSecret');
		$providers['Wordpress'] = JPluginHelper::isEnabled('authentication', 'socialconnectwordpress') && $params->get('wpClientId') && $params->get('wpClientSecret');
		$providers['Windows'] = JPluginHelper::isEnabled('authentication', 'socialconnectwindows') && $params->get('winClientId') && $params->get('winClientSecret');
		$providers['Ning'] = JPluginHelper::isEnabled('authentication', 'socialconnectning') && $params->get('ningDomain');
		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__socialconnect_sessions WHERE NOT EXISTS (SELECT * FROM #__session WHERE session_id = #__socialconnect_sessions.session_id)");
		$db->query();
		$sum = 0;
		foreach ($providers as $provider => $status)
		{
			if ($status)
			{
				$query = "SELECT COUNT(*) FROM #__socialconnect_sessions WHERE type = ".$db->quote(JString::strtolower($provider))." AND EXISTS (SELECT * FROM #__session WHERE session_id = #__socialconnect_sessions.session_id)";
				$db->setQuery($query);
				$result = $db->loadResult();
				$service = new stdClass;
				$service->name = $provider;
				$service->title = JText::_('JW_SC_BACKEND_MOD_'.JString::strtoupper($service->name).'_COUNT');
				$service->value = $result;
				$services[] = $service;
				$sum += $result;
			}
		}

		$query = 'SELECT COUNT(session_id) FROM #__session WHERE client_id = 0';
		$db->setQuery($query);
		$total = $db->loadResult();

		$query = 'SELECT COUNT(session_id) FROM #__session WHERE guest = 0 AND client_id = 0';
		$db->setQuery($query);
		$loggedIn = $db->loadResult();
		$joomla = $loggedIn - $sum;
		if ($joomla < 0)
		{
			$joomla = 0;
		}
		$services[0]->value = $joomla;
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$containerClass = 'socialConnectPopUp';
			$togglerClass = 'socialConnectArrowUp';
			$cmsVersionClass = 'isJ3';
		}
		else
		{
			$containerClass = 'socialConnectPopDown';
			$togglerClass = 'socialConnectArrowDown';
			$cmsVersionClass = version_compare(JVERSION, '2.5', 'ge') ? 'isJ25' : 'isJ15';
		}
		require (JModuleHelper::getLayoutPath('mod_socialconnect', 'default'));
	}

}
