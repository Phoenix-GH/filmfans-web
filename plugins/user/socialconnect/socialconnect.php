<?php
/**
 * @version		$Id: socialconnect.php 2961 2013-05-01 15:34:10Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgUserSocialConnect extends JPlugin
{

	public function plgUserSocialConnect(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	public function onUserLogin($user, $options = array())
	{
		$this->onLoginUser($user, $options);
	}

	public function onUserLogout($user, $options = array())
	{
		$this->onLogoutUser($user, $options);
	}

	public function onLoginUser($user, $options = array())
	{
		// Get user id
		$userID = JUserHelper::getUserId($user['username']);

		// Get application
		$application = JFactory::getApplication();

		// Get session data
		$session = JFactory::getSession();
		$data = $session->get('socialConnectData');

		if ($data)
		{
			// Check for spammers using http://www.stopforumspam.com
			$params = JComponentHelper::getParams('com_socialconnect');
			if ($params->get('stopForumSpamIntegration') && $application->isSite())
			{
				$ip = $_SERVER['REMOTE_ADDR'];
				$email = urlencode($user['email']);
				$username = urlencode($user['username']);
				JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
				$url = 'http://www.stopforumspam.com/api?ip='.$ip.'&email='.$email.'&username='.$username.'&f=json';
				$options = array(CURLOPT_HEADER => 0, CURLOPT_RETURNTRANSFER => 1, CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_FOLLOWLOCATION => 1);
				$response = SocialConnectHelper::request($url, array(), 'get', $options);
				$response = json_decode($response);
				if (is_object($response) && $response->ip->appears || $response->email->appears || $response->username->appears)
				{
					$db = JFactory::getDBO();
					$db->setQuery("UPDATE #__users SET block = 1 WHERE id = ".$userID);
					$db->query();
					$application->logout();
					$application->enqueueMessage(JText::_('JW_SC_YOUR_ACCOUNT_HAS_BEEN_SUSPENDED'), 'error');
					$application->redirect(JURI::root(true));
					return false;
				}
			}

			// Update SocialConnect sessions table
			JTable::addIncludePath(JPATH_SITE.'/components/com_socialconnect/tables');
			$socialConnectSession = JTable::getInstance('Session', 'SocialConnect');
			$socialConnectSession->session_id = $session->getId();
			$socialConnectSession->type = $data->type;
			$socialConnectSession->store();

			// If user has logged in from a Ning network then migrate him to Joomla! Also update the password everytime he logs in through Ning.
			if ($userID && $data->type == 'ning')
			{
				jimport('joomla.user.helper');
				$salt = JUserHelper::genRandomPassword(32);
				$crypt = JUserHelper::getCryptedPassword($user['password'], $salt);
				$password = $crypt.':'.$salt;
				$instance = new JUser();
				$instance->load($userID);
				$instance->set('password', $password);
				$instance->save();
			}
			// K2 user integration
			if (JPluginHelper::isEnabled('user', 'k2'))
			{
				jimport('joomla.filesystem.file');
				JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2/tables');
				$row = JTable::getInstance('K2User', 'Table');
				if (!$userID)
				{
					return;
				}
				$db = JFactory::getDBO();
				$query = "SELECT id FROM #__k2_users WHERE userID = ".(int)$userID;
				$db->setQuery($query);
				$K2UserID = $db->loadResult();
				if ($K2UserID)
				{
					$row->load($K2UserID);
				}
				$row->set('userID', $userID);
				$row->set('userName', $user['username']);
				if (isset($data->link))
				{
					$row->set('url', $data->link);
				}
				if (isset($data->image))
				{
					JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
					$url = $data->image;
					$options = array(CURLOPT_BINARYTRANSFER => 1, CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_FOLLOWLOCATION => 1, );
					$response = SocialConnectHelper::request($url, array(), 'get', $options, true);
					$buffer = $response->result;
					$effectiveURL = $response->information['effective_url'];
					if ($buffer)
					{
						$filename = JFile::getName($effectiveURL);
						$extension = JFile::getExt($filename);
						$validExtensions = array('jpg', 'gif', 'png');
						if (!in_array($extension, $validExtensions))
						{
							$extension = 'jpg';
						}
						$filename = $userID.'.'.$extension;
						JFile::write(JPATH_SITE.'/media/k2/users/'.$filename, $buffer);
						$row->set('image', $filename);
					}
				}
				if (isset($data->description))
				{
					$row->set('description', $data->description);
				}
				if ($K2UserID)
				{
					$row->set('group', NULL);
				}
				else
				{
					$params = JComponentHelper::getParams('com_k2');
					$K2UserGroup = $params->get('K2UserGroup');
					if (!$K2UserGroup)
					{
						return;
					}
					$row->set('group', $K2UserGroup);
				}
				$row->store();
			}
		}
		return true;
	}

	public function onLogoutUser($user, $options = array())
	{
		$application = JFactory::getApplication();
		if ($application->isAdmin())
		{
			return false;
		}
		$session = JFactory::getSession();
		$id = $session->getId();
		if ($id)
		{
			$db = JFactory::getDBO();
			$db->setQuery("DELETE FROM #__socialconnect_sessions WHERE session_id = ".$db->quote($id));
			$db->query();
		}
		return true;
	}

}
