<?php
/**
 * @version		$Id: view.html.php 3421 2013-07-24 16:40:45Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class SocialConnectViewSettings extends SocialConnectView
{

	public function display($tpl = null)
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
			$submitFunction = 'Joomla.submitbutton';
		}
		else
		{
			$submitFunction = 'submitbutton';
		}
		JHTML::_('behavior.tooltip');
		$model = $this->getModel();
		$model->setState('option', 'com_socialconnect');
		$form = $model->getForm();
		$this->assignRef('form', $form);
		$id = $model->getExtensionID();
		$this->assignRef('id', $id);
		$this->assignRef('submitFunction', $submitFunction);
		$application = JFactory::getApplication();
		$messages = $application->getMessageQueue();
		$this->assignRef('messages', $messages);
		parent::display($tpl);
	}

}
