<?php
/**
 * @version		$Id: view.html.php 3421 2013-07-24 16:40:45Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class SocialConnectViewDashboard extends SocialConnectView
{

	function display($tpl = null)
	{
		jimport('joomla.filesystem.file');
		$user = JFactory::getUser();
		$params = JComponentHelper::getParams('com_socialconnect');
		$checks = array();

		$services = array();

		// Facebook
		$service = new stdClass;
		$service->name = 'Facebook';
		$service->suffix = 'FB';
		$service->auth = (bool)JPluginHelper::isEnabled('authentication', 'socialconnectfacebook');
		$service->api = (bool)($params->get('facebookApplicationId') && $params->get('facebookApplicationSecret'));
		$service->comments = (bool)($params->get('facebookCommentsApplicationId') && ($params->get('commentsService') == 'facebook'));
		$service->sharing = (bool)($params->get('facebookAutoPost') && $params->get('facebookAutoPostData'));
		$services[] = $service;

		// Twitter
		$service = new stdClass;
		$service->name = 'Twitter';
		$service->suffix = 'TW';
		$service->auth = (bool)JPluginHelper::isEnabled('authentication', 'socialconnecttwitter');
		$service->api = (bool)($params->get('twitterConsumerKey') && $params->get('twitterConsumerSecret'));
		$service->comments = null;
		$service->sharing = (bool)($params->get('twitterAutoPost') && $params->get('twitterAutoPost'));
		$services[] = $service;

		// Google
		$service = new stdClass;
		$service->name = 'Google';
		$service->suffix = 'GO';
		$service->auth = (bool)JPluginHelper::isEnabled('authentication', 'socialconnectgoogle');
		$service->api = (bool)($params->get('googleClientId') && $params->get('googleClientSecret'));
		$service->comments = null;
		$service->sharing = null;
		$services[] = $service;

		// Google+
		$service = new stdClass;
		$service->name = 'Google+';
		$service->suffix = 'GOPL';
		$service->auth = (bool)JPluginHelper::isEnabled('authentication', 'socialconnectgoogle');
		$service->api = (bool)($params->get('googleClientId') && $params->get('googleClientSecret'));
		$service->comments = null;
		$service->sharing = null;
		$services[] = $service;

		// LinkedIn
		$service = new stdClass;
		$service->name = 'LinkedIn';
		$service->suffix = 'LI';
		$service->auth = (bool)JPluginHelper::isEnabled('authentication', 'socialconnectlinkedin');
		$service->api = (bool)($params->get('linkedInApiKey') && $params->get('linkedInApiSecret'));
		$service->comments = null;
		$service->sharing = null;
		$services[] = $service;

		// GitHub
		$service = new stdClass;
		$service->name = 'GitHub';
		$service->suffix = 'GH';
		$service->auth = (bool)JPluginHelper::isEnabled('authentication', 'socialconnectgithub');
		$service->api = (bool)($params->get('githubClientId') && $params->get('githubClientSecret'));
		$service->comments = null;
		$service->sharing = null;
		$services[] = $service;

		// WordPress
		$service = new stdClass;
		$service->name = 'WordPress';
		$service->suffix = 'WP';
		$service->auth = (bool)JPluginHelper::isEnabled('authentication', 'socialconnectwordpress');
		$service->api = (bool)($params->get('wpClientId') && $params->get('wpClientSecret'));
		$service->comments = null;
		$service->sharing = null;
		$services[] = $service;

		// Microsoft
		$service = new stdClass;
		$service->name = 'Microsoft';
		$service->suffix = 'MS';
		$service->auth = (bool)JPluginHelper::isEnabled('authentication', 'socialconnectwindows');
		$service->api = (bool)($params->get('winClientId') && $params->get('winClientSecret'));
		$service->comments = null;
		$service->sharing = null;
		$services[] = $service;

		// Ning
		$service = new stdClass;
		$service->name = 'Ning';
		$service->suffix = 'NI';
		$service->auth = (bool)JPluginHelper::isEnabled('authentication', 'socialconnectning');
		$service->api = (bool)$params->get('ningDomain');
		$service->comments = null;
		$service->sharing = null;
		$services[] = $service;

		// DISQUS
		$service = new stdClass;
		$service->name = 'DISQUS';
		$service->suffix = 'DI';
		$service->auth = null;
		$service->api = null;
		$service->comments = (bool)($params->get('disqusShortName') && ($params->get('commentsService') == 'disqus'));
		$service->sharing = null;
		$services[] = $service;

		$checks['services'] = $services;

		$checks['userPlugin'] = JPluginHelper::isEnabled('user', 'socialconnect');
		$checks['php'] = phpversion();
		$checks['curl'] = extension_loaded('curl');
		$checks['hash_hmac'] = function_exists('hash_hmac');
		$checks['json'] = extension_loaded('json');
		$this->assignRef('checks', $checks);
		if ($checks['userPlugin'])
		{
			$application = JFactory::getApplication();
			$db = JFactory::getDBO();
			if (version_compare(JVERSION, '2.5', 'ge'))
			{
				$db->setQuery("SELECT element, ordering FROM #__extensions WHERE type = 'plugin' AND folder = 'user' AND (element = 'joomla' OR element = 'socialconnect')");
			}
			else
			{
				$db->setQuery("SELECT element, ordering FROM #__plugins WHERE folder = 'user' AND (element = 'joomla' OR element = 'socialconnect')");
			}
			$plugins = $db->loadObjectList();
			$orderingValues = array();
			foreach ($plugins as $plugin)
			{
				$orderingValues[$plugin->element] = $plugin->ordering;
			}
			if ($orderingValues['joomla'] > $orderingValues['socialconnect'])
			{
				$application->enqueueMessage(JText::_('JW_SC_COM_USER_PLUGIN_ORDERING_NOTICE'), 'notice');
			}
			if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_k2/k2.php'))
			{
				$db->setQuery("SELECT COUNT(*) FROM #__k2_user_groups");
				$result = $db->loadResult();
				if (!$result)
				{
					$application->enqueueMessage(JText::_('JW_SC_COM_K2_USERGROUPS_NOTICE'), 'notice');
				}
			}
		}

		$userCanEditSettings = true;
		if (version_compare(JVERSION, '2.5.0', 'ge'))
		{
			$userCanEditSettings = $user->authorise('core.admin', 'com_socialconnect');
		}
		$this->assignRef('userCanEditSettings', $userCanEditSettings);
		
		$application = JFactory::getApplication();
		$messages = $application->getMessageQueue();
		$this->assignRef('messages', $messages);
		
		parent::display($tpl);
	}

}
