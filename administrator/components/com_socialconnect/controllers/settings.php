<?php
/**
 * @version		$Id: settings.php 3378 2013-07-17 12:03:51Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class SocialConnectControllerSettings extends SocialConnectController
{

	public function apply()
	{
		$response = $this->saveSettings();
		$this->setRedirect('index.php?option=com_socialconnect&view=settings', $response->message, $response->type);
	}

	public function save()
	{
		$response = $this->saveSettings();
		$this->setRedirect('index.php?option=com_socialconnect', $response->message, $response->type);
	}

	protected function saveSettings()
	{
		if (version_compare(JVERSION, '2.5.0', 'ge'))
		{
			$this->checkPermissions();
			JRequest::checkToken() or jexit('Invalid Token');
			$data = JRequest::getVar('jform', array(), 'post', 'array');
			$id = JRequest::getInt('id');
			$option = JRequest::getCmd('component');

			// Validate the form
			JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/'.$option);
			$form = JForm::getInstance('com_socialconnect.settings', 'config', array(
				'control' => 'jform',
				'load_data' => true
			), false, '/config');

			// Use Joomla! model for saving settings
			if (version_compare(JVERSION, '3.2', 'ge'))
			{
				require_once JPATH_SITE.'/components/com_config/model/cms.php';
				require_once JPATH_SITE.'/components/com_config/model/form.php';
			}

			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_config/models');
			$model = JModelLegacy::getInstance('Component', 'ConfigModel');
			$params = $model->validate($form, $data);
			if ($params === false)
			{
				$errors = $model->getErrors();
				$response = new stdClass;
				$response->message = $errors[0] instanceof Exception ? $errors[0]->getMessage() : $errors[0];
				$response->type = 'warning';
				return $response;
			}

			$data = array(
				'params' => $params,
				'id' => $id,
				'option' => $option
			);
		}
		else
		{
			JRequest::checkToken() or jexit('Invalid Token');
			$data = JRequest::get('post');
		}

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			SocialConnectModel::addIncludePath(JPATH_COMPONENT.'/models');
			$model = SocialConnectModel::getInstance('Settings', 'SocialConnectModel');
		}
		else
		{
			JModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_socialconnect/models', 'SocialConnectModel');
			$model = JModel::getInstance('Settings', 'SocialConnectModel');
		}
		$model->setState('option', 'com_socialconnect');
		$model->setState('data', $data);
		$response = new stdClass;
		if ($model->save())
		{
			$response->message = JText::_('JW_SC_SETTINGS_SAVED');
			$response->type = 'message';
		}
		else
		{
			$response->message = $model->getError();
			$response->type = 'error';
		}
		return $response;
	}

	public function cancel()
	{
		$application = JFactory::getApplication();
		$application->redirect('index.php?option=com_socialconnect');
	}

	protected function checkPermissions()
	{
		if (version_compare(JVERSION, '2.5.0', 'ge'))
		{
			if (!JFactory::getUser()->authorise('core.admin', 'com_socialconnect'))
			{
				$application = JFactory::getApplication();
				$application->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'));
				$application->redirect('index.php?option=com_socialconnect');
				return;
			}
		}
	}

}
