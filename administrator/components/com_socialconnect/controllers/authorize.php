<?php
/**
 * @version		$Id: authorize.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

require_once JPATH_SITE.'/components/com_socialconnect/helpers/oauth.php';
require_once JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php';

class SocialConnectControllerAuthorize extends SocialConnectController
{

	function display($cachable = false, $urlparams = false)
	{
		$application = JFactory::getApplication();
		$service = JRequest::getCmd('service');
		$this->setService($service);
		$result = SocialConnectOAuthHelper::authorize();
		if ($result)
		{
			$this->handleServiceToken();
			return $this;
		}
		else
		{
			$application->enqueueMessage(SocialConnectOAuthHelper::$error, 'error');
			return $this;
		}
	}

	private function handleServiceToken()
	{
		$params = JComponentHelper::getParams('com_socialconnect');
		switch(SocialConnectOAuthHelper::$service)
		{
			case 'facebook' :

				// Get the current user
				$parameters = array('access_token' => SocialConnectOAuthHelper::$access_token);
				$result = SocialConnectHelper::request('https://graph.facebook.com/me', $parameters, 'GET');
				$profile = json_decode($result);

				// Get all user pages
				$parameters = array('access_token' => SocialConnectOAuthHelper::$access_token);
				$result = SocialConnectHelper::request('https://graph.facebook.com/me/accounts', $parameters, 'GET');
				$pages = json_decode($result);
				$accounts = array();
				$tmp = new stdClass;
				$tmp->id = $profile->id;
				$tmp->name = $profile->name;
				$accounts[] = $tmp;
				foreach ($pages->data as $page)
				{
					$tmp = new stdClass;
					$tmp->id = $page->id;
					$tmp->name = $page->name;
					$accounts[] = $tmp;
				}

				// Get app token. Store it for future usage.
				$parameters = array(
					'client_id' => SocialConnectOAuthHelper::$client_id,
					'client_secret' => SocialConnectOAuthHelper::$client_secret,
					'grant_type' => 'client_credentials'
				);
				$result = SocialConnectHelper::request('https://graph.facebook.com/oauth/access_token', $parameters);
				parse_str($result);
				if (isset($access_token) && !empty($access_token))
				{
					$this->updateSettings('facebook', $accounts, $access_token);
				}
				break;
			case 'twitter' :
				$this->updateSettings('twitter', SocialConnectOAuthHelper::$screen_name, SocialConnectOAuthHelper::$oauth_token, SocialConnectOAuthHelper::$oauth_token_secret);
				break;
		}
		return $this;
	}

	private function setService($service)
	{
		$params = JComponentHelper::getParams('com_socialconnect');
		SocialConnectOAuthHelper::$service = $service;
		switch($service)
		{
			case 'facebook' :
				SocialConnectOAuthHelper::$authorization_endpoint = 'https://www.facebook.com/dialog/oauth';
				SocialConnectOAuthHelper::$token_endpoint = 'https://graph.facebook.com/oauth/access_token';
				SocialConnectOAuthHelper::$redirect_uri = JURI::base(false).'index.php?option=com_socialconnect&view=authorize&service=facebook';
				SocialConnectOAuthHelper::$client_id = $params->get('facebookApplicationId');
				SocialConnectOAuthHelper::$client_secret = $params->get('facebookApplicationSecret');
				SocialConnectOAuthHelper::$scope = 'publish_actions';
				break;
			case 'twitter' :
				SocialConnectOAuthHelper::$version = '1.0';
				SocialConnectOAuthHelper::$consumer_key = $params->get('twitterConsumerKey');
				SocialConnectOAuthHelper::$consumer_secret = $params->get('twitterConsumerSecret');
				SocialConnectOAuthHelper::$endpoint = 'https://api.twitter.com/oauth/';
				SocialConnectOAuthHelper::$oauth_callback = JURI::base(false).'index.php?option=com_socialconnect&view=authorize&service=twitter';
				break;
		}
	}

	private function updateSettings($service, $accounts, $token, $secret = null)
	{
		$script = '';
		if (is_array($accounts))
		{
			foreach ($accounts as $account)
			{
				$script .= '
				var option = document.createElement("option"); 
				option.text = "'.$account->name.'"; option.value = "'.htmlspecialchars($account->id, ENT_QUOTES, 'UTF-8').'"; 
				window.opener.document.getElementById("social-connect-'.$service.'-account").add(option, null);';
			}
			$script .= 'window.opener.document.getElementById("social-connect-'.$service.'-available-accounts").value = "'.htmlspecialchars(json_encode($accounts), ENT_QUOTES, 'UTF-8').'";';
		}
		else
		{
			$script .= 'window.opener.document.getElementById("social-connect-'.$service.'-account").value = "'.htmlspecialchars($accounts, ENT_QUOTES, 'UTF-8').'";';
		}
		$script .= 'window.opener.document.getElementById("social-connect-'.$service.'-token").value = "'.htmlspecialchars($token, ENT_QUOTES, 'UTF-8').'";
		window.opener.document.getElementById("social-connect-'.$service.'-secret").value = "'.htmlspecialchars($secret, ENT_QUOTES, 'UTF-8').'";
		window.close();';
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
	}

}
