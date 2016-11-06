<?php
/**
 * @version		$Id: default.php 2438 2013-01-29 16:45:14Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die;
?>
<div class="mod_socialconnect <?php echo $cmsVersionClass; ?>">
	
	<a id="socialConnectCountersLoggedInUsersButton">
		<span class="socialConnectCountersLoggedInUsersButtonCounter"><?php echo $loggedIn; ?></span>
		<span class="socialConnectCountersLoggedInUsersButtonToggler <?php echo $togglerClass; ?>"></span>
		<span class="socialConnectClear"></span>
	</a>
	<div id="socialConnectCounters" class="<?php echo $containerClass; ?>">
		<div class="socialConnectCountersLoggedInUsers">
			<div class="socialConnectCountersLoggedInUsersContainer">
				<span class="socialConnectCountersLoggedInUsersCounter"><?php echo $loggedIn; ?></span>
				<span class="socialConnectCountersLoggedInUsersDetails"><?php echo JText::_('JW_SC_BACKEND_MOD_FRONTEND_USERS_LOGGED_IN'); ?></span>
			</div>
		</div>
		
		<?php foreach($services as $key => $service): ?>
			
		<?php if($key == 0 || ($key)%3 == 0): ?>
		<div class="socialConnectServicesCounterRow">
		<?php endif; ?>
		
		<div class="socialConnectServiceCounter">
			<span class="socialConnect<?php echo $service->name; ?>Counter" title="<?php echo $service->title; ?>"><?php echo $service->value; ?></span>
		</div>
		
		<?php if(($key+1)%3 == 0 || ($key+1) == count($services)): ?>
			<div class="socialConnectClear"></div>
		</div>
		<?php endif; ?>
		
		<?php endforeach; ?>
		
		<div class="socialConnectCountersTotalUsers">
			<span class="socialConnectCountersTotalUsersCounter"><?php echo $total; ?></span>
			<span class="socialConnectCountersTotalUsersDetails"><?php echo JText::_('JW_SC_BACKEND_MOD_BROWSING_YOUR_SITE'); ?></span>
		</div>
	</div>
</div>