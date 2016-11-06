<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class FilmFansMonoPageViewPage extends JViewLegacy {

	function display($tpl = null) {

		$this->params = JComponentHelper::getParams('com_filmfans_monopage');

		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models/');
		$article = JModelLegacy::getInstance( 'article', 'ContentModel' );
		$category = JModelLegacy::getInstance( 'category', 'ContentModel' );

		$this->about = $this->params->get('aAbout') ? $article->getItem($this->params->get('aAbout')) : null;
		$this->app = $this->params->get('aApp') ? $article->getItem($this->params->get('aApp')) : null;
		$this->terms = $this->params->get('aTerms') ? $article->getItem($this->params->get('aTerms')) : null;
		$this->policy = $this->params->get('aPolicy') ? $article->getItem($this->params->get('aPolicy')) : null;
		$this->faq = array();

		if ($this->params->get('faqCategory')) {
			require_once(JPATH_SITE.'/components/com_content/helpers/query.php');
			$category->getState('category.id');
			$category->setState('category.id', (int) $this->params->get('faqCategory'));
			$category->setState('list.limit', 100);
			$this->faq = $category->getItems();
		}


		parent::display($tpl);
	}
}

/*

        linkAppIOS                                url" label="iOS App Link" description="" />
        linkAppAndroid                                url" label="Android App Link" description="" />
        linkExtChrome                                url" label="Chrome Extention Link" description="" />
        linkExtFF                                url" label="FireFox Extention Link" description="" />
        linkExtIE                                url" label="Internet Explorer Extention Link" description="" />
        linkExtOpera                                url" label="Opera Extention Link" description="" />
        linkExtSafari                                url" label="Safari Extention Link" description="" />
        inviteEmail                                text" label="Invite Email Template" description="" cols="60" rows="10" />-->
        contactEmail                                email" label="Contact Email" description="" />
        supportEmail                                email" label="Support Email" description="" />
        reportEmail                                email" label="Report Abuse Email" description="" />
*/