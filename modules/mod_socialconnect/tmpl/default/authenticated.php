<?php
/**
 * @version		$Id: authenticated.php 2673 2013-04-03 13:11:30Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die; ?>

<div id="modSocialConnectDefault" class="modSocialConnectAuth<?php echo $moduleClassSuffix; ?>">
	<div class="socialConnectUserBlock socialConnectBlock socialConnectClearFix">
		<div class="socialConnectClearFix">
			<?php if($user->socialConnectData->image):?>
			<img class="socialConnectAvatar" src="<?php echo $user->socialConnectData->image; ?>" alt="<?php echo $user->name; ?>" />
			<?php endif; ?>
			<div class="socialConnectUserInfo">
				<span class="socialConnectGreeting"><?php echo JText::_('JW_SC_WELCOME'); ?></span>
				<span class="socialConnectUsername"><?php echo $user->name; ?></span>
				<a class="socialConnectAccountLink" href="<?php echo $accountLink; ?>"><?php echo JText::_('JW_SC_MY_ACCOUNT'); ?></a>
			</div>
		</div>
		
		<?php if (count($K2Menu) || count($menu)): ?>
		<ul class="socialConnectUserMenu">
			
				<?php if (count($K2Menu)): ?>
				<li>
					<a class="socialConnectUserLink" href="<?php echo $K2Menu['user']; ?>"><?php echo JText::_('JW_SC_MY_PAGE'); ?></a>
				</li>
				<?php if(isset($K2Menu['add'])): ?>
				<li>
					<a class="modal socialConnectAddLink" rel="{handler:'iframe',size:{x:990,y:550}}" href="<?php echo $K2Menu['add']; ?>"><?php echo JText::_('JW_SC_ADD_NEW_ITEM'); ?></a>
				</li>
				<?php endif; ?>
				<li>
					<a class="socialConnectCommentsLink modal" rel="{handler:'iframe',size:{x:990,y:550}}" href="<?php echo $K2Menu['comments']; ?>"><?php echo JText::_('JW_SC_MY_COMMENTS'); ?></a>
				</li>
				<?php endif; ?>
			
				<?php if(count($menu)): ?>
				<?php $level = 1; foreach($menu as $key => $link): $level++; ?>
					<li class="<?php echo $link->class; ?>">
					<?php if($link->type == 'url' && $link->browserNav == 0): ?>
						<a href="<?php echo $link->href; ?>"><?php echo $link->title; ?></a>
					<?php elseif(strpos($link->link,'option=com_k2&view=item&layout=itemform') || $link->browserNav == 2): ?>
						<a class="modal" rel="{handler:'iframe',size:{x:990,y:550}}" href="<?php echo $link->href; ?>"><?php echo $link->title; ?></a>
					<?php else: ?>
						<a href="<?php echo $link->href; ?>"<?php if($link->browserNav == 1) echo ' target="_blank"'; ?>><?php echo $link->title; ?></a>
					<?php endif; ?>
				
				<?php if(isset($menu[$key+1]) && $menu[$key]->level < $menu[$key+1]->level): ?>
				<ul>
				<?php endif; ?>
				
				<?php if(isset($menu[$key+1]) && $menu[$key]->level > $menu[$key+1]->level): ?>
					<?php echo str_repeat('</li></ul>', $menu[$key]->level - $menu[$key+1]->level); ?>
				<?php endif; ?>
				
				<?php if(isset($menu[$key+1]) && $menu[$key]->level == $menu[$key+1]->level): ?>
					</li>
				<?php endif; ?>
				
				<?php endforeach; ?>
				
				<?php endif; ?>
		</ul>
		<?php endif; ?>
		<a class="socialConnectButton socialConnectSignOutButton">
			<i></i>
			<span><?php echo JText::_('JW_SC_SIGN_OUT'); ?></span>
		</a>
		<form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="socialConnectSignOutForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="<?php echo $task; ?>" />
			<input type="hidden" name="return" value="<?php echo $returnURL; ?>" />
			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>
</div>