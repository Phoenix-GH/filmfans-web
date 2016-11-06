<?php defined('_JEXEC') or die;

class FilmFans_MonoPageRouter extends JComponentRouterBase
{

    static protected function itemid() {
        static $id;

        if (!isset($id)) {
            $id = 0;
            $menu = JFactory::getApplication()->getMenu();
            $items = $menu->getItems(array('component'), array('com_filmfans_monopage'));
            foreach ($items as $k=>$v) {
                if (isset($v->query['view']) && $v->query['view'] == 'page') {
                    $id = $v->id;
                }
            }
        }

        return $id;
    }

	public function build(&$query) {

        if ($query['Itemid']) {
            $menu = JFactory::getApplication()->getMenu();
            $mi = $menu->getItem($query['Itemid']);
            $query = array_merge($mi->query, $query);
        }

        if ($query['view'] == 'hash' && !isset($query['ffmenuitem']))
            $query['ffmenuitem'] = self::itemid();

        if (!empty($query['ffmenuitem']))
            $query['Itemid'] = $query['ffmenuitem'];

        $hash = '';
        if (isset($query['ffhash']))
            $hash = '#'.$query['ffhash'];

        unset($query['ffhash'], $query['ffmenuitem'], $query['view']);

        return array($hash);
    }

    public function parse(&$segments) {
        return array();
    }

}