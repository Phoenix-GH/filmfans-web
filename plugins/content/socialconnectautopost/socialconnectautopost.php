<?php
/**
 * @version		$Id: socialconnectautopost.php 3404 2013-07-19 15:05:40Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgContentSocialConnectAutoPost extends JPlugin
{
	public function plgContentSocialConnectAutoPost(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onContentAfterSave($context, $row, $isNew)
	{
		if ($context == 'com_k2.item' || $context == 'com_content.article')
		{
			$this->execute($context, $row, $isNew);
		}
	}

	public function onAfterContentSave($row, $isNew)
	{
		$context = JRequest::getCmd('option').'.'.JRequest::getCmd('view').'.'.JRequest::getCmd('task');
		if ($context == 'com_k2.item.save' || $context == 'com_k2.item.apply' || $context == 'com_content..apply' || $context == 'com_content..save')
		{
			$this->execute($context, $row, $isNew);
		}
	}

	private function execute($context, $row, $isNew)
	{
		set_time_limit(0);
		$services = JRequest::getVar('socialconnectautopost');
		if (is_array($services) && count($services))
		{
			// Prepare data depending on context
			if (JString::strpos($context, 'com_k2') === 0)
			{
				$title = $row->title;
				require_once JPATH_SITE.'/components/com_k2/helpers/route.php';
				$url = K2HelperRoute::getItemRoute($row->id.':'.$row->alias, $row->catid);
			}
			else if (JString::strpos($context, 'com_content') === 0)
			{
				$title = $row->title;
				require_once JPATH_SITE.'/components/com_content/helpers/route.php';
				$db = JFactory::getDBO();
				$db->setQuery('SELECT alias FROM #__categories WHERE id = '.(int)$row->catid);
				$categoryAlias = $db->loadResult();
				$url = ContentHelperRoute::getArticleRoute($row->id.':'.$row->alias, $row->catid.':'.$categoryAlias);
			}

			if (isset($url))
			{
				$url = $this->buildURL($url);
				$suffix = JRequest::getString('socialConnectAutoPostSuffix');

				foreach ($services as $service)
				{
					if ($service == 'facebook')
					{
						$this->postToFacebook($title, $url, $suffix);
					}
					else if ($service == 'twitter')
					{
						$this->postToTwitter($title, $url, $suffix);
					}
				}
			}
		}
	}

	private function buildURL($url)
	{
		$site = JApplication::getInstance('site');
		$router = $site->getRouter();
		$uri = $router->build($url);
		$url = $uri->toString(array(
			'path',
			'query',
			'fragment'
		));
		$url = preg_replace('/\s/u', '%20', $url);
		$url = JString::str_ireplace(JURI::base(true).'/', JURI::root(false), $url);
		return $url;
	}

	private function postToFacebook($title, $url, $suffix)
	{
		$params = JComponentHelper::getParams('com_socialconnect');
		if (version_compare(JVERSION, '2.5', 'ge'))
		{
			$data = $params->get('facebookAutoPostData');
			if (is_object($data) && isset($data->token))
			{
				$params->set('facebookAutoPostDataToken', $data->token);
				$params->set('facebookAutoPostDataAccount', $data->account);
			}
		}
		$token = $params->get('facebookAutoPostDataToken');
		$account = $params->get('facebookAutoPostDataAccount');
		if ($token && $account)
		{
			require_once JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php';
			$parameters = array(
				'message' => $title.' '.$suffix,
				'link' => $url,
				'access_token' => $token
			);
			$result = SocialConnectHelper::request('https://graph.facebook.com/'.$account.'/feed', $parameters, 'post');
			if ($result)
			{
				$response = json_decode($result);
				if (is_object($response) && isset($response->error))
				{
					$application = JFactory::getApplication();
					$application->enqueueMessage(JText::sprintf('JW_SC_AUTOPOST_ERROR', 'Facebook', $response->error->message), 'notice');
				}
			}
		}
	}

	private function postToTwitter($title, $url, $suffix)
	{
		$params = JComponentHelper::getParams('com_socialconnect');
		if (version_compare(JVERSION, '2.5', 'ge'))
		{
			$data = $params->get('twitterAutoPostData');
			if (is_object($data) && isset($data->token))
			{
				$params->set('twitterAutoPostDataToken', $data->token);
				$params->set('twitterAutoPostDataSecret', $data->secret);
			}
		}
		$token = $params->get('twitterAutoPostDataToken');
		$secret = $params->get('twitterAutoPostDataSecret');
		$consumer_key = $params->get('twitterConsumerKey');
		$consumer_secret = $params->get('twitterConsumerSecret');
		if ($token && $secret && $consumer_key && $consumer_secret)
		{
			require_once JPATH_SITE.'/components/com_socialconnect/lib/tmhOAuth.php';
			$tmhOAuth = new tmhOAuth( array(
				'consumer_key' => $consumer_key,
				'consumer_secret' => $consumer_secret,
				'user_token' => $token,
				'user_secret' => $secret
			));
			$tmhOAuth->request('POST', 'https://api.twitter.com/1.1/statuses/update.json', array('status' => $title.' '.$url.' '.$suffix));
			if ($tmhOAuth->response['code'] != 200)
			{
				$application = JFactory::getApplication();
				$response = json_decode($tmhOAuth->response['response']);
				if (is_object($response) && isset($response->error))
				{
					$application->enqueueMessage(JText::sprintf('JW_SC_AUTOPOST_ERROR', 'Twitter', $response->error), 'notice');
				}
			}
		}
	}

}
