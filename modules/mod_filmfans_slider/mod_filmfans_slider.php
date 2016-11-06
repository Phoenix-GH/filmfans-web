<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();

JHtml::_('stylesheet', 'media/filmfans//slider/style.css');

$slides = array();
for ($i = 1; $i <= 20; $i++) {
    $key = 'slide'.str_pad($i, 2, '0', STR_PAD_LEFT);
    $image = trim($params->get($key.'_img'));

    if ($image) {
        $slides[] = array(
            'image' => $image,
            'link' => trim($params->get($key.'_link')),
            'title' => trim($params->get($key.'_title'))
        );
    }
}

if (empty($slides)) return;

require JModuleHelper::getLayoutPath('mod_filmfans_slider', $params->get('layout', 'default'));
