<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * Akeeba Backup Configuration view class
 *
 */
class AkeebaViewConfig extends F0FViewHtml
{
	public function onAdd($tpl = null)
	{
		$media_folder = JUri::base().'../media/com_akeeba/';

		// Get a JSON representation of GUI data
		$json = AkeebaHelperEscape::escapeJS(Factory::getEngineParamsProvider()->getJsonGuiDefinition(),'"\\');
		$this->json = $json;

		// Get profile ID
		$profileid = Platform::getInstance()->get_active_profile();
		$this->profileid = $profileid;

		// Get profile name
		$profileName = F0FModel::getTmpInstance('Profiles','AkeebaModel')
			->setId($profileid)
			->getItem()
			->description;
		$this->profilename = $this->escape($profileName);

		// Get the root URI for media files
		$this->mediadir = AkeebaHelperEscape::escapeJS($media_folder.'theme/');

		// Are the settings secured?
		if( Platform::getInstance()->get_platform_configuration_option('useencryption', -1) == 0 ) {
			$this->securesettings = -1;
		} elseif( !Factory::getSecureSettings()->supportsEncryption() ) {
			$this->securesettings = 0;
		} else {
			JLoader::import('joomla.filesystem.file');
			$filename = JPATH_COMPONENT_ADMINISTRATOR.'/engine/serverkey.php';
			if(JFile::exists($filename)) {
				$this->securesettings = 1;
			} else {
				$this->securesettings = 0;
			}
		}

		// Add live help
		AkeebaHelperIncludes::addHelp('config');
	}
}