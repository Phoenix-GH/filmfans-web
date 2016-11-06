<?php
/**
 * @version     $Id: serviceaccounts.php 3404 2013-07-19 15:05:40Z lefteris.kavadas $
 * @package     SocialConnect
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

require_once JPATH_SITE.'/administrator/components/com_socialconnect/elements/base.php';

class SocialConnectFieldServiceAccounts extends SocialConnectField
{
	public function fetchInput()
	{
		$document = JFactory::getDocument();
		$script = "function socialConnectPopupWindow(url, name, width, height, scroll) {
			var left = (screen.width - width) / 2;
			var top = (screen.height - height) / 2;
			var properties = 'height=' + height + ',width=' + width + ',top=' + top + ',left=' + left + ',scrollbars=' + scroll + ',resizable'
			window.open(url, name, properties).focus();
		}";
		$document->addScriptDeclaration($script);
		if (version_compare(JVERSION, '2.5', 'ge'))
		{
			$service = (string)$this->element['service'];
			$multiple = ((string)$this->element['multi'] == 'true') ? true : false;
		}
		else
		{
			$service = (string)$this->element->attributes('service');
			$multiple = ((string)$this->element->attributes('multi') == 'true') ? true : false;
			$params = JComponentHelper::getParams('com_socialconnect');
			$this->value = array();
			$this->value['token'] = $params->get($this->name.'Token');
			$this->value['secret'] = $params->get($this->name.'Secret');
			$this->value['account'] = $params->get($this->name.'Account');
			$this->value['availableAccounts'] = $params->get($this->name.'AvailableAccounts');
		}

		$link = '<a onclick="javascript:socialConnectPopupWindow(\'index.php?option=com_socialconnect&view=authorize&service='.$service.'\', \'SocialConnect\', 480, 360); return false;" href="index.php?option=com_socialconnect&view=authorize&service='.$service.'">'.JText::_('JW_SC_AUTHORIZE').'</a>';

		$fieldname = version_compare(JVERSION, '2.5', 'ge') ? $this->name.'[token]' : 'params['.$this->name.'Token]';
		$token = '<input id="social-connect-'.$service.'-token" type="hidden" name="'.$fieldname.'" value="'.@htmlspecialchars($this->value['token'], ENT_QUOTES, 'UTF-8').'" />';
		// Secret for OAuth v1 implentations only ( Twitter )
		$fieldname = version_compare(JVERSION, '2.5', 'ge') ? $this->name.'[secret]' : 'params['.$this->name.'Secret]';
		$secret = '<input id="social-connect-'.$service.'-secret" type="hidden" name="'.$fieldname.'" value="'.@htmlspecialchars($this->value['secret'], ENT_QUOTES, 'UTF-8').'" />';
		if ($multiple)
		{
			$fieldname = version_compare(JVERSION, '2.5', 'ge') ? $this->name.'[availableAccounts]' : 'params['.$this->name.'AvailableAccounts]';
			$accounts = '<input id="social-connect-'.$service.'-available-accounts" type="hidden" name="'.$fieldname.'" value="'.@htmlspecialchars($this->value['availableAccounts'], ENT_QUOTES, 'UTF-8').'" />';
			$options = array();
			if (isset($this->value['availableAccounts']))
			{
				$availableAccounts = json_decode(htmlspecialchars_decode($this->value['availableAccounts'], ENT_QUOTES));
				if (is_array($availableAccounts))
				{
					foreach ($availableAccounts as $availableAccount)
					{
						$options[] = JHTML::_('select.option', $availableAccount->id, $availableAccount->name);
					}
				}
			}
			$fieldname = version_compare(JVERSION, '2.5', 'ge') ? $this->name.'[account]' : 'params['.$this->name.'Account]';
			$accounts .= JHTML::_('select.genericlist', $options, $fieldname, '', 'value', 'text', @$this->value['account'], 'social-connect-'.$service.'-account');
		}
		else
		{
			$fieldname = version_compare(JVERSION, '2.5', 'ge') ? $this->name.'[account]' : 'params['.$this->name.'Account]';
			$accounts = '<input id="social-connect-'.$service.'-account" type="text" readonly="readonly" name="'.$fieldname.'" value="'.@htmlspecialchars($this->value['account'], ENT_QUOTES, 'UTF-8').'" />';
		}
		$html = $accounts.$link.$token.$secret;
		return $html;
	}

}

class JFormFieldServiceAccounts extends SocialConnectFieldServiceAccounts
{
	var $type = 'serviceaccounts';
}

class JElementServiceAccounts extends SocialConnectFieldServiceAccounts
{
	var $_name = 'serviceaccounts';
}
