<?php
/**
 * @version		$Id: socialconnectning.php 3014 2013-05-20 11:03:53Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgAuthenticationSocialConnectNing extends JPlugin
{

	function plgAuthenticationSocialConnectNing(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onUserAuthenticate($credentials, $options, &$response)
	{
		$this->onAuthenticate($credentials, $options, $response);
	}

	function onAuthenticate($credentials, $options, &$response)
	{

		// Front-end only
		$application = JFactory::getApplication();
		if ($application->isAdmin())
		{
			return false;
		}

		$session = JFactory::getSession();
		$params = JComponentHelper::getParams('com_socialconnect');
		$domain = $params->get('ningDomain');

		// Init login status and user data
		$response->status = version_compare(JVERSION, '3.0', 'ge') ? JAuthentication::STATUS_FAILURE : JAUTHENTICATE_STATUS_FAILURE;
		$data = false;

		if ($domain)
		{
			$domain = str_replace(array(
				'http://',
				'https://'
			), array(
				'',
				''
			), $domain);
			$url = "http://".$domain."/main/external/auth?format=serialize";
			$parameters = array(
				'email' => $credentials['username'],
				'password' => $credentials['password']
			);
			JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
			$result = SocialConnectHelper::request($url, $parameters, 'POST');
			if ($result && is_array($info = unserialize($result)) && count($info) && $info['email'] && $info['name'])
			{

				// Try to detect existing user from email or possible username values
				$account = SocialConnectHelper::getUserAccount('ning', md5($domain.$info['email']), $info['email'], $info['name']);

				// If registrations are disabled do not allow login by new users
				if (!SocialConnectHelper::canLogin($account))
				{
					$response->error_message = JText::_('JW_SC_USERS_REGISTRATION_IS_CURRENTLY_DISABLED');
				}
				else
				{
					// Store network profile data to session
					$data = new stdClass;
					$data->image = $info['avatar_url'];
					$data->type = 'ning';
					$session->set('socialConnectData', $data);

					// Set authentication success
					$response->status = version_compare(JVERSION, '3.0', 'ge') ? JAuthentication::STATUS_SUCCESS : JAUTHENTICATE_STATUS_SUCCESS;

					// Set authentication type
					$response->type = 'SocialConnect - Ning';

					// Empty any error messages
					$response->error_message = '';

					// Set the rest response attributes based on the user account
					if ($account)
					{
						$response->username = $account->username;
						$response->fullname = $account->name;
						$response->email = $account->email;
					}
					else
					{
						// Generate username
						$response->username = SocialConnectHelper::generateUsername('ning', md5($domain.$info['email']), $info['email'], $info['name']);
						$response->fullname = $data->name;
						$response->email = $data->email;
						$response->password = '';
					}
				}
			}
		}
	}

}
