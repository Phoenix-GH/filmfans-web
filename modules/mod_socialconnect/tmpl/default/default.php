<?php
/**
 * @version		$Id: default.php 3426 2013-07-25 13:00:31Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die; ?>

<div id="modSocialConnectDefault" class="modSocialConnect<?php echo $moduleClassSuffix; ?>">

	<?php if($params->get('introductionMessage')):?>
	<div class="socialConnectIntroductionMessage"><?php echo $introductionMessage; ?></div>
	<?php endif; ?>

	<div class="socialConnectSignInBlock">
		<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post">
			<input class="socialConnectInput" placeholder="<?php echo $usernameLabel; ?>" type="text" name="username" />
			<a class="socialConnectLink" href="<?php echo $remindUsernameLink; ?>"><?php echo JText::_('JW_SC_FORGOT_YOUR_USERNAME'); ?></a>
			<input class="socialConnectInput modSocialConnectPassword" placeholder="<?php echo JText::_('JW_SC_PASSWORD') ?>" type="password" name="<?php echo $passwordFieldName; ?>" />
			<a class="socialConnectLink" href="<?php echo $resetPasswordLink; ?>"><?php echo JText::_('JW_SC_FORGOT_YOUR_PASSWORD'); ?></a>
			<div class="socialConnectClearFix">
				<button class="socialConnectButton socialConnectSignInButton socialConnectClearFix" type="submit">
					<i></i>
					<span><?php echo JText::_('JW_SC_SIGN_IN') ?></span>
				</button>
				<div class="socialConnectRememberBlock">
					<input id="modSocialConnectDefaultRemember" type="checkbox" name="remember" value="yes" />
					<label class="socialConnectLabel" for="modSocialConnectDefaultRemember"><?php echo JText::_('JW_SC_REMEMBER_ME') ?></label>
				</div>		
			</div>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="<?php echo $task; ?>" />
			<input type="hidden" name="return" value="<?php echo $returnURL; ?>" />
	  		<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>
	
	<?php if($services): ?>
	<div class="socialConnectServicesBlock">
		<h4 class="socialConnectServicesMessage"><?php echo JText::_('JW_SC_OR_SIGN_IN_WITH'); ?></h4>
		<div class="socialConnectClearFix">
			<?php if($facebook): ?>
			<a class="socialConnectFacebookButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $facebookLink; ?>">
				<i></i>
				<span>Facebook</span>
			</a>
			<?php endif; ?>
			<?php if($twitter): ?>
			<a class="socialConnectTwitterButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $twitterLink; ?>">
				<i></i>
				<span>Twitter</span>
			</a>
			<?php endif; ?>
			<?php if($google): ?>
			<a class="socialConnectGoogleButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $googleLink; ?>">
				<i></i>
				<span>Google</span>
			</a>
			<?php endif; ?>
			<?php if($googlePlus): ?>
			<a class="socialConnectGooglePlusButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $googlePlusLink; ?>">
				<i></i>
				<span>Google+</span>
			</a>
			<?php endif; ?>
			<?php if($linkedin): ?>
			<a class="socialConnectLinkedInButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $linkedinLink; ?>">
				<i></i>
				<span>LinkedIn</span>
			</a>
			<?php endif; ?>
			<?php if($github): ?>
			<a class="socialConnectGitHubButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $githubLink; ?>">
				<i></i>
				<span>GitHub</span>
			</a>
			<?php endif; ?>
			<?php if($wordpress): ?>
			<a class="socialConnectWordPressButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $wordpressLink; ?>">
				<i></i>
				<span>WordPress</span>
			</a>
			<?php endif; ?>
			<?php if($windows): ?>
			<a class="socialConnectWindowsLiveButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $windowsLink; ?>">
				<i></i>
				<span>Microsoft</span>
			</a>
			<?php endif; ?>
			<?php if($ning): ?>
			<a class="socialConnectNingButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $ningLink; ?>">
				<i></i>
				<span><?php echo $params->get('ningNetworkName'); ?></span>
			</a>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	
	<span><?php echo JText::_('JW_SC_NOT_A_MEMBER_YET_SIGN_UP')?></span>
	<a class="socialConnectRegistrationButton" href="<?php echo $registrationLink; ?>"><?php echo JText::_('JW_SC_REGISTER'); ?></a>

	<?php if($params->get('footerMessage')):?>
	<div class="socialConnectFooterMessage"><?php echo $footerMessage; ?></div>
	<?php endif; ?>	

</div>