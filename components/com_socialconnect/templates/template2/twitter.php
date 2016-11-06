<?php
/**
 * @version		$Id: twitter.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die ;
?>
<div id="comSocialConnectContainer">
	<h2 class="socialConnectHeading"><?php echo JText::_('JW_SC_COM_PLEASE_TYPE_YOUR_EMAIL_ADDRESS_SO_WE_CAN_CONTACT_YOU')?></h2>
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post">
    	<fieldset>
        	<label class="socialConnectLabel" for="socialConnectEmail"><?php echo JText::_('JW_SC_COM_EMAIL_ADDRESS') ?></label>
        	<input class="socialConnectInput" id="socialConnectEmail" type="text" name="email"/>
        	<button class="socialConnectButton socialConnectSignInButton" type="submit" id="socialConnectEmailFormSubmitButton">
        		<span><?php echo JText::_('JW_SC_COM_SUBMIT') ?></span>
        	</button>
    	</fieldset>
    	<input type="hidden" name="option" value="com_socialconnect" />
    	<input type="hidden" name="task" value="updateEmail" />
	    <?php echo JHTML::_('form.token'); ?>
	</form>
</div>