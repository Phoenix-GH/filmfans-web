<?php
/**
 * @version		$Id: socialconnect.php 3404 2013-07-19 15:05:40Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgSystemSocialConnect extends JPlugin
{

	public function plgSystemSocialConnect(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	public function onAfterRoute()
	{
		$application = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		if ($application->isSite() && $view == 'login' && ($option == 'com_users' || $option == 'com_user') && $_SERVER['REQUEST_METHOD'] == 'GET')
		{
			$component = JComponentHelper::getComponent('com_socialconnect');
			$menu = $application->getMenu();
			$items = version_compare(JVERSION, '2.5', 'ge') ? $menu->getItems('component_id', $component->id) : $menu->getItems('componentid', $component->id);
			if (count($items))
			{
				$router = JSite::getRouter();
				$link = $router->getMode() == JROUTER_MODE_SEF ? 'index.php?Itemid='.$items[0]->id : $items[0]->link.'&Itemid='.$items[0]->id;
			}
			else
			{
				$link = 'index.php?option=com_socialconnect&view=login';
			}
			$redirect = JRoute::_($link, false);
			$application->redirect($redirect);
		}
	}

	public function onBeforeCompileHead()
	{
		$application = JFactory::getApplication();
		$document = JFactory::getDocument();
		if ($document->getType() == 'html')
		{
			if ($application->isSite())
			{
				if (defined('SOCIALCONNECT_HEAD_DATA'))
				{
					$title = (version_compare(JVERSION, '15', 'lte')) ? $document->getTitle() : html_entity_decode($document->getTitle());
					$document->setMetaData('og:title', $title);
					$document->setMetaData('twitter:title', JString::substr($title, 0, 70));

					$description = (version_compare(JVERSION, '15', 'lte')) ? $document->getDescription() : html_entity_decode($document->getDescription());
					$document->setMetaData('og:description', JString::substr($description, 0, 300));
					$document->setMetaData('twitter:description', JString::substr($description, 0, 200));
				}
			}
			else
			{
				if ($this->isAutoPostSupported())
				{
					$params = JComponentHelper::getParams('com_socialconnect');
					$layout = $params->get('autoPostLayout', 'default');
					$css = (version_compare(JVERSION, '2.5', 'lt')) ? '/plugins/content/socialconnectautopost/includes/tmpl/'.$layout.'/css/style.css' : '/plugins/content/socialconnectautopost/socialconnectautopost/includes/tmpl/'.$layout.'/css/style.css';
					$document->addStyleSheet(JURI::root(true).$css);
					if (version_compare(JVERSION, '3.0', 'ge'))
					{
						JHtml::_('jquery.framework');
					}
					else if (version_compare(JVERSION, '2.5', 'ge'))
					{
						JHtml::_('behavior.framework');
					}
					else
					{
						JHtml::_('behavior.mootools');
					}
					$script = (version_compare(JVERSION, '2.5', 'lt')) ? '/plugins/content/socialconnectautopost/includes/js/script.js' : '/plugins/content/socialconnectautopost/socialconnectautopost/includes/js/script.js';
					$document->addScript(JURI::root(true).$script);
					$language = JFactory::getLanguage();
					$language->load('plg_content_socialconnectautopost');
				}
			}
		}

	}

	public function onAfterRender()
	{
		$application = JFactory::getApplication();
		if ($application->isSite())
		{
			$this->fixMetaTags();
			$this->addDisqusScript();
		}
		else
		{
			$this->addAutoPost();
		}
	}

	private function fixMetaTags()
	{
		$response = JResponse::getBody();
		$searches = array(
			'<meta name="og:url"',
			'<meta name="og:title"',
			'<meta name="og:type"',
			'<meta name="og:image"',
			'<meta name="og:description"',
			'<meta name="fb:app_id"'
		);
		$replacements = array(
			'<meta property="og:url"',
			'<meta property="og:title"',
			'<meta property="og:type"',
			'<meta property="og:image"',
			'<meta property="og:description"',
			'<meta property="fb:app_id"'
		);
		if (JString::strpos($response, 'prefix="og: http://ogp.me/ns#"') === false)
		{
			$searches[] = '<html ';
			$searches[] = '<html>';
			$replacements[] = '<html prefix="og: http://ogp.me/ns#" ';
			$replacements[] = '<html prefix="og: http://ogp.me/ns#">';
		}
		$response = JString::str_ireplace($searches, $replacements, $response);
		JResponse::setBody($response);
	}

	private function addDisqusScript()
	{
		$document = JFactory::getDocument();
		if (defined('SOCIALCONNECT_DISQUS_COUNTERS') && $document->getType() == 'html')
		{
			$search = '</body>';
			$replace = "<script type=\"text/javascript\">
			/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
			var disqus_shortname = '".SOCIALCONNECT_DISQUS_SHORTNAME."';
			/* * * DON'T EDIT BELOW THIS LINE * * */
			(function () {
			var s = document.createElement('script'); s.async = true;
			s.type = 'text/javascript';
			s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
			(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
			}());
			</script></body>";
			$response = JResponse::getBody();
			$response = JString::str_ireplace($search, $replace, $response);
			JResponse::setBody($response);
		}

	}

	private function addAutoPost()
	{
		$document = JFactory::getDocument();
		if ($this->isAutoPostSupported() && $document->getType() == 'html')
		{

			$params = JComponentHelper::getParams('com_socialconnect');
			$layout = $params->get('autoPostLayout', 'default');

			if (version_compare(JVERSION, '2.5', 'lt'))
			{
				$file = JPATH_SITE.'/plugins/content/socialconnectautopost/includes/tmpl/'.$layout.'/default.php';
			}
			else
			{
				$file = JPATH_SITE.'/plugins/content/socialconnectautopost/socialconnectautopost/includes/tmpl/'.$layout.'/default.php';
			}

			if (version_compare(JVERSION, '2.5', 'ge'))
			{
				$data = $params->get('facebookAutoPostData');
				if (is_object($data) && isset($data->token))
				{
					$params->set('facebookAutoPostDataToken', $data->token);
					$params->set('facebookAutoPostDataSecret', $data->secret);
				}

				$data = $params->get('twitterAutoPostData');
				if (is_object($data) && isset($data->token))
				{
					$params->set('twitterAutoPostDataToken', $data->token);
					$params->set('twitterAutoPostDataSecret', $data->secret);
				}

			}

			$facebook = $params->get('facebookAutoPost') && $params->get('facebookAutoPostDataToken');
			$twitter = $params->get('twitterAutoPost') && $params->get('twitterAutoPostDataToken') && $params->get('twitterConsumerKey') && $params->get('twitterConsumerSecret');

			jimport('joomla.filesystem.file');
			ob_start();
			include $file;
			$buffer = ob_get_contents();
			ob_end_clean();

			$search = '</body>';
			$replace = $buffer;

			$response = JResponse::getBody();
			$response = JString::str_ireplace($search, $replace, $response);
			JResponse::setBody($response);
		}
	}

	private function isAutoPostSupported()
	{
		$params = JComponentHelper::getParams('com_socialconnect');
		$context = JRequest::getCmd('option').'.'.JRequest::getCmd('view').'.'.JRequest::getCmd('task');
		$extensions = array(
			'com_k2.item.',
			'com_content.article.',
			'com_content..edit',
			'com_content..add'
		);
		if (version_compare(JVERSION, '2.5', 'ge'))
		{
			$data = $params->get('facebookAutoPostData');
			if (is_object($data) && isset($data->token))
			{
				$params->set('facebookAutoPostDataToken', $data->token);
				$params->set('facebookAutoPostDataSecret', $data->secret);
			}

			$data = $params->get('twitterAutoPostData');
			if (is_object($data) && isset($data->token))
			{
				$params->set('twitterAutoPostDataToken', $data->token);
				$params->set('twitterAutoPostDataSecret', $data->secret);
			}
		}
		$facebook = $params->get('facebookAutoPost') && $params->get('facebookAutoPostDataToken');
		$twitter = $params->get('twitterAutoPost') && $params->get('twitterAutoPostDataToken') && $params->get('twitterConsumerKey') && $params->get('twitterConsumerSecret');
		return in_array($context, $extensions) && ($facebook || $twitter);
	}

}
