<?php
/**
 * @version		$Id: default.php 3426 2013-07-25 13:00:31Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die; ?>

<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?> pagelogin">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>

<div id="comSocialConnectContainer">

	<?php if($this->params->get('introductionMessage')):?>
	<div class="socialConnectIntroMessage"><?php echo $this->introductionMessage; ?></div>
	<?php endif; ?>

	<div class="row login-form">

		<div class="login col-lg-6 col-md-12">

			<form action="<?php echo JURI::base(); ?>index.php" method="post" class="ff-form" role="form" data-toggle="validator">

				<div class="form-group">
					<label class="control-label" for="email1i"><?php echo JText::_('PLG_K2_FILMFANS_LOGIN_EMAIL'); ?></label>
					<input type="text" class="form-control" id="email1i" name="username" required />
				</div>
				<div class="form-group">
					<label class="control-label" for="password1i"><?php echo JText::_('PLG_K2_FILMFANS_LOGIN_PASSWORD'); ?></label>
					<input type="password" class="form-control" id="password1i" name="password" required />
				</div>

				<button type="submit" class="btn btn-submit btn-lg btn-block"><?php echo JText::_('PLG_K2_FILMFANS_LOGIN_GO') ?></button>

				<div class="form-group login-remember">

					<div class="help-block">
						<a class="forgot-password" href="<?php echo $this->resetPasswordLink; ?>"><?php echo JText::_('PLG_K2_FILMFANS_LOGIN_FORGOT'); ?></a>
					</div>

					<div class="checkbox-group">
						<div class="checkbox checkbox-inline">
							<input type="checkbox" name="remember" id="remember1cb" value="yes" />
							<label for="remember1cb"><?php echo JText::_('PLG_K2_FILMFANS_LOGIN_REMEMBER') ?></label>
						</div>
					</div>
				</div>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
				<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get('ret', $this->returnURL); ?>" />
				<?php echo JHTML::_('form.token'); ?>
			</form>

		</div>

		<?php if($this->services): ?>
		<div class="social col-lg-6 col-md-12 col-xs-height">
			<div class="socialConnectServicesBlock">
				<div class="socialConnectClearFix">
					<?php if($this->facebook): ?>
					<a class="socialConnectFacebookButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $this->facebookLink; ?>">
						<i></i>
						<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_SIGN_WITH', 'Facebook'); ?></span>
					</a>
					<?php endif; ?>
					<?php if($this->twitter): ?>
					<a class="socialConnectTwitterButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $this->twitterLink; ?>">
						<i></i>
						<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_SIGN_WITH', 'Twitter'); ?></span>
					</a>
					<?php endif; ?>
					<?php if($this->google): ?>
					<a class="socialConnectGoogleButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $this->googleLink; ?>">
						<i></i>
						<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_SIGN_WITH', 'Google'); ?></span>
					</a>
					<?php endif; ?>
					<?php if($this->googlePlus): ?>
					<a class="socialConnectGooglePlusButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $this->googlePlusLink; ?>">
						<i></i>
						<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_SIGN_WITH', 'Google+'); ?></span>
					</a>
					<?php endif; ?>
					<?php if($this->linkedin): ?>
					<a class="socialConnectLinkedInButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $this->linkedinLink; ?>">
						<i></i>
						<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_SIGN_WITH', 'LinkedIn'); ?></span>
					</a>
					<?php endif; ?>
					<?php if($this->github): ?>
					<a class="socialConnectGitHubButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $this->githubLink; ?>">
						<i></i>
						<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_SIGN_WITH', 'GitHub'); ?></span>
					</a>
					<?php endif; ?>
					<?php if($this->wordpress): ?>
					<a class="socialConnectWordPressButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $this->wordpressLink; ?>">
						<i></i>
						<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_SIGN_WITH', 'WordPress'); ?></span>
					</a>
					<?php endif; ?>
					<?php if($this->windows): ?>
					<a class="socialConnectWindowsLiveButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $this->windowsLink; ?>">
						<i></i>
						<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_SIGN_WITH', 'Microsoft'); ?></span>
					</a>
					<?php endif; ?>
					<?php if($this->ning): ?>
					<a class="socialConnectNingButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="<?php echo $this->ningLink; ?>">
						<i></i>
						<span><?php echo $this->params->get('ningNetworkName'); ?></span>
					</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

	</div>

</div>

<?php if ($this->params->get('allowUserRegistration')) : ?>
<div class="row">

	<div class="well ff-form col-lg-6 col-md-12">

		<div class="componentheading pagesignup">
			<?php echo JText::_('PLG_K2_FILMFANS_LOGIN_SIGN_UP_TITLE'); ?>
			<small><?php echo JText::_('PLG_K2_FILMFANS_LOGIN_SIGN_UP_HINT'); ?></small>
		</div>

		<a class="btn btn-default btn-submit btn-lg btn-block" href="<?php echo $this->registrationLink; ?>">
			<i></i>
			<span><?php echo JText::_('PLG_K2_FILMFANS_LOGIN_SIGN_UP_GO'); ?></span>
		</a>

	</div>
</div>
<?php endif; ?>
