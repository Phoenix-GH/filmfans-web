<?php
/**
 * @version     $Id: socialconnectemail.php 2437 2013-01-29 14:14:53Z lefteris.kavadas $
 * @package     SocialConnect
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgAuthenticationSocialConnectEmail extends JPlugin
{

	function plgAuthenticationSocialConnectEmail(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onUserAuthenticate($credentials, $options, &$response)
	{
		$this->onAuthenticate($credentials, $options, $response);
	}

	function onAuthenticate($credentials, $options, &$response)
	{

		// Front-end only
		$application = JFactory::getApplication();
		if ($application->isAdmin())
		{
			return false;
		}

		// Init login status and user data
		$response->status = version_compare(JVERSION, '3.0', 'ge') ? JAuthentication::STATUS_FAILURE : JAUTHENTICATE_STATUS_FAILURE;

		jimport('joomla.user.helper');
		if (empty($credentials['password']))
		{
			$response->status = version_compare(JVERSION, '3.0', 'ge') ? JAuthentication::STATUS_FAILURE : JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = version_compare(JVERSION, '1.6.0', 'ge') ? JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED') : 'Empty password not allowed';
			return false;
		}

		$db = JFactory::getDBO();
		$query = "SELECT id, password FROM #__users WHERE email=".$db->Quote($credentials['username']);
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($result)
		{

			$match = false;

			if (version_compare(JVERSION, '3.2', 'ge'))
			{
				if (substr($result->password, 0, 4) == '$2y$')
				{
					// BCrypt passwords are always 60 characters, but it is possible that salt is appended although non standard.
					$password60 = substr($result->password, 0, 60);

					if (JCrypt::hasStrongPasswordSupport())
					{
						$match = password_verify($credentials['password'], $password60);
					}
				}
				elseif (substr($result->password, 0, 8) == '{SHA256}')
				{
					// Check the password
					$parts = explode(':', $result->password);
					$crypt = $parts[0];
					$salt = @$parts[1];
					$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt, 'sha256', false);

					if ($result->password == $testcrypt)
					{
						$match = true;
					}
				}
				else
				{
					// Check the password
					$parts = explode(':', $result->password);
					$crypt = $parts[0];
					$salt = @$parts[1];

					$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt, 'md5-hex', false);

					if ($crypt == $testcrypt)
					{
						$match = true;
					}
				}
			}
			else
			{
				$parts = explode(':', $result->password);
				$crypt = $parts[0];
				$salt = @$parts[1];
				$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);

				if ($crypt == $testcrypt)
				{
					$match = true;
				}
			}
			if ($match)
			{
				$response->status = version_compare(JVERSION, '3.0', 'ge') ? JAuthentication::STATUS_SUCCESS : JAUTHENTICATE_STATUS_SUCCESS;
				$response->type = 'SocialConnect - Email';
				$response->error_message = '';
				$user = JUser::getInstance($result->id);
				$response->email = $user->email;
				$response->username = $user->username;
				$response->fullname = $user->name;
			}
		}
	}

}
