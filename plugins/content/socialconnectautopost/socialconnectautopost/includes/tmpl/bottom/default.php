<?php
/**
 * @version		$Id: default.php 3402 2013-07-19 12:36:14Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die;
?>
<div id="socialConnectAutoPost">
	
	<input placeholder="<?php echo JText::_('JW_SC_APPEND_TEXT_TO_THE_POST'); ?>" type="text" id="socialConnectAutoPostSuffix" name="socialConnectAutoPostSuffix" />
		
	<?php if($facebook): ?>
	<input id="socialConnectAutoPostFB" type="checkbox" name="socialconnectautopost[]" value="facebook" />
	<label for="socialConnectAutoPostFB" class="socialConnectAutoPostFacebook"><span>Facebook</span></label>
	<?php endif; ?>
		
	<?php if($twitter): ?>
	<input id="socialConnectAutoPostTW" type="checkbox" name="socialconnectautopost[]" value="twitter" />
	<label for="socialConnectAutoPostTW" class="socialConnectAutoPostTwitter"><span>Twitter</span></label>
	<?php endif; ?>
		
	<a id="socialConnectAutoPostToggler" title="SocialConnect"><span>SocialConnect</span></a>
	<a id="socialConnectAutoPostCloseButton"></a>
	
</div>