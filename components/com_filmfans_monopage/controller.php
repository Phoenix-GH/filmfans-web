<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class FilmFansMonoPageController extends JControllerLegacy {

	public function display($cachable = false, $safeurlparams = false) {

		parent::display($cachable, $safeurlparams);

		return $this;
	}
}
