<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();

if ($mainframe->input->getString('service', 'filmfans') != 'filmfans') return;

$tags = FilmFansHelper::getTags();
$cats = FilmFansHelper::$behaviour;

foreach ($cats as $k=>$v)
    $cats[$k] = JText::_('PLG_K2_FILMFANS_CAT_'.$k);

FilmFansHelper::prepareFilter();

$params->set('ffcat', $mainframe->input->getString('ffcat'));
$params->set('term', $mainframe->input->getString('term'));

$pglobal = FilmFansHelper::params();

require JModuleHelper::getLayoutPath('mod_filmfans_filtersimple', $params->get('layout', 'default'));
