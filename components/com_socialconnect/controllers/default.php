<?php
/**
 * @version		$Id: default.php 3208 2013-06-06 13:01:14Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class SocialConnectControllerDefault extends SocialConnectController
{

	public function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'login');
		$user = JFactory::getUser();
		if ($user->guest)
		{
			$cache = true;
		}
		else
		{
			$cache = false;
		}
		parent::display($cache);
	}

	public function signOut()
	{
		$application = JFactory::getApplication();
		$user = JFactory::getUser();
		if (!$user->guest)
		{
			SocialConnectHelper::signOut();
		}
		else
		{
			$application->enqueueMessage(JText::_('JW_SC_COM_YOU_ARE_NOT_LOGGED_IN'), 'notice');
			$application->redirect(JURI::root());
		}
	}

	public function verify()
	{
		$application = JFactory::getApplication();
		$user = JFactory::getUser();
		if ($user->guest)
		{
			SocialConnectHelper::verify();
		}
		else
		{
			$application->enqueueMessage(JText::_('JW_SC_COM_YOU_ARE_ALREADY_LOGGED_IN'), 'notice');
			$application->redirect(JURI::root());
		}

	}

	public function updateEmail()
	{
		$user = JFactory::getUser();
		if ($user->guest)
		{
			return $this;
		}
		$application = JFactory::getApplication();
		$email = JRequest::getVar('email');
		jimport('joomla.mail.helper');
		if (JMailHelper::isEmailAddress($email))
		{
			$db = JFactory::getDBO();
			$query = "SELECT id FROM #__users WHERE email=".$db->Quote($email);
			$db->setQuery($query);
			$id = $db->loadResult();
			if ($id)
			{
				$application->enqueueMessage(JText::_('JW_SC_COM_THE_EMAIL_ADDRESS_YOU_PROVIDED_IS_ALREADY_USED'), 'error');
				$application->redirect(JRoute::_('index.php?option=com_socialconnect&view=login&task=twitter'));
			}
			$query = "UPDATE #__users SET email=".$db->Quote($email)." WHERE id=".(int)$user->id;
			$db->setQuery($query);
			$db->query();

			$session = JFactory::getSession();
			$returnURL = $session->get('socialConnectReturn');
			if (!$returnURL || !JURI::isInternal($returnURL))
			{
				$application->redirect(JURI::root());
			}
			else
			{
				$application->redirect($returnURL);
			}

		}
		else
		{
			$application->enqueueMessage(JText::_('JW_SC_COM_THE_EMAIL_ADDRESS_YOU_PROVIDED_IS_INVALID'), 'notice');
			$application->redirect(JRoute::_('index.php?option=com_socialconnect&view=login&task=twitter'));
		}
	}

}
