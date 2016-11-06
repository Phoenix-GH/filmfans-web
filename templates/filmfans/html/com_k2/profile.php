<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();

JHtml::_('stylesheet', JUri::base(false).'components/com_socialconnect/templates/default/css/style.css?v=1.5.1');
JHtml::_('script', 'system/html5fallback.js', false, true);
JHtml::_('script', 'media/filmfans/js/validator.min.js');
JHtml::_('script', 'media/filmfans/js/jquery.date-plugin.js');
JHtml::_('formbehavior.chosen', '#country1s', null, array('placeholder_text_single' => '...'));

$origdata = array();
$origdata['jform']['email1'] = $this->user->get('email');
$origdata['gender'] = $this->K2User->gender;
$origdata['plugins'] = is_array($this->K2User->plugins) ? $this->K2User->plugins : json_decode($this->K2User->plugins, true);

if (empty($origdata['plugins']['ffuser']['lastname']) && empty($origdata['plugins']['ffuser']['firstname'])) {
	$_tmp = array_filter(explode(' ', $this->user->get('name')));
	$origdata['plugins']['ffuser']['lastname'] = array_pop($_tmp);
	$origdata['plugins']['ffuser']['firstname'] = count($_tmp) ? implode(' ', $_tmp) : $origdata['plugins']['ffuser']['lastname'];
}

$data = array_replace_recursive($origdata, (array) $mainframe->getUserState('plg_ffuser.profile.data', array()));

$mainframe->setUserState('plg_ffuser.profile.data', null);

$mainframe->setUserState('com_users.edit.profile.redirect', JUri::current());

$avatar = FilmFansHelper::getAvatar($this->K2User->image, $this->user);
$cover = FilmFansHelper::getCover(FilmFansHelper::getVal('plugins/ffcover', $origdata));

//FilmFansHelper::_sd($avatar);

?>

<script type="text/javascript">
/*<![CDATA[*/
    jQuery(function($) {
		$('#birthdate1i').formatDate('yyyy/mm/dd');

		$(window).on('resize', function() {
			$('#country1s_chzn').css('width', '');
		});
		$(window).trigger('resize');
	});
/*]]>*/
</script>

<div class="container container-white-not">

	<div class="profile-avatar">
		<div class="user-title">
			<a class="avatar-upload avatar" href="javascript:void(0)" onclick="jQuery('#avatar-upload').slideToggle();" title="<?php echo JText::_('PLG_K2_FILMFANS_USER_AVATAR_HINT'); ?>" data-toggle="tooltip" data-placement="bottom">
				<b><img src="<?php echo $avatar[0]; ?>" alt="" /></b>
				<i></i>
			</a>
			<div class="componentheading">
				<?php echo $this->user->name; ?>
				<small><?php echo FilmFansHelper::getDD('country', FilmFansHelper::getVal('plugins/ffuser/country', $data)); ?></small>
			</div>
			<div class="clearfix"></div>
		</div>

		<div id="avatar-upload" style="display: none;">

			<div class="well">

				<a href="javascript:void(0)" onclick="jQuery('#avatar-upload').slideUp();" style="float: right;"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a>

				<h3><?php echo JText::_('PLG_K2_FILMFANS_USER_AVATAR_TITLE'); ?></h3>

				<form action="<?php echo JUri::current(); ?>" method="post" class="ff-form" role="form" enctype="multipart/form-data">

					<div class="form-group">
						<label class="control-label" for="image1f"><?php echo JText::_('PLG_K2_FILMFANS_USER_AVATAR_UPLOAD'); ?></label>
						<input type="file" class="form-control" id="image1f" name="avatar" />
					</div>

					<div class="form-group">
						<div class="checkbox-group">
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="avatardel" id="avatardel1cb" value="1" />
								<label for="avatardel1cb"><?php echo JText::_('PLG_K2_FILMFANS_USER_AVATAR_DELETE'); ?></label>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="form-group">
						<label class="control-label" for="image2i"><?php echo JText::_('PLG_K2_FILMFANS_USER_AVATAR_URL'); ?></label>
						<input type="text" class="form-control" id="image2i" name="avatarurl" value="<?php echo $avatar[1]; ?>" />
					</div>

					<button type="submit" class="btn btn-submit btn-lg btn-block"><?php echo JText::_('PLG_K2_FILMFANS_USER_AVATAR_GO'); ?></button>

					<input type="hidden" name="uid" value="<?php echo $this->user->get('id'); ?>" />
					<input type="hidden" name="K2UserForm" value="1" />
					<input type="hidden" name="ffaction" value="profile.avatar" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
			</div>
		</div>

		<div id="cover-upload" style="display: none;">

			<div class="well">

				<a href="javascript:void(0)" onclick="jQuery('#cover-upload').slideUp();" style="float: right;"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a>

				<h3><?php echo JText::_('PLG_K2_FILMFANS_USER_COVER_TITLE'); ?></h3>

				<form action="<?php echo JUri::current(); ?>" method="post" class="ff-form" role="form" enctype="multipart/form-data">

					<div class="form-group">
						<label class="control-label" for="image1f"><?php echo JText::_('PLG_K2_FILMFANS_USER_AVATAR_UPLOAD'); ?></label>
						<input type="file" class="form-control" id="image1f" name="cover" />
					</div>

					<div class="form-group">
						<div class="checkbox-group">
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="coverdel" id="coverdel1cb" value="1" />
								<label for="coverdel1cb"><?php echo JText::_('PLG_K2_FILMFANS_USER_AVATAR_DELETE'); ?></label>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="form-group">
						<label class="control-label" for="image2i"><?php echo JText::_('PLG_K2_FILMFANS_USER_AVATAR_URL'); ?></label>
						<input type="text" class="form-control" id="image2i" name="coverurl" value="<?php echo $cover[1]; ?>" />
					</div>

					<button type="submit" class="btn btn-submit btn-lg btn-block"><?php echo JText::_('PLG_K2_FILMFANS_USER_COVER_GO'); ?></button>

					<input type="hidden" name="uid" value="<?php echo $this->user->get('id'); ?>" />
					<input type="hidden" name="K2UserForm" value="1" />
					<input type="hidden" name="ffaction" value="profile.cover" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
			</div>
		</div>

		<a class="cover-upload" href="javascript:void(0)" onclick="jQuery('#cover-upload').slideToggle();"><span><?php echo JText::_('PLG_K2_FILMFANS_USER_COVER_HINT'); ?></span><i></i></a>

	</div>
</div>

<div class="profile-form-title componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?> pageaccount">
	<?php echo $this->escape($this->params->get('page_title')); ?>
	<small><?php echo JText::_('PLG_K2_FILMFANS_USER_REQUIRED'); ?></small>
</div>

<?php if (isset($this->message)) $this->display('message'); ?>

<div class="settings-switcher">
	<script type="text/javascript">
	/*<![CDATA[*/
	    function ffswitchSettings(el) {
			el = jQuery(el);
			var form = jQuery('#ffprofileform');
			var cont = el.closest('.ffprofile');
			cont.toggleClass('opened');
			form.slideToggle(cont.hasClass('opened'));
		}
	/*]]>*/
	</script>
	<a href="javascript:void(0)" onclick="ffswitchSettings(this)" >
		<?php echo JText::_('PLG_K2_FILMFANS_USER_SETTINGS'); ?>
		<span class="closed glyphicon glyphicon-chevron-down"></span>
		<span class="opened glyphicon glyphicon-chevron-up"></span>
	</a>
</div>

<form action="<?php echo JUri::current(); ?>" method="post" id="ffprofileform" class="profile-form ff-form fixheight" role="form" data-toggle="validator">

	<div class="container container-white-not">

		<div class="row">

			<div class="profile-form-wrap col-lg-9 col-md-9 col-md-push-3 col-lg-push-3">
				<div class="row">

					<div class="form-group col-lg-4 col-sm-6">
						<label class="control-label" for="firstname1i"><?php echo JText::_('PLG_K2_FILMFANS_USER_FIRST_NAME'); ?></label>
						<input type="text" class="form-control" id="firstname1i" name="plugins[ffuser][firstname]" required value="<?php echo FilmFansHelper::getVal('plugins/ffuser/firstname', $data); ?>" />
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group col-lg-4 col-sm-6">
						<label class="control-label" for="lastname1i"><?php echo JText::_('PLG_K2_FILMFANS_USER_LAST_NAME'); ?></label>
						<input type="text" class="form-control" id="lastname1i" name="plugins[ffuser][lastname]" required value="<?php echo FilmFansHelper::getVal('plugins/ffuser/lastname', $data); ?>" />
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group col-lg-4 col-sm-6">
						<?php $gender = FilmFansHelper::getVal('gender', $data); ?>
						<label class="control-label"><?php echo JText::_('PLG_K2_FILMFANS_USER_GENDER'); ?></label>
						<div class="radio-group">
							<div class="radio radio-inline">
								<input type="radio" name="gender" id="gender1cb" value="m" required <?php if ($gender == 'm') echo 'checked'; ?> />
								<label for="gender1cb"><?php echo JText::_('PLG_K2_FILMFANS_USER_GENDER_M'); ?></label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" name="gender" id="gender2cb" value="f" required <?php if ($gender == 'f') echo 'checked'; ?> />
								<label for="gender2cb"><?php echo JText::_('PLG_K2_FILMFANS_USER_GENDER_F'); ?></label>
							</div>
						</div>
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group col-lg-4 col-sm-6">
						<label class="control-label" for="birthdate1i"><?php echo JText::_('PLG_K2_FILMFANS_USER_BIRTH_DATE'); ?></label>
						<input type="text" class="form-control" id="birthdate1i" name="plugins[ffuser][birthdate]" placeholder="yyyy / mm / dd" pattern="(19|20)[0-9]{2}\s*\/\s*[0-9]{2}\s*\/\s*[0-9]{1,2}" required value="<?php echo str_replace('/', ' / ', str_replace(' ', '', FilmFansHelper::getVal('plugins/ffuser/birthdate', $data))); ?>" />
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group col-lg-4 col-sm-6">
						<label class="control-label" for="country1s"><?php echo JText::_('PLG_K2_FILMFANS_USER_COUNTRY'); ?></label>
						<?php $country = FilmFansHelper::getVal('plugins/ffuser/country', $data); ?>
						<select class="form-control" id="country1s" name="plugins[ffuser][country]" required>
							<option></option>
							<?php foreach (FilmFansHelper::getDDList('country') as $k=>$v) { ?>
							<option value="<?php echo $k; ?>"<?php if ($country == $k) echo ' selected="selected"'; ?>><?php echo $v; ?></option>
							<?php } ?>
						</select>
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group col-lg-4 col-sm-6">
						<label class="control-label" for="zip1i"><?php echo JText::_('PLG_K2_FILMFANS_USER_ZIP'); ?></label>
						<input type="text" class="form-control" id="zip1i" name="plugins[ffuser][zip]" required value="<?php echo FilmFansHelper::getVal('plugins/ffuser/zip', $data); ?>" />
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group col-lg-4 col-sm-6">
						<label class="control-label" for="email1i"><?php echo JText::_('PLG_K2_FILMFANS_USER_EMAIL'); ?></label>
						<input type="email" class="form-control" id="email1i" name="jform[email1]" required value="<?php echo FilmFansHelper::getVal('jform/email1', $data); ?>" />
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group col-lg-4 col-sm-6">
						<label class="control-label" for="password1i"><?php echo JText::_('PLG_K2_FILMFANS_USER_PASSWORD_NEW'); ?></label>
						<input type="password" class="form-control" id="password1i" name="jform[password1]" data-minlength="6" />
						<div class="help-block with-errors"><?php echo JText::_('PLG_K2_FILMFANS_USER_PASSWORD_LENGTH'); ?></div>
					</div>
					<div class="form-group col-lg-4 col-sm-6">
						<label class="control-label" for="password2i"><?php echo JText::_('PLG_K2_FILMFANS_USER_PASSWORD_CONFIRM'); ?></label>
						<input type="password" class="form-control" id="password2i" name="jform[password2]" data-match="#password1i" data-match-error="<?php echo JText::_('PLG_K2_FILMFANS_USER_PASSWORD_NOT_MATCH'); ?>" />
						<div class="help-block with-errors"></div>
					</div>
				</div>

				<div class="row">
					<div class="form-group col-lg-4 col-sm-6">
						<button type="submit" class="btn btn-submit btn-lg btn-block"><?php echo JText::_('PLG_K2_FILMFANS_USER_ACCOUNT_GO'); ?></button>
					</div>
					<div class="form-links col-lg-8 col-sm-6">
						<a href="javascript:void(0)" onclick="bootbox.alert('<?php echo JText::_('PLG_K2_FILMFANS_USER_ACCOUNT_DELETE_MSG', true); ?>')"><?php echo JText::_('PLG_K2_FILMFANS_USER_ACCOUNT_DELETE'); ?></a>
					</div>
				</div>
			</div>

			<div class="profile-tie col-lg-3 col-md-3 col-md-pull-9 col-lg-pull-9" id="comSocialConnectContainer">
				<div class="socialConnectServicesBlock">
					<div class="socialConnectClearFix">
						<label><?php echo JText::_('PLG_K2_FILMFANS_LOGIN_TIE'); ?></label>
						<div class="clearfix"></div>
						<a class="socialConnectFacebookButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="javascript:void(0)" onclick="bootbox.alert('<?php echo JText::_('PLG_K2_FILMFANS_USER_ACCOUNT_DELETE_MSG', true); ?>')">
							<i></i>
							<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_TIE_TO', 'Facebook'); ?></span>
						</a>
						<a class="socialConnectTwitterButton socialConnectButton socialConnectServiceButton socialConnectClearFix" href="javascript:void(0)" onclick="bootbox.alert('<?php echo JText::_('PLG_K2_FILMFANS_USER_ACCOUNT_DELETE_MSG', true); ?>')">
							<i></i>
							<span><?php echo JText::sprintf('PLG_K2_FILMFANS_LOGIN_TIE_TO', 'Twitter'); ?></span>
						</a>
					</div>
				</div>
			</div>

		</div>
	</div>

	<input type="hidden" name="plugins[ffcover]" value="<?php echo FilmFansHelper::getVal('plugins/ffcover', $data); ?>" />
	<input type="hidden" name="jform[username]" value="<?php echo $this->user->get('username'); ?>" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->user->get('id'); ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->user->get('gid'); ?>" />
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="profile.save" />
	<input type="hidden" name="K2UserForm" value="1" />
	<input type="hidden" name="ffaction" value="profile.save" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>