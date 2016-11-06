<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 1.3
 *
 * The main page of the Akeeba Backup component is where all the fun takes place :)
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Util\Comconfig;

Platform::getInstance()->load_version_defines();
$lang = JFactory::getLanguage();
$icons_root = JUri::base().'components/com_akeeba/assets/images/';

JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen');

$script = <<<JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
(function($){
	$(document).ready(function(){
		$('#btnchangelog').click(showChangelog);
	});

	function showChangelog()
	{
		var akeebaChangelogElement = $('#akeeba-changelog').clone().appendTo('body').attr('id', 'akeeba-changelog-clone');

		SqueezeBox.fromElement(
			document.getElementById('akeeba-changelog-clone'), {
				handler: 'adopt',
				size: {
					x: 550,
					y: 500
				}
			}
		);
	}
})(akeeba.jQuery);

JS;
JFactory::getDocument()->addScriptDeclaration($script,'text/javascript');

?>

<?php
// Obsolete PHP version check
if (version_compare(PHP_VERSION, '5.4.0', 'lt')):
	JLoader::import('joomla.utilities.date');
	$akeebaCommonDatePHP = new JDate('2014-08-14 00:00:00', 'GMT');
	$akeebaCommonDateObsolescence = new JDate('2015-05-14 00:00:00', 'GMT');
	?>
	<div id="phpVersionCheck" class="alert alert-warning">
		<h3><?php echo JText::_('AKEEBA_COMMON_PHPVERSIONTOOOLD_WARNING_TITLE'); ?></h3>
		<p>
			<?php echo JText::sprintf(
				'AKEEBA_COMMON_PHPVERSIONTOOOLD_WARNING_BODY',
				PHP_VERSION,
				$akeebaCommonDatePHP->format(JText::_('DATE_FORMAT_LC1')),
				$akeebaCommonDateObsolescence->format(JText::_('DATE_FORMAT_LC1')),
				'5.5'
			);
			?>
		</p>
	</div>
<?php endif; ?>

<div id="fastcheckNotice" class="alert alert-danger" style="display: none">
	<h3><?php echo JText::_('COM_AKEEBA_CPANEL_ERR_CORRUPT_HEAD') ?></h3>
	<p>
		<?php echo JText::_('COM_AKEEBA_CPANEL_ERR_CORRUPT_INFO') ?>
	</p>
	<p>
		<?php echo JText::_('COM_AKEEBA_CPANEL_ERR_CORRUPT_MOREINFO') ?>
	</p>
	<p>
		<a href="index.php?option=com_akeeba&view=checkfiles" class="btn btn-large btn-primary">
			<?php echo JText::_('COM_AKEEBA_CPANEL_CORRUPT_RUNFILES') ?>
		</a>
	</p>
</div>

<div id="restOfCPanel">

<?php if(!$this->schemaok): ?>
<div style="margin: 1em; padding: 1em; background: #ffff00; border: thick solid red; color: black; font-size: 14pt;" id="notfixedperms">
	<h1 style="margin: 1em 0; color: red; font-size: 22pt;"><?php echo JText::_('CPANEL_SCHEMAERROR_TITLE') ?></h1>
	<p><?php echo JText::_('CPANEL_SCHEMAERROR_BODY') ?></p>
</div>
<?php
	return;
	endif;
?>

<?php if(!$this->fixedpermissions): ?>
<div style="margin: 1em; padding: 1em; background: #ffff00; border: thick solid red; color: black; font-size: 14pt;" id="notfixedperms">
	<h1 style="margin: 1em 0; color: red; font-size: 22pt;"><?php echo JText::_('AKEEBA_CPANEL_WARN_WARNING') ?></h1>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L1') ?></p>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L2') ?></p>
	<ol>
		<li><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L3A') ?></li>
		<li><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L3B') ?></li>
	</ol>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L4') ?></p>
</div>
<?php endif; ?>

<!-- jQuery & jQuery UI detection. Also shows a big, fat warning if they're missing -->
<div id="nojquerywarning" style="margin: 1em; padding: 1em; background: #ffff00; border: thick solid red; color: black; font-size: 14pt;">
	<h1 style="margin: 1em 0; color: red; font-size: 22pt;"><?php echo JText::_('AKEEBA_CPANEL_WARN_ERROR') ?></h1>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_JQ_L1B'); ?></p>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_JQ_L2'); ?></p>
</div>
<script type="text/javascript" language="javascript">
	if(typeof akeeba.jQuery == 'function')
	{
		if(typeof akeeba.jQuery.ui == 'object')
		{
			akeeba.jQuery('#nojquerywarning').css('display','none');
			akeeba.jQuery('#notfixedperms').css('display','none');
		}
	}
</script>

<?php if(!version_compare(PHP_VERSION, '5.3.0', 'ge') && Comconfig::getValue('displayphpwarning', 1)): ?>
<div class="alert">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<p><strong><?php echo JText::_('COM_AKEEBA_CONFIG_LBL_OUTDATEDPHP_HEADER') ?></strong><br/>
	<?php echo JText::_('COM_AKEEBA_CONFIG_LBL_OUTDATEDPHP_BODY') ?>
	</p>

	<p>
		<a class="btn btn-small btn-primary" href="index.php?option=com_akeeba&view=cpanel&task=disablephpwarning&<?php echo JFactory::getSession()->getFormToken() ?>=1">
			<?php echo JText::_('COM_AKEEBA_CONFIG_LBL_OUTDATEDPHP_BUTTON'); ?>
		</a>
	</p>
</div>
<?php endif; ?>

<?php if($this->needsdlid): ?>
<div class="alert">
	<?php echo JText::sprintf('COM_AKEEBA_LBL_CPANEL_NEEDSDLID','https://www.akeebabackup.com/instructions/1435-akeeba-backup-download-id.html'); ?>
</div>
<?php elseif ($this->needscoredlidwarning): ?>
<div class="alert alert-danger">
	<?php echo JText::sprintf('COM_AKEEBA_LBL_CPANEL_NEEDSUPGRADE','https://www.akeebabackup.com/videos/63-video-tutorials/1505-abt03-upgrade-core-to-pro.html'); ?>
</div>
<?php endif; ?>

<div id="updateNotice"></div>

<?php if($this->hasPostInstallationMessages): ?>
<div class="alert alert-info">
	<h3>
		<?php echo JText::_('AKEEBA_CPANEL_PIM_TITLE'); ?>
	</h3>
	<p>
		<?php echo JText::_('AKEEBA_CPANEL_PIM_DESC'); ?>
	</p>
	<a href="index.php?option=com_postinstall&eid=<?php echo $this->extension_id?>"
		class="btn btn-primary btn-large">
		<?php echo JText::_('AKEEBA_CPANEL_PIM_BUTTON'); ?>
	</a>
</div>
<?php elseif(is_null($this->hasPostInstallationMessages)): ?>
	<div class="alert alert-error">
		<h3>
			<?php echo JText::_('AKEEBA_CPANEL_PIM_ERROR_TITLE'); ?>
		</h3>
		<p>
			<?php echo JText::_('AKEEBA_CPANEL_PIM_ERROR_DESC'); ?>
		</p>
		<a href="https://www.akeebabackup.com/documentation/troubleshooter/abpimerror.html"
		   class="btn btn-primary btn-large">
			<?php echo JText::_('AKEEBA_CPANEL_PIM_ERROR_BUTTON'); ?>
		</a>
	</div>
<?php endif; ?>

<div id="cpanel" class="row-fluid">
	<div class="span8">
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-formstyle-reset form-inline">
			<input type="hidden" name="option" value="com_akeeba" />
			<input type="hidden" name="view" value="cpanel" />
			<input type="hidden" name="task" value="switchprofile" />
			<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />
			<label>
				<?php echo JText::_('CPANEL_PROFILE_TITLE'); ?>: #<?php echo $this->profileid; ?>
			</label>
			<?php echo JHTML::_('select.genericlist', $this->profilelist, 'profileid', 'onchange="document.forms.adminForm.submit()" class="advancedSelect"', 'value', 'text', $this->profileid); ?>
			<button class="btn" onclick="this.form.submit(); return false;">
				<i class="icon-retweet"></i>
				<?php echo JText::_('CPANEL_PROFILE_BUTTON'); ?>
			</button>
		</form>

		<h3><?php echo JText::_('CPANEL_HEADER_BASICOPS'); ?></h3>

		<?php foreach($this->icondefs['operations'] as $icon): ?>
		<div class="icon">
			<a href="<?php echo 'index.php?option=com_akeeba'.
				(is_null($icon['view']) ? '' : '&amp;view='.$icon['view']).
				(is_null($icon['task']) ? '' : '&amp;task='.$icon['task']); ?>">
			<div class="ak-icon ak-icon-<?php echo $icon['icon'] ?>">&nbsp;</div>
			<span><?php echo $icon['label']; ?></span>
			</a>
		</div>
		<?php endforeach; ?>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=schedule">
				<div class="ak-icon ak-icon-scheduling">&nbsp;</div>
				<span><?php echo JText::_('AKEEBA_SCHEDULE'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_config&view=component&component=com_akeeba&path=&return=<?php echo base64_encode(JUri::getInstance()->toString()) ?>">
				<div class="ak-icon ak-icon-componentparams">&nbsp;</div>
				<span><?php echo JText::_('CPANEL_LABEL_COMPONENTCONFIG'); ?></span>
			</a>
		</div>

		<div class="ak_clr"></div>

		<?php if(!empty($this->icondefs['inclusion'])): ?>
		<h3><?php echo JText::_('CPANEL_HEADER_INCLUSION'); ?></h3>
		<?php foreach($this->icondefs['inclusion'] as $icon): ?>
		<div class="icon">
			<a href="<?php echo 'index.php?option=com_akeeba'.
				(is_null($icon['view']) ? '' : '&amp;view='.$icon['view']).
				(is_null($icon['task']) ? '' : '&amp;task='.$icon['task']); ?>">
			<div class="ak-icon ak-icon-<?php echo $icon['icon'] ?>">&nbsp;</div>
			<span><?php echo $icon['label']; ?></span>
			</a>
		</div>
		<?php endforeach; ?>
		<div class="ak_clr"></div>
		<?php endif; ?>

		<h3><?php echo JText::_('CPANEL_HEADER_EXCLUSION'); ?></h3>
		<?php foreach($this->icondefs['exclusion'] as $icon): ?>
		<div class="icon">
			<a href="<?php echo 'index.php?option=com_akeeba'.
				(is_null($icon['view']) ? '' : '&amp;view='.$icon['view']).
				(is_null($icon['task']) ? '' : '&amp;task='.$icon['task']); ?>">
			<div class="ak-icon ak-icon-<?php echo $icon['icon'] ?>">&nbsp;</div>
			<span><?php echo $icon['label']; ?></span>
			</a>
		</div>
		<?php endforeach; ?>
		<div class="ak_clr"></div>

	</div>

	<div class="span4">

		<h3><?php echo JText::_('CPANEL_LABEL_STATUSSUMMARY')?></h3>
		<div>
			<?php echo $this->statuscell ?>

			<?php $quirks = Factory::getConfigurationChecks()->getDetailedStatus(); ?>
			<?php if(!empty($quirks)): ?>
			<div>
				<?php echo $this->detailscell ?>
			</div>
			<hr/>
			<?php endif; ?>

			<?php if(!defined('AKEEBA_PRO')) { $show_donation = 1; } else { $show_donation = (AKEEBA_PRO != 1); } ?>
			<p class="ak_version">
				<?php echo JText::_('AKEEBA').' '.($show_donation?'Core':'Professional ').' '.AKEEBA_VERSION.' ('.AKEEBA_DATE.')' ?>
			</p>
			<!-- CHANGELOG :: BEGIN -->
			<?php if($show_donation): ?>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick" />
				<input type="hidden" name="hosted_button_id" value="10903325" />
				<a href="#" id="btnchangelog" class="btn btn-info btn-small">CHANGELOG</a>
				<input type="submit" class="btn btn-inverse btn-small" value="Donate via PayPal" />
				<!--<input class="btn" type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online." style="border: none !important; width: 92px; height 26px;" />-->
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			<?php else: ?>
			<a href="#" id="btnchangelog" class="btn btn-info btn-small">CHANGELOG</a>
			<?php endif; ?>
			<div style="display:none;">
				<div id="akeeba-changelog">
					<?php
					require_once dirname(__FILE__).'/coloriser.php';
					echo AkeebaChangelogColoriser::colorise(JPATH_COMPONENT_ADMINISTRATOR.'/CHANGELOG.php');
					?>
				</div>
			</div>
			<!-- CHANGELOG :: END -->

			<a href="index.php?option=com_akeeba&view=update&task=force" class="btn btn-inverse btn-small">
				<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_RELOADUPDATE'); ?>
			</a>
		</div>

		<h3><?php echo JText::_('BACKUP_STATS') ?></h3>
		<div><?php echo $this->statscell ?></div>

	</div>
</div>

<div class="row-fluid footer">
	<div class="span12">
		<p style="height: 6em">
			<?php echo JText::sprintf('COPYRIGHT', date('Y')); ?><br/>
			<?php echo JText::_('LICENSE'); ?>
			<?php if(AKEEBA_PRO != 1): ?>
			<br/>If you use Akeeba Backup Core, please post a rating and a review at the <a href="http://extensions.joomla.org/extensions/access-a-security/site-security/backup/1606">Joomla! Extensions Directory</a>.
			<?php endif; ?>
			<br/><br/>
			<strong><?php echo JText::_('TRANSLATION_CREDITS')?></strong>:
			<em><?php echo JText::_('TRANSLATION_LANGUAGE') ?></em> &bull;
			<a href="<?php echo JText::_('TRANSLATION_AUTHOR_URL') ?>"><?php echo JText::_('TRANSLATION_AUTHOR') ?></a>
		</p>
	</div>
</div>

</div>

<?php
if($this->statsIframe)
{
    echo $this->statsIframe;
}
?>

<script type="text/javascript">
	(function($) {
		$(document).ready(function(){
			$.ajax('index.php?option=com_akeeba&view=cpanel&task=updateinfo&tmpl=component', {
				success: function(msg, textStatus, jqXHR)
				{
					// Get rid of junk before and after data
					var match = msg.match(/###([\s\S]*?)###/);
					data = match[1];

					if (data.length)
					{
						$('#updateNotice').html(data);
					}
				}
			});

			$.ajax('index.php?option=com_akeeba&view=cpanel&task=fastcheck&tmpl=component', {
				success: function (msg, textStatus, jqXHR)
				{
					// Get rid of junk before and after data
					var match = msg.match(/###([\s\S]*?)###/);
					data = match[1];

					if (data == 'false')
					{
						$('#fastcheckNotice').show('fast');
					}
				}
			});
		});
	})(akeeba.jQuery);
</script>
