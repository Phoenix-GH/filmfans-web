<?php
/**
 * @version		$Id: default.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die;
?>
<div id="socialConnectAutoPost">
	
	<div id="socialConnectAutoPostInner">
	
		<a id="socialConnectAutoPostToggler" title="SocialConnect"><span>SocialConnect</span></a>
		
		<div id="socialConnectAutoPostContainer">
			<div id="socialConnectAutoPostHeader">
				<h3><?php echo JText::_('JW_SC_POST_ITEM_ON_SOCIAL_MEDIA'); ?></h3>
				<a id="socialConnectAutoPostCloseButton"></a>
				<div class="socialConnectAutoPostClear"></div>
			</div>
			<div id="socialConnectAutoPostFields">
				
				<?php if($facebook): ?>
				<div class="socialConnectAutoPostService">
					<label for="socialConnectAutoPostFB" class="socialConnectAutoPostFacebook"><span>Facebook</span></label>
					<input id="socialConnectAutoPostFB" type="checkbox" name="socialconnectautopost[]" value="facebook" />
				</div>
				<?php endif; ?>
	
	
				<?php if($twitter): ?>
				<div class="socialConnectAutoPostService">
					<label for="socialConnectAutoPostTW" class="socialConnectAutoPostTwitter"><span>Twitter</span></label>
					<input id="socialConnectAutoPostTW" type="checkbox" name="socialconnectautopost[]" value="twitter" />
				</div>
				<?php endif; ?>
	
			</div>
			
			<div id="socialConnectAutoPostText">
				<label for="socialConnectAutoPostSuffix"><?php echo JText::_('JW_SC_APPEND_TEXT_TO_THE_POST'); ?></label>
				<textarea id="socialConnectAutoPostSuffix" name="socialConnectAutoPostSuffix"></textarea>
			</div>
			
			<div id="socialConnectAutoPostFooter">
				<span><?php echo JText::_('JW_SC_POWERED_BY'); ?> <a target="_blank" href="http://www.joomlaworks.net/socialconnect">SocialConnect</a></span>
			</div>
			
		</div>
		
		<div class="socialConnectAutoPostClear"></div>
		
	</div>
</div>