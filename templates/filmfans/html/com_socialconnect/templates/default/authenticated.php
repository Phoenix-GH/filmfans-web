<?php
/**
 * @version		$Id: authenticated.php 2437 2013-01-29 14:14:53Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die;

?>

<div class="componentheading">&nbsp;</div>

<div class="row login-form login-authicated">
	<div class="login col-sm-12 col-md-8 col-lg-6">
		<div class="">
			<?php if($this->user->socialConnectData->image):?>
			<a class="avatar"><img  src="<?php echo $this->user->socialConnectData->image; ?>" alt="" /></a>
			<?php endif; ?>
			<div class="componentheading">
				<small style="display: block;"><?php echo JText::_('PLG_K2_FILMFANS_USER_WELCOME'); ?></small>
				<?php echo $this->user->name; ?>
			</div>
			<div class="clearfix"></div>
		</div>
		<form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="ff-form" role="form">
			<button type="submit" class="btn btn-submit btn-lg btn-block"><?php echo JText::_('PLG_K2_FILMFANS_USER_LOGOUT'); ?></button>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="return" value="<?php echo $this->returnURL; ?>" />
			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>

	<div class="social col-sm-12 col-md-4 col-lg-6">
		<ul class="nav">
			<li><a class="miaccount" href="<?php echo $this->accountLink; ?>"><?php echo JText::_('PLG_K2_FILMFANS_USER_ACCOUNT'); ?></a></li>
			<li><a class="miprofile" href="<?php echo FilmFansHelper::routeMenuItem('miProfile'); ?>"><?php echo JText::_('PLG_K2_FILMFANS_USER_PROFILE'); ?></a></li>
		</ul>
	</div>
</div>