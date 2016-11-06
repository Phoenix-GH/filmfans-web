<?php
/**
 * @version		$Id: socialconnectfacebook.php 3390 2013-07-18 11:49:42Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgAuthenticationSocialConnectFacebook extends JPlugin
{

	function plgAuthenticationSocialConnectFacebook(&$subject, $config)
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
		$socialConnectFacebookAccessToken = $session->get('socialConnectFacebookAccessToken');
		$socialConnectServive = $session->get('socialConnectService');

		if ($socialConnectFacebookAccessToken && $socialConnectServive == 'facebook')
		{
			$parameters = array('access_token' => $socialConnectFacebookAccessToken);
			JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
			$result = SocialConnectHelper::request('https://graph.facebook.com/me', $parameters, 'GET');
			if (extension_loaded('json'))
			{
				$data = json_decode($result);
			}
			$data->image = 'https://graph.facebook.com/'.$data->id.'/picture';
			if (isset($data->about))
			{
				$data->description = $data->about;
			}
			$data->type = 'facebook';
		}

		// Set response and session data on success
		if ($data)
		{
			// Try to detect existing user from email or possible username values
			$account = SocialConnectHelper::getUserAccount('facebook', $data->id, $data->email, $data->name);

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
				$response->type = 'SocialConnect - Facebook';

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
					$response->username = SocialConnectHelper::generateUsername('facebook', $data->id, $data->email, $data->name);
					$response->fullname = $data->name;
					$response->email = $data->email;
					$response->password = '';
				}
			}
		}
	}

}
