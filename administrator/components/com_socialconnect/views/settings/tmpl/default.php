<?php
/**
 * @version		$Id: default.php 3438 2013-07-29 16:05:27Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */
defined('_JEXEC') or die;
?>
<script type="text/javascript">
if ( typeof (jQuery) != 'undefined') {
	function setSettingsHeight() {
		var height = jQuery(window).height();
		jQuery('#scSettings').css('height', (0.7*height) + 'px');
	}
	var resizeTimer;
	jQuery(window).resize(function() {
		clearTimeout(resizeTimer);
    	resizeTimer = setTimeout(setSettingsHeight, 100);
	});
	jQuery(document).ready(function() {
		jQuery('html').css('overflow-y', 'hidden');
		jQuery('body').css('overflow-y', 'hidden');
		setSettingsHeight();
		jQuery('#scSettingszMenu a').click(function(event) {
			event.preventDefault();
			var index = this.href.indexOf('#') + 1;
			var id = this.href.substring(index);
			document.getElementById('scSettings').scrollTop = document.getElementById(id).offsetTop - document.getElementById('scSettings').offsetTop;
		});
	});
} else {
	function setSettingsHeight() {
		var height = window.getHeight();
		$('scSettings').setStyle('height', (0.7*height) + 'px');
	}
	var resizeTimer;
	window.addEvent('resize', function() {
		clearTimeout(resizeTimer);
    	resizeTimer = setTimeout(setSettingsHeight, 100);
	});
	window.addEvent('domready', function() {
		$$('html').setStyle('overflow-y', 'hidden');
		$$('body').setStyle('overflow-y', 'hidden');
		setSettingsHeight();
		$$('#scSettingsMenu a').addEvent('click', function(event) {
			event.preventDefault();
			var index = this.href.indexOf('#') + 1;
			var id = this.href.substring(index);
			document.getElementById('scSettings').scrollTop = document.getElementById(id).offsetTop - document.getElementById('scSettings').offsetTop;
		});
	});
}
</script>
<div id="socialConnect">

	<div id="jwHeader">
		<div id="jwLogo">
   			<i>SocialConnect</i>
   		</div>
   		<span id="jwVersion">v1.5.1</span>
   		<ul id="jwToolbar" class="jwActionsMenu">
   			<li><a onclick="<?php echo $this->submitFunction; ?>('save'); return false;" href="#"><?php echo JText::_('JW_SC_COM_SAVE_AND_CLOSE'); ?></a></li>
   			<li><a onclick="<?php echo $this->submitFunction; ?>('apply'); return false;" href="#"><?php echo JText::_('JW_SC_COM_SAVE'); ?></a></li>
   			<li><a onclick="<?php echo $this->submitFunction; ?>('cancel'); return false;" href="#"><?php echo JText::_('JW_SC_COM_CLOSE'); ?></a></li>
   			<li class="scClear"></li>
   		</ul>
   		<div class="scClear"></div>
	</div>

	<div id="jwSubheader">
		<h3 class="jwPageTitle"><?php echo JText::_('JW_SC_COM_SETTINGS'); ?></h3>
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
		<div id="scSettingsContainer">
		<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
			
			<?php if (version_compare(JVERSION, '2.5.0', 'ge')): ?>
				<div id="scSettingsMenu">
					<ul>
						<?php foreach ($this->form->getFieldsets() as $name => $fieldset): ?>
							<li><a class="jw-icon-right-open-mini" href="#<?php echo $name; ?>"><?php echo JText::_($fieldset->label); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div id="scSettings">
				<?php foreach ($this->form->getFieldsets() as $name => $fieldset): ?>
					<div class="scSettingsSection">
						<h3 class="scSettingsSectionHeader" id="<?php echo $name; ?>"><?php echo JText::_($fieldset->label); ?></h3>
						<?php if (isset($fieldset->description) && !empty($fieldset->description)) : ?>
							<p><?php echo JText::_($fieldset->description); ?></p>
						<?php endif; ?>
		
						<?php foreach ($this->form->getFieldset($name) as $field): ?>
							<div class="scSettingsRow scSettingsField<?php echo $field->type; ?>">
								<?php echo $field->label; ?>
								<?php echo $field->input; ?>
							</div>
							<div class="clr"></div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div id="scSettingsMenu">
					<ul>
						<?php foreach ($this->form->_xml as $fieldset): ?>
							<li><a class="jw-icon-right-open-mini" href="#<?php echo $fieldset->attributes('group'); ?>"><?php echo JText::_($fieldset->attributes('label')); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div id="scSettings">
				<?php foreach ($this->form->_xml as $fieldset): ?>
					<div class="scSettingsSection">
						<h3 class="scSettingsSectionHeader" id="<?php echo $fieldset->attributes('group'); ?>"><?php echo JText::_($fieldset->attributes('label')); ?></h3>
		
						<?php if ($fieldset->attributes('description')) : ?>
							<p><?php echo JText::_($fieldset->attributes('description')); ?></p>
						<?php endif; ?>
		
						<div class="<?php echo $fieldset->attributes('group'); ?>Fieldset">
							<?php echo $this->form->render('params', $fieldset->attributes('group')); ?>
						</div>
		
						<div class="clr"></div>
					</div>
				<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="scClear"></div>
		
		    <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		    <input type="hidden" name="component" value="com_socialconnect" />
		    <input type="hidden" name="option" value="com_socialconnect" />
		    <input type="hidden" name="view" value="settings" />
		    <input type="hidden" id="task" name="task" value="" />
		    <input type="hidden" name="return" value="<?php echo JRequest::getVar('return'); ?>" />
			<?php echo JHTML::_('form.token'); ?>
		</form>
		</div>
	</div>
</div>