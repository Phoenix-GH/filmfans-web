<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();

JHtml::_('script', 'system/html5fallback.js', false, true);
JHtml::_('script', 'media/filmfans/js/validator.min.js');
JHtml::_('script', 'media/filmfans/js/jquery.date-plugin.js');
JHtml::_('formbehavior.chosen', '#country1s', null, array('placeholder_text_single' => '...'));

$country = false;
$form = false;
foreach ($this->K2Plugins as $K2Plugin) {
	if (isset($K2Plugin->id) && $K2Plugin->id == 'ffuser' && is_a($K2Plugin->form, 'JForm')) {
		$form = &$K2Plugin->form;
		$country = &$form->getField('country');
	}
}

$data = $mainframe->getUserState('plg_ffuser.registration.data');

//FilmFansHelper::_sd($data);

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

<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?> pagesignup">
	<?php echo $this->escape($this->params->get('page_title')); ?>
	<small><?php echo JText::_('PLG_K2_FILMFANS_USER_REQUIRED'); ?></small>
</div>

<?php if(isset($this->message)) $this->display('message'); ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="ff-form fixheight" role="form" data-toggle="validator">

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
			<input type="text" class="form-control" id="birthdate1i" name="plugins[ffuser][birthdate]" placeholder="yyyy / mm / dd" pattern="(19|20)[0-9]{2}\s*\/\s*[0-9]{2}\s*\/\s*[0-9]{1,2}" required value="<?php echo str_replace('/', ' / ', FilmFansHelper::getVal('plugins/ffuser/birthdate', $data)); ?>" />
			<div class="help-block with-errors"></div>
		</div>
		<div class="form-group col-lg-4 col-sm-6">
			<label class="control-label" for="country1s"><?php echo JText::_('PLG_K2_FILMFANS_USER_COUNTRY'); ?></label>
			<?php if (!empty($country)) {
				$country->__set('value', FilmFansHelper::getVal('plugins/ffuser/country', $data));
				$country->__set('id', 'country1s');
				$country->__set('class', 'form-control');
				$country->__set('required', 'required');
				$country->__set('name', 'plugins[ffuser][country]');
				echo $country->__get('input');
			} else { ?>
			<input type="text" class="form-control" id="country1s" name="plugins[ffuser][country]" required />
			<?php } ?>
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
			<label class="control-label" for="password1i"><?php echo JText::_('PLG_K2_FILMFANS_USER_PASSWORD_CREATE'); ?></label>
			<input type="password" class="form-control" id="password1i" name="jform[password1]" required data-minlength="6" />
			<div class="help-block with-errors"><?php echo JText::_('PLG_K2_FILMFANS_USER_PASSWORD_LENGTH'); ?></div>
		</div>
		<div class="form-group col-lg-4 col-sm-6">
			<label class="control-label" for="password2i"><?php echo JText::_('PLG_K2_FILMFANS_USER_PASSWORD_CONFIRM'); ?></label>
			<input type="password" class="form-control" id="password2i" name="jform[password2]" required data-match="#password1i" data-match-error="<?php echo JText::_('PLG_K2_FILMFANS_USER_PASSWORD_NOT_MATCH'); ?>" />
			<div class="help-block with-errors"></div>
		</div>
	</div>

	<button type="submit" class="btn btn-submit btn-lg btn-block"><?php echo JText::_('PLG_K2_FILMFANS_USER_REGISTER_GO'); ?></button>

	<input type="hidden" name="option" value="<?php echo $this->optionValue; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->taskValue; ?>" />
	<input type="hidden" name="id" value="0" />
	<input type="hidden" name="gid" value="0" />
	<input type="hidden" name="K2UserForm" value="1" />
	<input type="hidden" name="ffaction" value="register" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
