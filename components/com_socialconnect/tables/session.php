<?php
/**
 * @version		$Id: session.php 2437 2013-01-29 14:14:53Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die ;

class SocialConnectSession extends JTable
{

	var $session_id = null;
	var $type = null;

	public function __construct(&$db)
	{
		parent::__construct('#__socialconnect_sessions', 'session_id', $db);
	}

	public function store($updateNulls = false)
	{
		$exists = false;
		if ($this->session_id)
		{
			$this->_db->setQuery("SELECT COUNT(*) FROM #__socialconnect_sessions WHERE session_id = ".$this->_db->quote($this->session_id));
			$exists = $this->_db->loadResult();
		}
		if ($exists)
		{
			$result = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
		}
		else
		{
			$result = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}
		return $result;
	}

}
