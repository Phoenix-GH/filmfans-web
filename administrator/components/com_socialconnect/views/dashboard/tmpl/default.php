<?php
/**
 * @version		$Id: default.php 3438 2013-07-29 16:05:27Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;
?>

<div id="socialConnect">

	<div id="jwHeader">
		<div id="jwLogo">
   			<i>SocialConnect</i>
   		</div>
   		<span id="jwVersion">v1.5.1</span>
   		<ul id="jwToolbar">
   			<?php if($this->userCanEditSettings):?>
   			<li><a href="index.php?option=com_socialconnect&view=settings"><i class="jw-icon jw-icon-cog"></i><?php echo JText::_('JW_SC_COM_SETTINGS'); ?></a></li>
   			<?php endif; ?>
   			<li><a href="http://www.joomlaworks.net/extensions/commercial-premium/socialconnect#documentation" target="_blank"><i class="jw-icon jw-icon-help-circled"></i><?php echo JText::_('JW_SC_COM_HELP'); ?></a></li>
   			<li class="scClear"></li>
   		</ul>
   		<div class="scClear"></div>
	</div>

	<div id="jwSubheader">
		<h3 class="jwPageTitle"><?php echo JText::_('JW_SC_COM_DASHBOARD'); ?></h3>
		<?php if(count($this->messages)): ?>
			<ul id="jwMessages">
				<?php foreach($this->messages as $message): ?>
				<li class="jwMessage jwMessage-<?php echo $message['type']; ?>">
					<i class="jw-icon-<?php echo $message['type']; ?>"></i>
					<?php echo $message['message']; ?>
				</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	
	<div id="jwContent">
		<div id="socialConnectDashboard">
			
			<div id="socialConnectDashboardMain">
				<h3 class="socialConnectDashboardTitle"><?php echo JText::_('JW_SC_COM_SYSTEM_STATUS'); ?></h3>
				<table class="socialConnectDashboardTable">
					<thead>
						<tr>
							<td></td>
							<td class="socialConnectDashboardHeader"><span class="jw-icon-key"><?php echo JText::_('JW_SC_COM_AUTHENTICATION'); ?></span></td>
							<td class="socialConnectDashboardHeader"><span class="jw-icon-chat"><?php echo JText::_('JW_SC_COM_COMMENTING'); ?></span></td>
							<td class="socialConnectDashboardHeader"><span class="jw-icon-export"><?php echo JText::_('JW_SC_COM_SHARING'); ?></span></td>
						</tr>
					</thead>
					<tbody>
						<?php foreach($this->checks['services'] as $service): ?>
						<tr>
							<td class="socialConnectDashboardService socialConnectDashboardService<?php echo $service->suffix; ?>"><?php echo $service->name; ?></td>
							<td class="socialConnectDashboardStatus">
								<?php if($service->auth === null): ?>
								<span class="scNotAvailable jw-icon-minus"></span>
								<?php elseif($service->auth): ?>
								<span class="scPassed"><?php echo JText::_('JW_SC_COM_ENABLED'); ?></span>
								<?php else: ?>
								<span class="scFailed"><?php echo JText::_('JW_SC_COM_DISABLED'); ?></span>
								<?php endif; ?>
							</td>
							<td class="socialConnectDashboardStatus">
								<?php if($service->comments === null): ?>
								<span class="scNotAvailable jw-icon-minus"></span>
								<?php elseif($service->comments): ?>
								<span class="scPassed"><?php echo JText::_('JW_SC_COM_ENABLED'); ?></span>
								<?php else: ?>
								<span class="scFailed"><?php echo JText::_('JW_SC_COM_DISABLED'); ?></span>
								<?php endif; ?>
							</td>
							<td class="socialConnectDashboardStatus">
								<?php if($service->sharing === null): ?>
								<span class="scNotAvailable jw-icon-minus"></span>
								<?php elseif($service->sharing): ?>
								<span class="scPassed"><?php echo JText::_('JW_SC_COM_ENABLED'); ?></span>
								<?php else: ?>
								<span class="scFailed"><?php echo JText::_('JW_SC_COM_DISABLED'); ?></span>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div id="socialConnectDashboardSidebar">
				<h2><?php echo JText::_('JW_SC_COM_ABOUT'); ?></h2>
				<div class="socialConnectDashboardDescription"><?php echo JText::_('SOCIALCONNECT_ABOUT'); ?></div>
				<h2><?php echo JText::_('JW_SC_COM_SYSTEM_INFORMATION'); ?></h2>
				<table class="socialConnectDashboardTable">
					<tbody>
						<tr>
							<td class="socialConnectDashboardSystemInfoLabel">PHP</td>
							<td class="socialConnectDashboardSystemInfoValue"><?php echo $this->checks['php']; ?></td>
						</tr>
						<tr>
							<td class="socialConnectDashboardSystemInfoLabel">PHP cURL</td>
							<td class="socialConnectDashboardSystemInfoValue socialConnectDashboardStatus"><span class="<?php echo $this->checks['curl'] ? 'scPassed' : 'scFailed'; ?>"><?php echo $this->checks['curl'] ? JText::_('JW_SC_COM_ENABLED') : JText::_('JW_SC_COM_DISABLED'); ?></span></td>
						</tr>
						<tr>
							<td class="socialConnectDashboardSystemInfoLabel">PHP JSON</td>
							<td class="socialConnectDashboardSystemInfoValue socialConnectDashboardStatus"><span class="<?php echo $this->checks['json'] ? 'scPassed' : 'scFailed'; ?>"><?php echo $this->checks['json'] ? JText::_('JW_SC_COM_ENABLED') : JText::_('JW_SC_COM_DISABLED'); ?></span></td>
						</tr>
						<tr>
							<td class="socialConnectDashboardSystemInfoLabel">PHP HMAC</td>
							<td class="socialConnectDashboardSystemInfoValue socialConnectDashboardStatus"><span class="<?php echo $this->checks['hash_hmac'] ? 'scPassed' : 'scFailed'; ?>"><?php echo $this->checks['hash_hmac'] ? JText::_('JW_SC_COM_ENABLED') : JText::_('JW_SC_COM_DISABLED'); ?></span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="scClear"></div>
			<div id="socialConnectAdminFooter">
				<a target="_blank" href="http://www.joomlaworks.net/socialconnect">SocialConnect v1.5.1</a> | Copyright &copy; 2006-<?php echo date('Y'); ?> <a href="http://www.joomlaworks.net/" target="_blank">JoomlaWorks Ltd.</a>
			</div>
		</div>
	</div>
</div>