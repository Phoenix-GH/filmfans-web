<?php
/**
 * @version		$Id: default.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die; ?>

<div id="modSocialConnectTwitterDefault" class="modSocialConnectTwitter<?php echo $moduleClassSuffix; ?>">

	<?php if(count($tweets)): ?>
		<ul>
		<?php foreach($tweets as $key => $tweet): ?>
			<li class="<?php echo ($key%2) ? "modSocialConnectOdd" : "modSocialConnectEven"; ?>">
				<img src="<?php echo $tweet->userAvatar; ?>" class="modSocialConnectTwitterUserAvatar" alt="<?php echo $tweet->userName; ?>" />
				<span class="modSocialConnectTwitterStatus"><?php echo $tweet->text; ?></span>
				<a class="modSocialConnectTwitterStatusTime" href="<?php echo $tweet->link; ?>" target="_blank"><?php echo $tweet->time; ?></a>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if($params->get('screenName')): ?>
	<div class="modSocialConnectTwitterFollowUs">
		<a target="_blank" href="http://twitter.com/<?php echo $params->get('screenName'); ?>">
			<span><?php echo JText::_('JW_SC_TW_FOLLOW_US_ON_TWITTER'); ?></span>
		</a>
	</div>
	<?php endif; ?>
</div>