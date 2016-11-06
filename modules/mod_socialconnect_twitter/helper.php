<?php
/**
 * @version		$Id: helper.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class modSocialConnectTwitterHelper
{

	public static function getLatestTweets($params)
	{

		// Initialize
		$tweets = array();

		// Get Component params
		$componentParams = JComponentHelper::getParams('com_socialconnect');
		$consumerKey = $componentParams->get('twitterConsumerKey');
		$consumerSecret = $componentParams->get('twitterConsumerSecret');

		// Go further only if consumer key and consumer secret exist
		if ($consumerKey && $consumerSecret && $params->get('screenName'))
		{
			// Application authentication
			$consumerKey = urlencode($consumerKey);
			$consumerSecret = urlencode($consumerSecret);
			$bearerToken = base64_encode($consumerKey.':'.$consumerSecret);
			$options = array(CURLOPT_HTTPHEADER => array(
					'Authorization: Basic '.$bearerToken,
					'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
				));
			$parameters = array(
				'grant_type' => 'client_credentials',
				'consumer_key' => $consumerKey,
				'consumer_secret' => $consumerSecret
			);
			$result = SocialConnectHelper::request('https://api.twitter.com//oauth2/token', $parameters, 'POST', $options);
			if ($result)
			{
				$data = json_decode($result);
				$accessToken = $data->access_token;
				$options = array(CURLOPT_HTTPHEADER => array('Authorization: Bearer '.$accessToken));
				$parameters = array(
					'access_token' => $accessToken,
					'screen_name' => $params->get('screenName'),
					'count' => $params->get('count')
				);
				$result = SocialConnectHelper::request('https://api.twitter.com/1.1/statuses/user_timeline.json', $parameters, 'GET', $options);
				if ($result)
				{
					$tweets = json_decode($result);
				}
				foreach ($tweets as $key => $tweet)
				{
					$tweet->userAvatar = $tweet->user->profile_image_url;
					$tweet->userName = $tweet->user->name;
					$tweet->text = preg_replace("#((https?|s?ftp|ssh)\:\/\/[^\"\s\<\>]*[^.,;'\">\:\s\<\>\)\]\!])#s", '<a target="_blank" href="$0">$0</a>', $tweet->text);
					$tweet->text = preg_replace("#\B[@@]([a-zA-Z0-9_]{1,20})#s", '<a target="_blank" href="http://twitter.com/$1">$0</a>', $tweet->text);
					$tweet->text = preg_replace("~(^|\s+)#(\w+)~s", '<a target="_blank" href="http://twitter.com/search?q=%23$2">$0</a>', $tweet->text);
					$tweet->link = 'http://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str;
					$tweet->time = $tweet->created_at;
				}
			}
		}
		else
		{
			$application = JFactory::getApplication();
			$application->enqueueMessage(JText::_('JW_SC_TW_SETUP_ERROR'), 'notice');
		}
		return $tweets;
	}

}
