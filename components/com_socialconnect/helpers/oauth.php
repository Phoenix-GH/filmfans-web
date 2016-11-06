<?php
/**
 * @version		$Id: oauth.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

require_once JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php';

class SocialConnectOAuthHelper
{
	public static $version = '2.0';
	public static $service = null;
	// OAuth 2.0 params
	public static $authorization_endpoint = null;
	public static $token_endpoint = null;
	public static $redirect_uri = null;
	public static $client_id = null;
	public static $client_secret = null;
	public static $access_token = null;
	public static $scope = null;
	public static $code = null;
	public static $error = null;
	public static $state = null;
	// OAuth 1.0 params
	public static $oauth_token = null;
	public static $oauth_token_secret = null;
	public static $oauth_verifier = null;
	public static $consumer_key = null;
	public static $consumer_secret = null;
	public static $user_token = null;
	public static $user_secret = null;
	public static $endpoint = null;
	public static $oauth_callback = null;
	// Twitter custom variables
	public static $user_id = null;
	public static $screen_name = null;

	public static function authorize()
	{

		if (self::$version == '1.0')
		{
			return self::legacyAuthorize();
		}

		$application = JFactory::getApplication();
		$session = JFactory::getSession();

		self::$code = JRequest::getString('code');
		self::$error = JRequest::getString('error');
		self::$state = JRequest::getString('state');

		if (self::$error)
		{
			return false;
		}
		if (empty(self::$code))
		{
			self::$state = md5(uniqid(rand(), true));
			$session->set('socialConnect'.self::$service.'OAuthState', self::$state);
			$parameters = array(
				'client_id' => self::$client_id,
				'redirect_uri' => self::$redirect_uri,
				'state' => self::$state,
				'scope' => self::$scope,
				'display' => 'popup',
				'response_type' => 'code'
			);
			$url = self::$authorization_endpoint.'?'.http_build_query($parameters, '', '&');
			$application->redirect($url);
		}
		else if (self::$state == $session->get('socialConnect'.self::$service.'OAuthState'))
		{
			$parameters = array(
				'code' => self::$code,
				'client_id' => self::$client_id,
				'client_secret' => self::$client_secret,
				'redirect_uri' => self::$redirect_uri,
				'grant_type' => 'authorization_code'
			);
			$result = SocialConnectHelper::request(self::$token_endpoint, $parameters, 'POST');
			if ($result)
			{
				// HTML attempt
				parse_str($result);
				if (isset($access_token) && !empty($access_token))
				{
					self::$access_token = $access_token;
					return true;
				}

				// JSON attempt
				$response = json_decode($result);
				if ($response && is_object($response) && isset($response->access_token) && !empty($response->access_token))
				{
					self::$access_token = $response->access_token;
					return true;
				}
			}
			return false;
		}

	}

	public static function legacyAuthorize()
	{

		$application = JFactory::getApplication();
		$session = JFactory::getSession();

		$socialConnectOAuthUserToken = $session->get('socialConnect'.self::$service.'OAuthUserToken');
		$socialConnectOAuthUserSecret = $session->get('socialConnect'.self::$service.'OAuthUserSecret');

		// Get url variables
		$oauthToken = JRequest::getString('oauth_token');
		$oauthVerifier = JRequest::getString('oauth_verifier');
		$oauthTokenSecret = JRequest::getString('oauth_token_secret');

		// Trim tokens
		$oauthToken = JString::trim($oauthToken);
		$oauthVerifier = JString::trim($oauthVerifier);
		$oauthTokenSecret = JString::trim($oauthTokenSecret);

		// Load the library
		require_once JPATH_SITE.'/components/com_socialconnect/lib/tmhOAuth.php';

		// Step 1
		if (!$oauthToken)
		{
			$tmhOAuth = new tmhOAuth( array(
				'consumer_key' => self::$consumer_key,
				'consumer_secret' => self::$consumer_secret
			));

			$tmhOAuth->request('POST', self::$endpoint.'request_token', array('oauth_callback' => self::$oauth_callback));
			if ($tmhOAuth->response['code'] == 200)
			{
				$data = $tmhOAuth->extract_params($tmhOAuth->response['response']);
				if ($data['oauth_callback_confirmed'] == true)
				{
					$session->set('socialConnect'.self::$service.'OAuthUserToken', $data['oauth_token']);
					$session->set('socialConnect'.self::$service.'OAuthUserSecret', $data['oauth_token_secret']);
					$application->redirect(self::$endpoint.'authenticate?oauth_token='.$data['oauth_token']);
				}
			}
			else
			{
				if ($tmhOAuth->response['code'] == 0)
				{
					self::$error = $tmhOAuth->response['error'];
				}
				else
				{
					self::$error = $tmhOAuth->response['code'].': '.$tmhOAuth->response['response'];
				}
				return false;
			}
		}
		// Step 2
		else if ($oauthVerifier)
		{
			if ($session->get('socialConnect'.self::$service.'OAuthUserToken') == $oauthToken)
			{
				$tmhOAuth = new tmhOAuth( array(
					'consumer_key' => self::$consumer_key,
					'consumer_secret' => self::$consumer_secret,
					'user_token' => $socialConnectOAuthUserToken,
					'user_secret' => $socialConnectOAuthUserSecret
				));
				$tmhOAuth->request('POST', self::$endpoint.'access_token', array('oauth_verifier' => $oauthVerifier));
				if ($tmhOAuth->response['code'] == 200)
				{
					$data = $tmhOAuth->extract_params($tmhOAuth->response['response']);
					self::$oauth_token = $data['oauth_token'];
					self::$oauth_token_secret = $data['oauth_token_secret'];
					// Twitter custom implementation
					if (isset($data['user_id']))
					{
						self::$user_id = $data['user_id'];
						self::$screen_name = $data['screen_name'];
					}
					return true;
				}
				else
				{
					if ($tmhOAuth->response['code'] == 0)
					{
						self::$error = $tmhOAuth->response['error'];
					}
					else
					{
						self::$error = $tmhOAuth->response['code'].': '.$tmhOAuth->response['response'];
					}
					return false;
				}
			}
		}
	}

}
