<?php defined( '_JEXEC' ) or die;

require_once(dirname(__DIR__).'/filmfans/lib/helper.php');

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$menu = $app->getMenu();
$active = $menu->getActive();
$params = $app->getParams();
$pageclass = $params->get('pageclass_sfx');
$tpath = $this->baseurl.'/templates/filmfans';

$uri = trim(str_replace(JUri::base(true), '', JUri::getInstance()->getPath()), '/');
$is_front = $active == $menu->getDefault() && empty($uri);

$pglobal = FilmFansHelper::params();

$this->setGenerator(null);

$doc->addStyleSheet($tpath.'/css/backend.css');
$doc->addStyleSheet($tpath.'/css/custom.css');

$doc->addScript($tpath.'/js/bootstrap.min.js');
$doc->addScript($tpath.'/js/bootstrap-select.min.js');
$doc->addScript($tpath.'/js/bootbox.min.js');
$doc->addScript($tpath.'/js/jquery.sidr.min.js');
$doc->addScript($tpath.'/js/extra.js');
//$doc->addScript($tpath.'/js/logic.js');
