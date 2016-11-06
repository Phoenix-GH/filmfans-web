<?php
/**
 * @version		$Id: socialconnectgoogle.php 3390 2013-07-18 11:49:42Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');
jimport('joomla.user.helper');

class plgAuthenticationSocialConnectGoogle extends JPlugin
{

	function plgAuthenticationSocialConnectGoogle(&$subject, $config)
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
		$socialConnectGoogleAccessToken = $session->get('socialConnectGoogleAccessToken');
		$socialConnectGooglePlusAccessToken = $session->get('socialConnectGooglePlusAccessToken');
		$socialConnectServive = $session->get('socialConnectService');

		if ($socialConnectGoogleAccessToken && $socialConnectServive == 'google')
		{
			$parameters = array('access_token' => $socialConnectGoogleAccessToken);
			JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
			$result = SocialConnectHelper::request('https://www.googleapis.com/oauth2/v1/userinfo', $parameters, 'GET');
			if (extension_loaded('json'))
			{
				$data = json_decode($result);
			}
			if (isset($data->picture))
			{
				$data->image = $data->picture;
			}
			$data->type = $socialConnectServive;
		}

		if ($socialConnectGooglePlusAccessToken && $socialConnectServive == 'googleplus')
		{
			$parameters = array('access_token' => $socialConnectGooglePlusAccessToken);
			JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
			$result = SocialConnectHelper::request('https://www.googleapis.com/oauth2/v1/userinfo', $parameters, 'GET');
			if (extension_loaded('json'))
			{
				$data = json_decode($result);
			}
			if (isset($data->picture))
			{
				$data->image = $data->picture;
			}
			$data->type = $socialConnectServive;

			$result = SocialConnectHelper::request('https://www.googleapis.com/plus/v1/people/'.$data->id, $parameters, 'GET');
			if (extension_loaded('json'))
			{
				$plusData = json_decode($result);
			}
			$data->name = $plusData->displayName;
			$data->image = $plusData->image->url;

		}

		// Set response and session data on success
		if ($data)
		{

			// Try to detect existing user from email or possible username values
			$account = SocialConnectHelper::getUserAccount('google', $data->id, $data->email, $data->name);

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
				$response->type = 'SocialConnect - Google';

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
					$response->username = SocialConnectHelper::generateUsername('google', $data->id, $data->email, $data->name);
					$response->fullname = $data->name;
					$response->email = $data->email;
					$response->password = '';
				}
			}
		}
	}

}
