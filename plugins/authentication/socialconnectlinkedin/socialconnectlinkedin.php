<?php
/**
 * @version		$Id: socialconnectlinkedin.php 3390 2013-07-18 11:49:42Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');
jimport('joomla.user.helper');

class plgAuthenticationSocialConnectLinkedIn extends JPlugin
{

	function plgAuthenticationSocialConnectLinkedIn(&$subject, $config)
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

		// Get params
		$params = JComponentHelper::getParams('com_socialconnect');

		// Init login status and user data
		$response->status = version_compare(JVERSION, '3.0', 'ge') ? JAuthentication::STATUS_FAILURE : JAUTHENTICATE_STATUS_FAILURE;
		$data = false;

		// Check for Twitter tokens
		$session = JFactory::getSession();
		$socialConnectLinkedInAccessToken = $session->get('socialConnectLinkedInAccessToken');
		$socialConnectServive = $session->get('socialConnectService');

		if ($socialConnectLinkedInAccessToken && $socialConnectServive == 'linkedin')
		{
			$parameters = array(
				'oauth2_access_token' => $socialConnectLinkedInAccessToken,
				'format' => 'json'
			);
			JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
			$result = SocialConnectHelper::request('https://api.linkedin.com/v1/people/~:(id,first-name,last-name,picture-url,email-address,public-profile-url)', $parameters, 'GET');
			$data = json_decode($result);
			if (isset($data->publicProfileUrl))
			{
				$data->link = $data->publicProfileUrl;
			}
			if (isset($data->pictureUrl))
			{
				$data->image = $data->pictureUrl;
			}
			$data->type = 'linkedin';
		}

		// Set response and session data on success
		if ($data)
		{
			// Try to detect existing user from email or possible username values
			$account = SocialConnectHelper::getUserAccount('linkedin', $data->id, $data->emailAddress, $data->firstName.' '.$data->lastName);

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
				$response->type = 'SocialConnect - LinkedIn';

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
					$response->username = SocialConnectHelper::generateUsername('linkedin', $data->id, $data->emailAddress, $data->firstName.' '.$data->lastName);
					$response->fullname = $data->firstName.' '.$data->lastName;
					$response->email = $data->emailAddress;
					$response->password = '';
				}
			}
		}
	}

}
