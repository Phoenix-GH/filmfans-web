<?php
/**
 * @version		$Id: socialconnecttwitter.php 3014 2013-05-20 11:03:53Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');
jimport('joomla.user.helper');

class plgAuthenticationSocialConnectTwitter extends JPlugin
{

	function plgAuthenticationSocialConnectTwitter(&$subject, $config)
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
		$socialConnectTwitterUserID = $session->get('socialConnectTwitterUserID');
		$socialConnectTwitterOauthToken = $session->get('socialConnectTwitterOauthToken');
		$socialConnectTwitterOauthTokenSecret = $session->get('socialConnectTwitterOauthTokenSecret');
		$socialConnectServive = $session->get('socialConnectService');

		if ($socialConnectTwitterOauthToken && $socialConnectTwitterOauthTokenSecret && $socialConnectTwitterUserID && $socialConnectServive == 'twitter')
		{
			// Load library
			JLoader::register('tmhOAuth', JPATH_SITE.'/components/com_socialconnect/lib/tmhOAuth.php');
			$consumerKey = $params->get('twitterConsumerKey');
			$consumerSecret = $params->get('twitterConsumerSecret');
			$tmhOAuth = new tmhOAuth( array(
				'consumer_key' => $consumerKey,
				'consumer_secret' => $consumerSecret,
				'user_token' => $socialConnectTwitterOauthToken,
				'user_secret' => $socialConnectTwitterOauthTokenSecret
			));
			$tmhOAuth->request('GET', $tmhOAuth->url('1.1/users/show', 'json'), array('user_id' => $socialConnectTwitterUserID));
			if (extension_loaded('json'))
			{
				$data = json_decode($tmhOAuth->response['response']);
			}
			if (isset($data->url))
			{
				$data->link = $data->url;
			}
			$data->image = $data->profile_image_url;
			$data->type = 'twitter';
		}

		// Set response and session data on success
		if ($data)
		{

			// Try to detect existing user from email or possible username values
			$account = SocialConnectHelper::getUserAccount('twitter', $data->screen_name, $data->screen_name.'@twitter', $data->name);

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
				$response->type = 'SocialConnect - Twitter';

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
					$response->username = SocialConnectHelper::generateUsername('twitter', $data->screen_name, $email, $data->name);
					$response->fullname = $data->name;
					$response->email = $data->screen_name.'@twitter';
					$response->password = '';
				}
			}
		}
	}

}
