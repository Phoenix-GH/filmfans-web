<?php
/**
 * @version		$Id: login.php 3360 2013-07-15 12:37:38Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class SocialConnectControllerLogin extends SocialConnectController
{

	public function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'login');
		parent::display(false);
	}

	public function twitter()
	{
		$view = $this->getView('login', 'html');
		$view->setLayout('twitter');
		$view->twitter();
		return $this;
	}

	public function ning()
	{
		JRequest::setVar('tmpl', 'component');
		$user = JFactory::getUser();
		$this->setReturn();
		if (!$user->guest)
		{
			echo $this->addHeadCode();
			return $this;
		}
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("window.addEvent('domready', function() {if(window.opener) {
			var buffer = 60;
			var content = document.getElementById('comSocialConnectContainer');
			var vph = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
			var browser = window.outerHeight || vph + 60;
			var vpw = window.innerWidth || document.documentElement.clientWidth + 30 || document.body.clientWidth + 30;
			if(vph < content.offsetHeight + buffer) {
				window.resizeTo(vpw, content.offsetHeight + (browser - vph) + buffer);
			}
			}});");
		$view = $this->getView('login', 'html');
		$view->setLayout('ning');
		$view->ning();
		return $this;
	}

	public function facebookOauth()
	{
		return $this->_authorize('Facebook');
	}

	public function googlePlusOauth()
	{
		return $this->_authorize('GooglePlus');
	}

	public function googleOauth()
	{
		return $this->_authorize('Google');
	}

	public function githubOauth()
	{
		return $this->_authorize('Github');
	}

	public function wordpressOauth()
	{
		return $this->_authorize('Wordpress');
	}

	public function windowsOauth()
	{
		return $this->_authorize('Windows');
	}

	public function twitterOauth()
	{
		return $this->_authorize('Twitter');
	}

	public function linkedInOauth()
	{
		return $this->_authorize('LinkedIn');
	}

	private function _authorize($service)
	{
		JRequest::setVar('tmpl', 'component');
		$params = JComponentHelper::getParams('com_socialconnect');
		$user = JFactory::getUser();
		if (!$user->guest)
		{
			$this->addHeadCode();
			return $this;
		}
		require_once JPATH_SITE.'/components/com_socialconnect/helpers/oauth.php';
		SocialConnectOAuthHelper::$service = $service;
		switch($service)
		{
			case 'Facebook' :
				SocialConnectOAuthHelper::$authorization_endpoint = 'https://www.facebook.com/dialog/oauth';
				SocialConnectOAuthHelper::$token_endpoint = 'https://graph.facebook.com/oauth/access_token';
				SocialConnectOAuthHelper::$redirect_uri = JURI::root(false).'index.php?option=com_socialconnect&view=login&task=facebookoauth';
				SocialConnectOAuthHelper::$client_id = $params->get('facebookApplicationId');
				SocialConnectOAuthHelper::$client_secret = $params->get('facebookApplicationSecret');
				SocialConnectOAuthHelper::$scope = 'email';
				break;
			case 'GooglePlus' :
				SocialConnectOAuthHelper::$authorization_endpoint = 'https://accounts.google.com/o/oauth2/auth';
				SocialConnectOAuthHelper::$token_endpoint = 'https://accounts.google.com/o/oauth2/token';
				SocialConnectOAuthHelper::$redirect_uri = JURI::root(false).'index.php?option=com_socialconnect&view=login&task=googleplusoauth';
				SocialConnectOAuthHelper::$client_id = $params->get('googleClientId');
				SocialConnectOAuthHelper::$client_secret = $params->get('googleClientSecret');
				SocialConnectOAuthHelper::$scope = 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email';
				break;
			case 'Google' :
				SocialConnectOAuthHelper::$authorization_endpoint = 'https://accounts.google.com/o/oauth2/auth';
				SocialConnectOAuthHelper::$token_endpoint = 'https://accounts.google.com/o/oauth2/token';
				SocialConnectOAuthHelper::$redirect_uri = JURI::root(false).'index.php?option=com_socialconnect&view=login&task=googleoauth';
				SocialConnectOAuthHelper::$client_id = $params->get('googleClientId');
				SocialConnectOAuthHelper::$client_secret = $params->get('googleClientSecret');
				SocialConnectOAuthHelper::$scope = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';
				break;
			case 'Github' :
				SocialConnectOAuthHelper::$authorization_endpoint = 'https://github.com/login/oauth/authorize';
				SocialConnectOAuthHelper::$token_endpoint = 'https://github.com/login/oauth/access_token';
				SocialConnectOAuthHelper::$redirect_uri = JURI::root(false).'index.php?option=com_socialconnect&view=login&task=githuboauth';
				SocialConnectOAuthHelper::$client_id = $params->get('githubClientId');
				SocialConnectOAuthHelper::$client_secret = $params->get('githubClientSecret');
				SocialConnectOAuthHelper::$scope = 'user:email';
				break;
			case 'Wordpress' :
				SocialConnectOAuthHelper::$authorization_endpoint = 'https://public-api.wordpress.com/oauth2/authorize';
				SocialConnectOAuthHelper::$token_endpoint = 'https://public-api.wordpress.com/oauth2/token';
				SocialConnectOAuthHelper::$redirect_uri = JURI::root(false).'index.php?option=com_socialconnect&view=login&task=wordpressoauth';
				SocialConnectOAuthHelper::$client_id = $params->get('wpClientId');
				SocialConnectOAuthHelper::$client_secret = $params->get('wpClientSecret');
				break;
			case 'Windows' :
				SocialConnectOAuthHelper::$authorization_endpoint = 'https://login.live.com/oauth20_authorize.srf';
				SocialConnectOAuthHelper::$token_endpoint = 'https://login.live.com/oauth20_token.srf';
				SocialConnectOAuthHelper::$redirect_uri = JURI::root(false).'index.php?option=com_socialconnect&view=login&task=windowsoauth';
				SocialConnectOAuthHelper::$client_id = $params->get('winClientId');
				SocialConnectOAuthHelper::$client_secret = $params->get('winClientSecret');
				SocialConnectOAuthHelper::$scope = 'wl.basic,wl.emails';
				break;
			case 'Twitter' :
				SocialConnectOAuthHelper::$version = '1.0';
				SocialConnectOAuthHelper::$consumer_key = $params->get('twitterConsumerKey');
				SocialConnectOAuthHelper::$consumer_secret = $params->get('twitterConsumerSecret');
				SocialConnectOAuthHelper::$endpoint = 'https://api.twitter.com/oauth/';
				SocialConnectOAuthHelper::$oauth_callback = JURI::root(false).'index.php?option=com_socialconnect&view=login&task=twitteroauth';
				break;
			case 'LinkedIn' :
				SocialConnectOAuthHelper::$authorization_endpoint = 'https://www.linkedin.com/uas/oauth2/authorization';
				SocialConnectOAuthHelper::$token_endpoint = 'https://www.linkedin.com/uas/oauth2/accessToken';
				SocialConnectOAuthHelper::$redirect_uri = JURI::root(false).'index.php?option=com_socialconnect&view=login&task=linkedinoauth';
				SocialConnectOAuthHelper::$client_id = $params->get('linkedInApiKey');
				SocialConnectOAuthHelper::$client_secret = $params->get('linkedInApiSecret');
				SocialConnectOAuthHelper::$scope = 'r_basicprofile r_emailaddress';
				break;
		}
		$result = SocialConnectOAuthHelper::authorize();
		if ($result)
		{
			// Set session variables
			$session = JFactory::getSession();
			$session->set('socialConnectService', strtolower($service));

			// OAuth 2.0
			$session->set('socialConnect'.$service.'AccessToken', SocialConnectOAuthHelper::$access_token);
			// OAuth 1.0
			$session->set('socialConnect'.$service.'OauthToken', SocialConnectOAuthHelper::$oauth_token);
			$session->set('socialConnect'.$service.'OauthTokenSecret', SocialConnectOAuthHelper::$oauth_token_secret);
			// Twitter only ...
			$session->set('socialConnect'.$service.'UserID', SocialConnectOAuthHelper::$user_id);

			// Redirect parent window to verify URL
			$this->addHeadCode();
		}
		else
		{
			$application = JFactory::getApplication();
			$application->enqueueMessage(SocialConnectOAuthHelper::$error, 'error');
		}
		return $this;
	}

	private function addHeadCode()
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('window.opener.location = "'.JRoute::_('index.php?option=com_socialconnect&task=verify', false).'"; window.close();');
	}

	private function setReturn()
	{
		$return = JRequest::getVar('return', null, 'GET', 'BASE64');
		if (!is_null($return))
		{
			$session = JFactory::getSession();
			$session->set('socialConnectReturn', base64_decode($return));
		}
	}

}
