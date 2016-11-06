<?php
/**
 * @version		$Id: socialconnectwordpress.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgAuthenticationSocialConnectWordPress extends JPlugin
{

	function plgAuthenticationSocialConnectWordPress(&$subject, $config)
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
		$socialConnectWordpressAccessToken = $session->get('socialConnectWordpressAccessToken');
		$socialConnectServive = $session->get('socialConnectService');

		if ($socialConnectWordpressAccessToken && $socialConnectServive == 'wordpress')
		{
			JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
			$parameters = array('pretty' => '1');
			$options = array(CURLOPT_HTTPHEADER => array('Authorization: Bearer '.$socialConnectWordpressAccessToken));
			$result = SocialConnectHelper::request('https://public-api.wordpress.com/rest/v1/me/', $parameters, 'GET', $options);
			if (extension_loaded('json'))
			{
				$data = json_decode($result);
			}
			$data->image = $data->avatar_URL;
			$data->type = 'wordpress';
		}

		// Set response and session data on success
		if ($data)
		{
			// Try to detect existing user from email or possible username values
			$account = SocialConnectHelper::getUserAccount('wordpress', $data->ID, $data->email, $data->display_name);

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
				$response->type = 'SocialConnect - WordPress';

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
					$response->username = SocialConnectHelper::generateUsername('wordpress', $data->ID, $data->email, $data->display_name);
					$response->fullname = $data->display_name;
					$response->email = $data->email;
					$response->password = '';
				}
			}
		}
	}

}
