<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();

$tags = FilmFansHelper::getTags();
$cats = FilmFansHelper::$behaviour;

foreach ($cats as $k=>$v)
    $cats[$k] = JText::_('PLG_K2_FILMFANS_CAT_'.$k);

FilmFansHelper::prepareFilter();

$p = clone $params;

if (!$params->get('term')) $params->set('term', $mainframe->input->getString('term'));
if (!$params->get('ffcat')) $params->set('ffcat', $mainframe->input->getString('ffcat'));
if (!$params->get('fftag')) $params->set('fftag', $mainframe->input->getInt('fftag'));
if (!$params->get('fford')) $params->set('fford', $mainframe->input->getString('fford'));
if (!$params->get('fffeatured')) $params->set('fffeatured', $mainframe->input->getInt('fffeatured'));
if (!$params->get('ffpopular')) $params->set('ffpopular', $mainframe->input->getInt('ffpopular'));
if (!$params->get('fftmpl')) $params->set('fftmpl', $mainframe->input->getInt('fftmpl'));

$pglobal = FilmFansHelper::params();

require JModuleHelper::getLayoutPath('mod_filmfans_filter', $params->get('layout', 'default'));
