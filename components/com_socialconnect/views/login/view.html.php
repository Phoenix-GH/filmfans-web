<?php
/**
 * @version     $Id: view.html.php 3438 2013-07-29 16:05:27Z lefteris.kavadas $
 * @package     SocialConnect
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */
// no direct access
defined('_JEXEC') or die ;

class SocialConnectViewLogin extends SocialConnectView
{

	function display($tpl = null)
	{
		$this->addViewVariables();
		$this->setPageTitle();
		$this->loadHelper('SocialConnect');
		SocialConnectHelper::loadHeadData($this->params, 'component');
		SocialConnectHelper::setUserData($this->user);
		$variables = SocialConnectHelper::setVariables($this->params);
		foreach ($variables as $key => $value)
		{
			$this->assign($key, $value);
		}
		$layout = ($this->user->guest) ? 'default' : 'authenticated';
		$this->setLayout($layout);
		$this->addTemplatePaths();
		parent::display();
	}

	function twitter()
	{
		$this->addViewVariables();
		$this->addStyles();
		$this->setLayout('twitter');
		$this->addTemplatePaths();
		parent::display();
	}

	function ning()
	{
		version_compare(JVERSION, '3.0', 'ge') ? JHTML::_('behavior.framework') : JHTML::_('behavior.mootools');
		$this->addViewVariables();
		$document = JFactory::getDocument();
		$this->addStyles();
		$this->loadHelper('SocialConnect');
		$variables = SocialConnectHelper::setVariables($this->params);
		foreach ($variables as $key => $value)
		{
			$this->assign($key, $value);
		}
		$this->setLayout('ning');
		$this->addTemplatePaths();
		parent::display();
	}

	private function getParams()
	{
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$application = JFactory::getApplication();
			$params = $application->getParams('com_socialconnect');
		}
		else
		{
			$params = JComponentHelper::getParams('com_socialconnect');
		}
		return $params;
	}

	private function addTemplatePaths()
	{
		// Get application
		$application = JFactory::getApplication();

		// Get params
		$params = $this->getParams();

		// Look for template files in component folders
		$this->addTemplatePath(JPATH_COMPONENT.'/templates');
		$this->addTemplatePath(JPATH_COMPONENT.'/templates/default');

		// Look for overrides in template folder (Component template structure)
		$this->addTemplatePath(JPATH_SITE.'/templates/'.$application->getTemplate().'/html/com_socialconnect/templates');
		$this->addTemplatePath(JPATH_SITE.'/templates/'.$application->getTemplate().'/html/com_socialconnect/templates/default');

		// Look for overrides in template folder (Joomla! template structure)
		$this->addTemplatePath(JPATH_SITE.'/templates/'.$application->getTemplate().'/html/com_socialconnect/default');
		$this->addTemplatePath(JPATH_SITE.'/templates/'.$application->getTemplate().'/html/com_socialconnect');

		// Look for specific Component theme files
		if ($params->get('template', 'default'))
		{
			$this->addTemplatePath(JPATH_COMPONENT.'/templates/'.$params->get('template', 'default'));
			$this->addTemplatePath(JPATH_SITE.'/templates/'.$application->getTemplate().'/html/com_socialconnect/templates/'.$params->get('template', 'default'));
			$this->addTemplatePath(JPATH_SITE.'/templates/'.$application->getTemplate().'/html/com_socialconnect/'.$params->get('template', 'default'));
		}
	}

	private function setPageTitle()
	{
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$application = JFactory::getApplication();
			$params = $this->getParams();
			$title = $params->get('page_title');
			if ($application->getCfg('sitename_pagetitles', 0) == 1)
			{
				$title = JText::sprintf('JPAGETITLE', $application->getCfg('sitename'), $params->get('page_title'));
			}
			elseif ($application->getCfg('sitename_pagetitles', 0) == 2)
			{
				$title = JText::sprintf('JPAGETITLE', $params->get('page_title'), $application->getCfg('sitename'));
			}
			$document = JFactory::getDocument();
			$document->setTitle($title);
		}
	}

	private function addStyles()
	{
		$document = JFactory::getDocument();
		$params = $this->getParams();
		$application = JFactory::getApplication();
		if (JFile::exists(JPATH_SITE.'/templates/'.$application->getTemplate().'/html/com_socialconnect/'.$params->get('template', 'default').'/css/style.css'))
		{
			$document->addStylesheet(JURI::root(true).'/templates/'.$application->getTemplate().'/html/com_socialconnect/'.$params->get('template', 'default').'/css/style.css?v=1.5.1');
		}
		else
		{
			$document->addStylesheet(JURI::root(true).'/components/com_socialconnect/templates/'.$params->get('template', 'default').'/css/style.css?v=1.5.1');
		}

	}

	private function addViewVariables()
	{
		$user = JFactory::getUser();
		$this->assignRef('user', $user);
		$params = $this->getParams();
		$this->assignRef('params', $params);
	}

}
