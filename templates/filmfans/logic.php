<?php defined( '_JEXEC' ) or die;

require_once('lib/helper.php');

if (!isset($this->_short)) $this->_short = false;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$menu = $app->getMenu();
$active = $menu->getActive();
$params = $app->getParams();
$pageclass = $params->get('pageclass_sfx');
$tpath = $this->baseurl.'/templates/'.$this->template;

$uri = trim(str_replace(JUri::base(true), '', JUri::getInstance()->getPath()), '/');
$is_front = $active == $menu->getDefault() && empty($uri);

$pglobal = FilmFansHelper::params();
$_mi = (int) $pglobal->get('miSearch');
$search_url = $_mi ? JRoute::_('index.php?Itemid='.$_mi) : JUri::root();

$this->setGenerator(null);

$doc->addStyleSheet($tpath.'/css/template.css');
$doc->addStyleSheet($tpath.'/css/custom.css');

$doc->addScript($tpath.'/js/bootstrap.min.js');
$doc->addScript($tpath.'/js/bootstrap-select.min.js');
$doc->addScript($tpath.'/js/imagesloaded.js');
$doc->addScript($tpath.'/js/masonry.js');
$doc->addScript($tpath.'/js/bootbox.min.js');
$doc->addScript($tpath.'/js/extra.js');
$doc->addScript($tpath.'/js/logic.js');

$services = array('imdb', 'youtube', 'google', 'filmfans', 'bing', 'wiki', 'yahoo');
$service = $app->input->get('service', 'filmfans');
