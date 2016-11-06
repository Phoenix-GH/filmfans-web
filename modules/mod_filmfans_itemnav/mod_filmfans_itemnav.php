<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();

$mainid = (int) $mainframe->input->getString('mainid', $mainframe->input->getString('~mainid'));

if (!$mainid) return false;

$mainitem = FilmFansHelper::getItem($mainid);

if (!$mainitem->id) return false;

if ($mainitem->background) {
    JFactory::getDocument()->addStyleDeclaration('body { background-image: url("'.$mainitem->background.'"); }');
}

$cats = FilmFansHelper::$behaviour;

foreach ($cats as $k=>$v)
    $cats[$k] = JText::_('PLG_K2_FILMFANS_CAT_'.$k);

FilmFansHelper::prepareFilter();

$params->set('ffcat', $mainframe->input->getString('ffcat', $mainframe->input->getString('~behaviour')));

$pglobal = FilmFansHelper::params();

require JModuleHelper::getLayoutPath('mod_filmfans_itemnav', $params->get('layout', 'default'));
