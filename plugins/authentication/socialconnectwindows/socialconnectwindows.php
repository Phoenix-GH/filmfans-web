<?php
/**
 * @version		$Id: socialconnectwindows.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgAuthenticationSocialConnectWindows extends JPlugin
{

	function plgAuthenticationSocialConnectWindows(&$subject, $config)
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

		// Init login status and user data
		$response->status = version_compare(JVERSION, '3.0', 'ge') ? JAuthentication::STATUS_FAILURE : JAUTHENTICATE_STATUS_FAILURE;
		$data = false;

		// Check for access token
		$session = JFactory::getSession();
		$socialConnectWindowsAccessToken = $session->get('socialConnectWindowsAccessToken');
		$socialConnectServive = $session->get('socialConnectService');

		if ($socialConnectWindowsAccessToken && $socialConnectServive == 'windows')
		{
			JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
			$parameters = array('access_token' => $socialConnectWindowsAccessToken);
			$result = SocialConnectHelper::request('https://apis.live.net/v5.0/me', $parameters, 'GET');
			$data = json_decode($result);
			$data->image = 'https://apis.live.net/v5.0/me/picture?access_token='.$socialConnectWindowsAccessToken;
			$data->type = 'windows';
		}

		// Set response and session data on success
		if ($data)
		{

			// Try to detect existing user from email or possible username values
			$account = SocialConnectHelper::getUserAccount('windows', $data->id, $data->emails->preferred, $data->name);

			// If registrations are disabled do not allow login by new users
			if (!SocialConnectHelper::canLogin($account))
			{
				$response->error_message = JText::_('JW_SC_USERS_REGISTRATION_IS_CURRENTLY_DISABLED');
			}
			else
			{
				// Store network profile data to session
				$session->set('socialConnectData', $data);

				// Set authentication success
				$response->status = version_compare(JVERSION, '3.0', 'ge') ? JAuthentication::STATUS_SUCCESS : JAUTHENTICATE_STATUS_SUCCESS;

				// Set authentication type
				$response->type = 'SocialConnect - Microsoft account';

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
					$response->username = SocialConnectHelper::generateUsername('windows', $data->id, $data->emails->preferred, $data->name);
					$response->fullname = $data->name;
					$response->email = $data->emails->preferred;
					$response->password = '';
				}
			}
		}
	}

}
