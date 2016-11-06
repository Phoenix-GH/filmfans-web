<?php
/**
 * @version     2.6.x
 * @package     K2
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

class K2ModelItem extends K2ModelItemDefault
{

	function getData()
	{
		$cid = JRequest::getVar('cid');
		$row = JTable::getInstance('K2ItemFF', 'Table');
		$row->load($cid);
		return $row;
	}
}
