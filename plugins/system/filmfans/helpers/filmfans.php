<?php
/**
 * @version		1.0
 * @package		FilmFans K2 Plugin
 * @author		jThemes
 * @copyright	Copyright (c) 2010 - 2014 jThemes. All rights reserved.
 * @license		Private
 */

// no direct access
defined('_JEXEC') or die ;

class FilmFansHelper
{

    static $flags = array('stars' => 1, 'director' => 2, 'writer' => 3, 'cast' => 4, 'publisher' => 5);
    static $behaviour = array('movie' => 1, 'trailer' => 2, 'review' => 3, 'image' => 4);

    /**
     * @return JRegistry
     */
    public static function &params() {

        static $params;

        if (!isset($params)) {

            jimport( 'joomla.registry.registry' );

            $plugin = JPluginHelper::getPlugin('system', 'filmfans');
            $params = new JRegistry($plugin->params);
        }

        return $params;
    }

    public static function routeMenuItem($mi, $url = 'index.php') {

        $params = self::params();

        $itemid = (int) $params->get($mi);
        if ($itemid)
            $itemid = (strpos($url, '?') === false ? '?' : '&').'Itemid='.$itemid;
        else
            $itemid = '';

        return JRoute::_($url.$itemid);
    }

    public static function getDD($type, $id, $def = null) {

        $opts = self::getDDList($type);

        return isset($opts[$id]) ? $opts[$id] : $def;
    }

    public static function getDDList($type) {

        static $values;

        if (!isset($values)) {

            $values = array();
            $xml = new SimpleXMLElement(file_get_contents(dirname(__DIR__).'/models/item-movie.xml'));

            foreach (array('country', 'language') as $fs) {

                $values[$fs] = array();

                foreach ($xml->xpath('//field[@name="'.$fs.'"]/option') as $opt) {
                    $_tmp = array_map('trim', explode('/', (string) $opt));
                    $values[$fs][strtolower((string) $opt['value'])] = $_tmp[0];
                }
            }

            unset($xml);

            $xml = new SimpleXMLElement(file_get_contents(dirname(__DIR__).'/models/item-review.xml'));

            foreach (array('reaction' => 'ffreaction') as $k=>$fs) {

                $values[$fs] = array();

                foreach ($xml->xpath('//field[@name="'.$fs.'"]/option') as $opt) {
                    $_tmp = array_map('trim', explode('/', (string) $opt));
                    $values[$k][strtolower((string) $opt['value'])] = $_tmp[0];
                }
            }
        }

        return isset($values[$type]) ? $values[$type] : array();
    }

    public static function getVal($path, $array, $def = null) {

        if (!is_array($path))
            $path = explode('/', $path);

        $part = array_shift($path);

        if (!isset($array[$part])) return $def;

        if (count($path)) return self::getVal($path, $array[$part], $def);

        return $array[$part];
    }

    public static function getLink($id, $alias, $catid, $catalias, $mid = null, $malias = '', $mcatid = null, $mcatalias = '') {

        if (!class_exists('K2HelperRoute')) require_once JPATH_SITE.'/components/com_k2/helpers/route.php';

        $link = K2HelperRoute::getItemRoute($id.':'.$alias, $catid.':'.$catalias);
        return self::_mainLink(urldecode(JRoute::_($link)), $mid, $malias, $mcatid, $mcatalias);
    }

    public static function getCatLink($behaviour, $mid = null, $malias = '', $mcatid = null, $mcatalias = '') {

        $catid = self::categoryGroups($behaviour);
        $catid = array_shift($catid);

        $params = self::categoryParams($catid);

        if (!empty($params['alias'])) {

            $link = K2HelperRoute::getCategoryRoute($catid.':'.$params['alias']);
            return self::_mainLink(urldecode(JRoute::_($link)), $mid, $malias, $mcatid, $mcatalias);

        } else
            return self::getLink($mid, $malias, $mcatid, $mcatalias);
    }

    protected static function _mainLink($link, $mid = null, $malias = '', $mcatid = null, $mcatalias = '') {

        if ($mid) {

            $mainframe = JFactory::getApplication();
            $router = $mainframe->getRouter();

            if ($router->getMode() == JROUTER_MODE_SEF) {

                $mainlink = K2HelperRoute::getItemRoute($mid . ':' . $malias, $mcatid . ':' . $mcatalias);
                $mainlink = urldecode(JRoute::_($mainlink));

                $link = $mainlink . str_replace(JUri::base(true), '', $link);

            } else
                $link .= ( strpos($link, '?') === false ? '?' : '&') . 'mainid=' . $mid . ':' . $malias;
        }

        return $link;
    }

    public static function getAvatar($image, $user) {

        static $k2user;

        $uid = (int) $user->get('id');

        if ($uid && is_null($image)) {

            if (!isset($k2user[$uid])) {
                $db = JFactory::getDBO();
                $db->setQuery("SELECT `image` FROM `#__k2_users` WHERE `userID` = $uid");
                $k2user[$uid] = $db->loadResult();
            }
            $image = $k2user[$uid];
        }

        $k2params = JComponentHelper::getParams('com_k2');
        $awidth = $k2params->get('userImageWidth', '120');

        $avatar_link = $avatar = $image;

        if (strpos($avatar, '/') == false) {
        	$avatar = K2HelperUtilities::getAvatar($uid, $user->get('email'), $awidth);
        	$avatar_link = '';
        } else if ($k2params->get('gravatar') && $user->get('email')) {
        	$avatar = '//www.gravatar.com/avatar/'.md5($user->get('email')).'?s='.$awidth.'&amp;default='.urlencode($avatar_link);
        }

        if (empty($image) && !empty($user->socialConnectData) && $user->socialConnectData->image) $avatar = $user->socialConnectData->image;

        return array($avatar, $avatar_link);
    }

    public static function getCover($image, $part = null) {

        $cover = $cover_link = $image;

        if (empty($cover)) {

            $cover = JUri::base().'media/filmfans/images/back-movie.jpg';

        } else if (strpos($cover, '/') == false) {

            $cover = JUri::base().'media/k2/covers/'.$cover;
            $cover_link = '';
        }

        $res = array($cover, $cover_link);

        return !is_null($part) ? $res[$part] : $res;
    }

    public static function getItem($id) {

        static $items;

        if (!isset($items[$id])) {

            defined('JPATH_COMPONENT') or define('JPATH_COMPONENT', JPATH_SITE.'/components/com_k2');
            defined('JPATH_COMPONENT_ADMINISTRATOR') or define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR.'/components/com_k2');
            JLoader::register('K2Controller', JPATH_COMPONENT.DS.'controllers'.DS.'controller.php');
            JLoader::register('K2Model', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'model.php');
            JLoader::register('K2View', JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'view.php');
            JLoader::register('K2HelperRoute', JPATH_COMPONENT.DS.'helpers'.DS.'route.php');
            JLoader::register('K2HelperPermissions', JPATH_COMPONENT.DS.'helpers'.DS.'permissions.php');
            JLoader::register('K2HelperUtilities', JPATH_COMPONENT.DS.'helpers'.DS.'utilities.php');

            JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2/tables');
            $item = JTable::getInstance('K2Item', 'Table');
            $item->load($id);
            if ($item->id) {
                K2Model::addIncludePath(JPATH_COMPONENT . DS . 'models');
                $model = K2Model::getInstance('Item', 'K2Model');
                $item = $model->prepareItem($item, 'item', '');
                FilmFansHelper::prepareItem($item);
            }

            $items[$id] = $item;
        }

        return $items[$id];
    }

    public static function prepareItem(&$item) {

        $mainframe = JFactory::getApplication();

        $item->plugins = json_decode($item->plugins, true);

        $item->catparams = FilmFansHelper::categoryParams($item->catid);

        $item->behaviour = isset($item->catparams['ffBehaviour']) ? $item->catparams['ffBehaviour'] : 'movie';

        $item->mainid = !empty($item->plugins['fflink']) ? intval($item->plugins['fflink']) : 0;

        $item->story = self::strip_tags(!empty($item->fulltext) ? $item->fulltext : $item->introtext);

        if ($item->mainid) {

            $db = JFactory::getDBO();
            $uid = (int) JFactory::getUser()->get('id');

            $q = $db->getQuery(true)
                ->select('i.`id`, i.`title`, i.`introtext`, i.`alias`, c.`alias` AS `categoryalias`, c.`id` AS `catid`, SUM(lik.`like`) AS `likes`, SUM(lik.`dislike`) AS `dislikes`, ul.`like` - ul.`dislike` AS `userlike`, ur.`reaction` AS `userreact`')
                ->from('`#__k2_items` AS i')
                ->leftjoin('`#__k2_categories` AS c ON c.`id` = i.`catid`')
                ->leftjoin('`#__k2_filmfans_likes` AS lik ON lik.`item` = i.`id`')
                ->leftjoin("`#__k2_filmfans_likes` AS ul ON lik.`item` = i.`id` AND ul.`user` = {$uid}")
                ->leftjoin("`#__k2_filmfans_reactions` AS ur ON lik.`item` = i.`id` AND ur.`user` = {$uid}")
                ->where('i.`id` = '. (int) $item->plugins['fflink'])
                ->group('i.`id`');

            $db->setQuery($q);
            $mainitem = $db->loadObject('JObject');

            if (!empty($mainitem)) {
                $item->link = FilmFansHelper::getLink($item->id, $item->alias, $item->catid, $item->category->alias, $mainitem->id, $mainitem->alias, $mainitem->catid, $mainitem->categoryalias);
            }
        }

        $db = JFactory::getDBO();
        $user = JFactory::getUser();

        if (!isset($item->likes)) {
            $db->setQuery("SELECT SUM(`like`) AS `likes`, SUM(`dislike`) AS `dislikes` FROM `#__k2_filmfans_likes` WHERE `item` = " . (int) $item->id);
            $_tmp = $db->loadRow();
            if (is_array($_tmp)) list($item->likes, $item->dislikes) = $_tmp;
        }

        if (!isset($item->userlike)) {
            if ($user->get('id')) {
                $db->setQuery("SELECT `like` - `dislike` FROM `#__k2_filmfans_likes` WHERE `item` = " . (int) $item->id . ' AND `user` = ' . (int) $user->get('id'));
                $item->userlike = $db->loadResult();
            } else
                $item->userlike = 0;
        }

        if ($user->get('id')) {
            $db->setQuery("SELECT `reaction` FROM `#__k2_filmfans_reactions` WHERE `item` = " . (int) $item->id. ' AND `user` = '.(int) $user->get('id'));
            $item->userreact = $db->loadResult();
        } else
            $item->userlike = 0;

        $item->title_html = $item->title . ( !empty($item->plugins['ffvars']['releasedate']) ? '<span> ('.date('Y', strtotime($item->plugins['ffvars']['releasedate'])).')</span>' : '');
        $item->title_text = strip_tags($item->title_html);
        $mainframe->input->set('term', $item->title_text);

        $item->categoryalias = $item->category->alias;
        if (!empty($item->text)) $item->text = str_replace('{K2Splitter}', '', $item->text);

        $item->_rate = $item->votingPercentage / 20;

        $item->background = JURI::base(true).'/media/filmfans/images/back-movie.jpg';
        foreach ($item->attachments as $a) {
            if (preg_match('/.(jpeg|jpg)$/i', $a->filename) || strpos($a->title, 'background') !== false)
                $item->background = JURI::base(true).'/media/k2/attachments/'.$a->filename;
        }

	    $q = "SELECT `id`, `alias`, `title` FROM `#__k2_items` WHERE `id` %s {$item->id} AND `catid` = {$item->catid} AND `published` = 1 AND ( `publish_up` = ".$db->Quote($db->getNullDate())." OR `publish_up` <= NOW() ) AND ( `publish_down` = ".$db->Quote($db->getNullDate())." OR publish_down >= NOW()) AND `access` IN (".implode(',', $user->getAuthorisedViewLevels()).") AND `trash` = 0 AND `language` IN (".$db->quote(JFactory::getLanguage()->getTag()).", '*') ORDER BY `id` %s LIMIT 1";
	    $db->setQuery(sprintf($q, '>', 'ASC'));
        $item->prevItem = $db->loadAssoc();
	    $db->setQuery(sprintf($q, '<', 'DESC'));
        $item->nextItem = $db->loadAssoc();

	    if (!empty($item->prevItem)) $item->prevItem['link'] = empty($mainitem) ? FilmFansHelper::getLink($item->prevItem['id'], $item->prevItem['alias'], $item->catid, $item->category->alias) : FilmFansHelper::getLink($item->prevItem['id'], $item->prevItem['alias'], $item->catid, $item->category->alias, $mainitem->id, $mainitem->alias, $mainitem->catid, $mainitem->categoryalias);
	    if (!empty($item->nextItem)) $item->nextItem['link'] = empty($mainitem) ? FilmFansHelper::getLink($item->nextItem['id'], $item->nextItem['alias'], $item->catid, $item->category->alias) : FilmFansHelper::getLink($item->nextItem['id'], $item->nextItem['alias'], $item->catid, $item->category->alias, $mainitem->id, $mainitem->alias, $mainitem->catid, $mainitem->categoryalias);

        $item->fulllink = trim(JUri::base(), '/').$item->link;
    }

    public static function _movieInfo($data, $type) {

        static $folder, $path, $values;

        if (!isset($folder)) {
            $folder = JPATH_SITE.'/media/filmfans/';
            $path = JUri::base(true).'/media/filmfans/';
        }

        if (!is_array($data)) $data = trim($data);

        $res = array('ok' => 0, 'class' => 'minfo-'.$type, 'span' => '', 'img' => '', 'b' => '');

        switch ($type) {

            case 'language':
            case 'country':
            case 'publisher':

                if (empty($data)) break;
                $data = (array) $data;

                $result = array();

                foreach ($data as $v) {
                    $alias = trim(preg_replace('/[\-]+/i', '-', preg_replace('/[^a-z0-9~]+/i', '-', strtolower($v))), '-');
                    $a = $res;

                    $a['ok'] = 1;

                    $img = $type.'/'.$alias.'.png';

                    if (!JFile::exists($folder.$img)) {
                        $a['span'] = $type;
                        $a['b'] = self::getDD($type, strtolower($v), $v);
                    } else
                        $a['img'] = $path.$img;

                    $result[] = $a;
                }

                return $result;

                break;

            case 'release':
                if (is_array($data)) $data = array_shift($data);
                if (empty($data)) break;

                $m = $y = $d = '';

                if (!empty($data)) {
                    $_tmp = strtotime($data);

                    if ($_tmp) {
                        $_tmp = JFactory::getDate($data);
                        $m = $_tmp->format('M');
                        $y = $_tmp->format('Y');
                        $d = $_tmp->format('j');
                    } else {
                        foreach (array_filter(explode(' ', $data)) as $v) {
                            if (is_numeric($v) && strlen($v) == 4) $y = $v;
                            if (!is_numeric($v) && strlen($v) >= 3) $m = $v;
                        }
                    }
                }

                if (empty($y)) break;

                $res['span'] = $d;
                $res['b'] = $m.' '.$y;
                $res['img'] = $path.'images/movie-release.png';
                $res['ok'] = 1;

                break;
            case 'runtime' :
                if (is_array($data)) $data = array_shift($data);
                $data = intval($data);
                if (empty($data)) break;

                $res['b'] = JText::sprintf('PLG_K2_FILMFANS_MOVIE_INFO_RUNTIME', $data);
                $res['img'] = $path.'images/movie-runtime.png';
                $res['ok'] = 1;
                break;

            default:
                if (is_array($data)) $data = array_shift($data);
                if (empty($data)) break;

                $res['ok'] = 1;
                $res['span'] = $data;

            break;

        }
        return array($res);
    }

    public static function movieInfo($data) {

        $result = array();

        $result = array_merge($result, self::_movieInfo(FilmFansHelper::getVal('ffvars/country', $data), 'country'));
        $result = array_merge($result, self::_movieInfo(FilmFansHelper::getVal('ffvars/language', $data), 'language'));
        $result = array_merge($result, self::_movieInfo(FilmFansHelper::getVal('fftags/publisher', $data), 'publisher'));

        $result = array_merge($result, self::_movieInfo(FilmFansHelper::getVal('ffvars/releasedate', $data), 'release'));
        $result = array_merge($result, self::_movieInfo(FilmFansHelper::getVal('ffvars/runtime', $data), 'runtime'));

        foreach ($result as $k=>$v)
            if (empty($v['ok']))
                unset($result[$k]);

        return $result;
    }

    public static function movieAfflink($data) {
        ob_start();

        $data = array_map('trim', $data);

        ?>
        <li>
            <?php if (!empty($data['c']) && $data['c']{0} != '_') { ?>
            <a class="hover-switch" href="<?php echo $data['l']; ?>">
                <img src="<?php echo JUri::base(true); ?>/media/filmfans/logos/<?php echo $data['c']; ?>.png" alt="<?php echo $data['c']; ?>" />
                <img class="h" src="<?php echo JUri::base(true); ?>/media/filmfans/logos/<?php echo $data['c']; ?>-hover.png" alt="<?php echo $data['c']; ?>" />
            </a>
            <?php } else if (!empty($data['t'])) { ?>
                <a class="custom" href="<?php echo $data['l']; ?>">
                    <?php if (strpos($data['t'], 'http') === 0) { ?>
                    <img src="<?php echo $data['t']; ?>" alt=" - " />
                    <?php } else echo $data['t']; ?>
                </a>
            <?php } ?>
        </li>
        <?php
        return ob_get_clean();
    }

    /**
     * @param string $video
     * @param string $image
     * @param string $size : xs, s, m, l, xl, <empty>
     * @param integer $id
     * @return string
     */
    public static function getThumb($image, $video, $size = 'M', $id = null) {

        if ($image) return $image;

        $size = strtoupper($size);
        if (empty($image) && $id) {
            $f = md5("Image".$id).'_'.($size ? $size : 'Generic').'.jpg';
            if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.$f))
                $image = JURI::base(true).'/media/k2/items/cache/'.$f;
        }

        if (empty($image) && $video) {

            if (preg_match("#{youtube}(.*?){/youtube}#is", $video, $_matches)) {

                $youtubemap = array('XS' => 'mq', 'S' => 'hq', 'M' => 'sd', 'L' => 'maxres', 'XL' => 'maxres');
                $image = 'http://img.youtube.com/vi/'.$_matches[1].'/'.( isset($youtubemap[$size]) ? $youtubemap[$size] : 'maxres' ).'default.jpg';
            }

            /*
             * 120 => http://img.youtube.com/vi/C0BMx-qxsP4/default.jpg
             * 320 => http://img.youtube.com/vi/C0BMx-qxsP4/mqdefault.jpg
             * 480 => http://img.youtube.com/vi/C0BMx-qxsP4/hqdefault.jpg
             * 640 => http://img.youtube.com/vi/C0BMx-qxsP4/sddefault.jpg
             * MAX => http://img.youtube.com/vi/C0BMx-qxsP4/maxresdefault.jpg
             */
        }

        return $image;
    }

    public static function itemHit($id, $weight = 1) {

        $db = JFactory::getDBO();
        $uid = (int) JFactory::getUser()->get('id');

        $id = (int) $id;
        $weight = (int) $weight;

        if (!$id || !$weight) return false;

        $db->setQuery("INSERT INTO `#__k2_filmfans_hits` SET `item` = {$id}, `user` = {$uid}, `weight` = {$weight}, `stamp` = NOW()");
        $db->execute();

        return true;
    }

    public static function FBInit() {

        static $done;

        if (!isset($done)) {
            $done = 1;

            if (version_compare(PHP_VERSION, '5.4.0', '<')) return;

            define('FACEBOOK_SDK_V4_SRC_DIR', dirname(__DIR__).'/Facebook/');
            require 'fbautoload.php';

            $params = self::params();
            Facebook\FacebookSession::setDefaultApplication($params->get('fb-app-main'), $params->get('fb-secret'));
        }



    }

    public static function FBAppSession() {

        self::FBInit();

        if (version_compare(PHP_VERSION, '5.4.0', '<')) return null;

        $session = Facebook\FacebookSession::newAppSession();

        try {
            $session->validate();
        } catch (Facebook\FacebookRequestException $ex) {
            return false;
        } catch (\Exception $ex) {
            return false;
        }

        return $session;
    }

    public static function FBSession() {
        self::FBInit();
        return false;
    }

    public static function FBPost($type, $uid, $id, $params = array()) {

        $app = JFactory::getApplication();
        $db = JFactory::getDBO();

        $uid = (int) $uid;

        $db->setQuery("SELECT `token`, IF(`expires` < NOW(), 1, 0) AS `expired` FROM `#__k2_filmfans_fbtoken` WHERE `uid` = {$uid}");
        $token = $db->loadAssoc();

        if (empty($token)) {
            $app->enqueueMessage(JText::_('PLG_K2_FILMFANS_FB_NEED'), 'warning');
            return false;
        }

        if ($token['expired']) {
            $app->enqueueMessage(JText::_('PLG_K2_FILMFANS_FB_TOKEN_EXPIRED'), 'warning');
            return false;
        }

        $item = self::getItem($id);

        if (empty($item) || !$item->id) return false;

        $msg = '';
        switch ($type) {
            case 'like':
                $msg = 'I LIKE it!';
                break;
            case 'dislike':
                $msg = 'I DISLIKE it!';
                break;
            case 'reaction':
                if (!$params['reaction']) break;
                $react = self::getDD('reaction', $params['reaction']);
                if (empty($react)) break;
                $msg = 'I said "'.strtoupper($react).'" about this!';
                break;
        }

        if (!empty($msg)) {

            $params = self::params();
            $fbsecret = $params->get('fb-secret');

            $parameters = array( 'link' => $item->fulllink, 'message' => $msg, 'access_token' => $token['token'], 'appsecret_proof' => hash_hmac('sha256', $token['token'], $fbsecret) );

            JLoader::register('SocialConnectHelper', JPATH_SITE . '/components/com_socialconnect/helpers/socialconnect.php');
            SocialConnectHelper::request('https://graph.facebook.com/me/feed', $parameters, 'POST');

            return true;
        }

        return false;

    /*

        if (version_compare(PHP_VERSION, '5.4.0', '<')) return null;

        $session = self::FBSession();

        if ($session) {

            try {

                $request = new Facebook\FacebookRequest(
                                    $session, 'POST', '/me/feed', array(
                                        'link' => 'www.example.com',
                                        'message' => 'User provided message'
                                    )
                                );
                $response = $request->execute()->getGraphObject();

                echo "Posted with id: " . $response->getProperty('id');

            } catch (Facebook\FacebookRequestException $e) {

                echo "Exception occured, code: " . $e->getCode();
                echo " with message: " . $e->getMessage();

            }
        }
    */
    }

    public static function social($link) {

        if (strpos($link, 'http') == false)
            $link = trim(JUri::base(), '/').$link;

        return array(
            'facebook' => '<a class="social s-fb" target="ffsocial" href="https://www.facebook.com/sharer/sharer.php?u='.$link.'" data-applink="facebook://post?u='.$link.'"><i></i><span>Facebook</span></a>',
            'twitter' => '<a class="social s-tw" target="ffsocial" href="http://twitter.com/intent/tweet?url='.$link.'" data-applink="twitter://post?url='.$link.'"><i></i><span>Twitter</span></a>',
            'google' => '<a class="social s-gp" target="ffsocial" href="https://plus.google.com/share?url='.$link.'" data-applink="https://plus.google.com/share?url='.$link.'"><i></i><span>Google +</span></a>'
        );
    }

    public static function timeAgo($date){

        require_once 'timeago.php';

        return timeAgoInWords($date);
    }

    public static function strip_tags($str) {
        return trim(
            preg_replace('/\s+/u', ' ',
                str_ireplace(array('&nbsp;', '&Acirc;'), ' ',
                    htmlentities(
                        strip_tags(
                            html_entity_decode($str))))));
    }

    public static function uniteFlags($flag) {

        if (!isset(FilmFansHelper::$flags[$flag])) return array();

        switch ($flag) {
            case 'stars':
            case 'cast':
                return array(FilmFansHelper::$flags['stars'], FilmFansHelper::$flags['cast']);
                break;
            case 'director':
            case 'writer':
                return array(FilmFansHelper::$flags['director'], FilmFansHelper::$flags['writer']);
                break;
        }

        return array();
    }

    public static function getTags() {

        static $tags;

        if (!isset($tags)) {

            $db = JFactory::getDBO();
            $db->setQuery('SELECT `id`, `name` FROM `#__k2_tags` WHERE `published` > 0 ORDER BY `name` ASC');
            $tags = $db->loadAssocList('id', 'name');
        }

        return $tags;

    }

    public static function prepareFilter() {

        static $done;

        if (isset($done)) return;

        $mainframe = JFactory::getApplication();
        $tags = array_flip(array_map('strtolower', FilmFansHelper::getTags()));
        $tag = strtolower($mainframe->input->get('tag'));
        if (!empty($tag) && isset($tags[$tag])) {
            $tag = $tags[$tag];
        } else
            $tag = null;

        $cat = $mainframe->input->getInt('id');
        if (!empty($cat)) {
            $_tmp = FilmFansHelper::categoryParams($cat);
            $cat = isset($_tmp['ffBehaviour']) ? $_tmp['ffBehaviour'] : null;
        } else
            $cat = null;

        if (!empty($tag)) $mainframe->input->set('fftag', $mainframe->input->getString('fftag', $tag));
        if (!empty($cat)) $mainframe->input->set('ffcat', $mainframe->input->getString('ffcat', $cat));

        $done = 1;
    }

    public static function addChosen($selector = 'select:not(.no-chosen)', $options = array()) {

		$options['disable_search_threshold']  = isset($options['disable_search_threshold']) ? $options['disable_search_threshold'] : 10;
		$options['allow_single_deselect']     = isset($options['allow_single_deselect']) ? $options['allow_single_deselect'] : true;
		$options['placeholder_text_multiple'] = isset($options['placeholder_text_multiple']) ? $options['placeholder_text_multiple']: JText::_('JGLOBAL_SELECT_SOME_OPTIONS');
		$options['placeholder_text_single']   = isset($options['placeholder_text_single']) ? $options['placeholder_text_single'] : JText::_('JGLOBAL_SELECT_AN_OPTION');
		$options['no_results_text']           = isset($options['no_results_text']) ? $options['no_results_text'] : JText::_('JGLOBAL_SELECT_NO_RESULTS_MATCH');

		$options_str = json_encode($options);

		JHtml::_('script', 'jui/chosen.jquery.min.js');
		JHtml::_('stylesheet', 'jui/chosen.css');
		JFactory::getDocument()->addScriptDeclaration("
				jQuery(function ($) {
				    $(document).on('onFFLoad', function (e, parent) {
					    $(parent).find('" . $selector . "').chosen(" . $options_str . ");
					});
				});
			"
		);
    }

    public static function cutString($str, $len = 100, $end = '...') {

        jimport('joomla.string');

        $length = abs((int) $len);

        $str = self::strip_tags($str);

        if ($len > JString::strlen($str)) return $str;

        $str = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1'.$end, $str);

        if ($end)
            $str = trim($str, '.').$end;

        return $str;
    }

    /**
     * Return associated array id => params of categories
     *
     * @param string $group Category group name
     * @return array Categories ids belong to group
     */
    public static function categoryGroups($group = null) {

        static $groups;

        if (!isset($groups)) {

            foreach (self::categoryParams() as $k=>$cat) {
                if (!empty($cat['ffBehaviour']))
                    $groups[$cat['ffBehaviour']][] = $k;
            }
        }

        return is_null($group) ? $groups : (isset($groups[$group]) ? $groups[$group] : array(0));
    }

    /**
     * Return associated array id => params of categories
     *
     * @param integer $id Category id to get params of exact category
     * @return array Category FilmFans Parameters
     */
    public static function categoryParams($id = null) {

        static $cats, $defs;

        if (!isset($cats)) {

            $defs = array( 'ffParent' => 0, 'ffChild' => 0, 'ffBehaviour' => '' );

            $db = JFactory::getDBO();

            $db->setQuery('SELECT `id`, `params`, `alias` FROM `#__k2_categories` WHERE `published` = 1 AND `trash` = 0');
            $cats = $db->loadAssocList('id');

            foreach ($cats as $k => $v)
                $cats[$k] = array_merge($defs, json_decode($v['params'], true), array('alias' => $v['alias']));
        }

        return is_null($id) ? $cats : (isset($cats[$id]) ? $cats[$id] : $defs);
    }

    public static function disableAssets($assets = array()) {

        $doc = JFactory::getDocument();
        foreach ($assets as $v) {
            $v = trim($v, '\/');
            foreach (array('_styleSheets', '_scripts') as $type) {
                foreach ($doc->{$type} as $k => $vvv)
                    if (strpos($k, $v) !== false) unset($doc->{$type}[$k]);
            }
        }
    }

    /**
     * Clear all buffers and return their content
     * @return string
     */
    public static function ob_get_clean_all() {

        $output = '';
        while (ob_get_level())
            $output .= ob_get_clean().PHP_EOL;

        return $output;
    }

    public static function exit_json($respond, $header = array()) {

        $head = array('Content-type' => 'application/json');

        if (is_array($header))
            $head = array_merge($head, $header);

        header('Pragma: no-cache');
        header('Cache-Control: private, no-cache');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        foreach ($head as $k=>$v)
            header("$k: $v");

        if (is_string($respond) && strpos($respond, '?') === 0)
            parse_str(trim($respond, '?'), $respond);

        self::ob_get_clean_all();

        if (!defined('JSON_UNESCAPED_UNICODE')) {
            define('JSON_UNESCAPED_SLASHES', 64);
            define('JSON_UNESCAPED_UNICODE', 256);
        }

        jexit(json_encode($respond, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Output Debug info - DEV ONLY
     */
    public static function _sd() {

        $output = self::ob_get_clean_all();

        ob_start();
        include('kint/Kint.class.php');
        call_user_func_array(array('Kint', 'dump'), func_get_args());
        Kint::trace();
        jexit(ob_get_clean().($output ? '<h2 style="padding: 1em 1em; margin: 2em 0 1em; border-bottom: 1px dashed #AAA; border-top: 1px solid #AAA; background: #F5F5F5;">Output before breakpoint:</h2>'.$output : ''));
    }
}


/* //SQL

ALTER TABLE `jff_k2_items` ADD `ffpopularity` INT NOT NULL , ADD INDEX ( `ffpopularity` );

 */