<?php
/**
 * @version		3.2
 * @package		DISQUS Comments for Joomla! (package)
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die; ?>

<?php echo $this->row->text; ?>

<div class="socialConnectCommentsCounter">
	<div class="socialConnect<?php echo $this->service; ?>CommentsCounter">
		<?php if($this->service == 'Facebook'): ?>
			<a href="<?php echo $this->itemURL; ?>#socialConnect<?php echo $this->service; ?>CommentsAnchor"><?php echo $this->counter; ?> <?php echo JText::_('JW_SC_COMMENTS'); ?></a>
		<?php else: ?>
			<?php echo $this->counter; ?>
		<?php endif; ?>
	</div>
	<div class="clr"></div>
</div>