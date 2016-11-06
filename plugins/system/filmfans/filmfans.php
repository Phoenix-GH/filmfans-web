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

require_once('helpers/filmfans.php');

class plgSystemFilmFans extends JPlugin
{

	var $pluginName = 'filmfans';
	var $pluginNameHumanReadable = 'FilmFans K2 Plugin';

	function plgSystemFilmFans(&$subject, $params) {

        JFactory::getLanguage()->load('plg_system_filmfans', JPATH_ROOT);

		parent::__construct($subject, $params);
	}

    function onAfterK2Save(&$row, $isNew) {

        $values = json_decode($row->plugins, true);
        $id = (int) $row->id;

        if (!$id) return;

        $params = FilmFansHelper::categoryParams($row->catid);

        $db = JFactory::getDBO();

        $q = "DELETE FROM `#__k2_filmfans_link` WHERE `itemID` = $id";
        $db->setQuery($q);
        $db->execute();

        if (!empty($values['fflink'])) {

            $link = (int) $values['fflink'];

            $flag = isset($params['ffBehaviour']) && isset(FilmFansHelper::$behaviour[$params['ffBehaviour']]) ? FilmFansHelper::$behaviour[$params['ffBehaviour']] : 0;

            $q = "INSERT INTO `#__k2_filmfans_link` (`mainID`, `itemID`, `flag`) VALUES($link, $id, $flag)";
            $db->setQuery($q);
            $db->execute();
        }

        $q = "DELETE FROM `#__k2_filmfans_tags_xref` WHERE `itemID` = $id";
        $db->setQuery($q);
        $db->execute();

        if (isset($values['fftags'])) {

            foreach (FilmFansHelper::$flags as $type=>$flag) if (!empty($values['fftags'][$type])) {

                $flag = (int) $flag;
                $cc = 0;

                foreach ($values['fftags'][$type] as $tag) {

                    $tag = trim($tag);

                    $q = "SELECT `id` FROM `#__k2_filmfans_tags` WHERE `name` LIKE ".$db->q($db->escape($tag))." LIMIT 1";
                    $db->setQuery($q);
                    $tagid = (int) $db->loadResult();

                    if (!$tagid) {

                        $tag = ucwords($tag);

                        $q = "INSERT INTO `#__k2_filmfans_tags` (`name`, `published`, `flag`) VALUES(".$db->q($db->escape($tag)).", 1, $flag)";
                        $db->setQuery($q);
                        $db->execute();

                        $tagid = (int) $db->insertid();
                    }

                    if ($tagid) {
                        $q = "INSERT INTO `#__k2_filmfans_tags_xref` (`tagID`, `itemID`, `flag`, `ord`) VALUES($tagid, $id, $flag, $cc)";
                        $db->setQuery($q);
                        $db->execute();
                    }
                }
            }
        }
    }

    function onUserAfterLogin($options) {

        if (empty($options['user']) || !is_a($options['user'], 'JUser')) return true;

        $db = JFactory::getDBO();
        $session = JFactory::getSession();
        $uid = (int) $options['user']->get('id');

        $params = FilmFansHelper::params();
        $fbkey = $params->get('fb-app-main');
        $fbsecret = $params->get('fb-secret');

        $data = $session->get('socialConnectData');
        $token = $session->get('socialConnectFacebookAccessToken');

        if (!$uid || !is_object($data) || $data->type != 'facebook' || empty($token) || empty($fbkey) || empty($fbsecret)) return true;

        //Requests long-term token and stores it in tie to user id
        $parameters = array('grant_type' => 'fb_exchange_token', 'fb_exchange_token' => $token, 'client_id' => $fbkey, 'client_secret' => $fbsecret);
        JLoader::register('SocialConnectHelper', JPATH_SITE.'/components/com_socialconnect/helpers/socialconnect.php');
        $result = SocialConnectHelper::request('https://graph.facebook.com/oauth/access_token', $parameters, 'GET');

        parse_str($result, $longtoken);

        if (!empty($longtoken['access_token'])) {
            $set = "`stamp` = NOW(), `token` = ".$db->q($db->escape($longtoken['access_token'])).", `expires` = ".$db->q(date('Y-m-d H:i:s', time() + $longtoken['expires']));
            $q = "INSERT INTO `#__k2_filmfans_fbtoken` SET `uid` = {$uid}, {$set} ON DUPLICATE KEY UPDATE {$set}";
            $db->setQuery($q);
            $db->execute();
        }

        return true;
    }

    function onAfterRoute() {

        $mainframe = JFactory::getApplication();
        $user = JFactory::getUser();
        $db = JFactory::getDBO();

        $params = FilmFansHelper::params();

        $action = $mainframe->input->get('ffaction');

        if ($action) {

            switch ($action) {

                case 'cron':
                case 'do.cron':

                    $q = "UPDATE `#__k2_items` SET `ffpopularity` = (SELECT SUM(`weight`) FROM `#__k2_filmfans_hits` AS hits WHERE hits.`item` = `#__k2_items`.`id` AND hits.stamp > DATE_SUB(NOW(), INTERVAL 30 DAY))";
                    $db->setQuery($q);
                    $db->execute();

                    FilmFansHelper::ob_get_clean_all();

                    echo 'Done - ok';
                    jexit();

                    break;

                case 'img':

                    FilmFansHelper::ob_get_clean_all();

                    $opts = array( 'http' =>
                        array(
                            'timeout' => 5
                        )
                    );

                    $context = stream_context_create($opts);

                    echo file_get_contents($mainframe->input->getString('url'), false, $context);

                    jexit();

                    break;

                case 'profile.save':
                case 'profilesave':
                case 'register':

                    $array = $mainframe->input->post->getArray();

                    $uid = @$array['jform']['id'];
                    $_user = JFactory::getUser($uid);

                    if ($action != 'register' && (!$_user->get('id') || $_user->get('id') != $user->get('id'))) break;
                    if ($action == 'register' && $_user->get('id')) break;

                    $array['plugins']['ffuser']['firstname'] = trim(@$array['plugins']['ffuser']['firstname']);
                    $array['plugins']['ffuser']['lastname'] = trim(@$array['plugins']['ffuser']['lastname']);
                    $array['plugins']['ffuser']['birthdate'] = date('Y/m/d', strtotime(str_replace(' ', '', @$array['plugins']['ffuser']['birthdate'])));

                    $array['jform']['username'] = $array['jform']['email2'] = @$array['jform']['email1'];
                    $array['jform']['name'] = trim(@$array['plugins']['ffuser']['firstname']) . ' ' . trim(@$array['plugins']['ffuser']['lastname']);

                    $data = 'registration';
                    switch ($action) {
                        case 'profile.save':
                        case 'profilesave':
                            $mainframe->setUserState('plg_ffuser.profile.data', $array);
                            $array['jform']['username'] = $_user->get('username');
                            $data = 'profile';
                            break;
                    }

                    $mainframe->input->post->set('jform', $array['jform']);
                    $mainframe->input->post->set('plugins', $array['plugins']);

                    $mainframe->setUserState('plg_ffuser.'.$data.'.data', $array);

                    break;

                case 'profile.avatar':
                case 'profileavatar':

                    $url = base64_decode($mainframe->input->get('return'));
                    if (!$url) $url = JUri::current();

                    $uid = $mainframe->input->getInt('uid');
                    $aurl = trim($mainframe->input->getString('avatarurl'));
                    $adel = $mainframe->input->getInt('avatardel', 0);
                    $file = $mainframe->input->files->get('avatar');
                    if (is_array($file) && !isset($file['error'])) $file = array_shift($file);

                    if ($uid != $user->get('id')) {
                        $mainframe->enqueueMessage('Access denied', 'alert');
                        $mainframe->redirect($url);
                    }

                    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
                    $row = JTable::getInstance('K2User', 'Table');

              		$db->setQuery("SELECT `id` FROM `#__k2_users` WHERE `userID` = {$uid}");
              		$k2id = $db->loadResult();

                    if (!$k2id) {
                        $mainframe->enqueueMessage('Access denied', 'alert');
                        $mainframe->redirect($url);
                    }

                    $row->load($k2id);

                    $savepath = JPATH_ROOT . '/media/k2/users/';
                    $oldimage = $savepath.DIRECTORY_SEPARATOR.$row->image;
                    $updated = false;

                    if ($adel) {

                        if (JFile::exists($oldimage))
                            JFile::delete($oldimage);

                        $row->image = '';
                        $updated = true;
                    }

                    if (!empty($file['name']) && $file['error'] == 0) {

                        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_k2' . DS . 'lib' . DS . 'class.upload.php');

                        $params = JComponentHelper::getParams('com_k2');

                        $handle = new Upload($file);
                        $handle->allowed = array( 'image/*' );
                        if ($handle->uploaded) {
                            $handle->file_auto_rename = false;
                            $handle->file_overwrite = true;
                            $handle->file_new_name_body = $row->id;
                            $handle->image_resize = true;
                            $handle->image_ratio_crop = 1;
                            $handle->image_x = $params->get('userImageWidth', '120');
                            $handle->image_y = $params->get('userImageWidth', '120');
                            $handle->Process($savepath);
                            $handle->Clean();

                            if ($handle->file_dst_name != $row->image && JFile::exists($oldimage))
                                JFile::delete($oldimage);

                            $row->image = $handle->file_dst_name;
                            $updated = true;

                        } else {
                            $mainframe->enqueueMessage(JText::_('PLG_K2_FILMFANS_USER_AVATAR_UPLOAD_ERROR') . '<br />' .$handle->error, 'alert');
                        }

                    } else if ($aurl) {

                        if (JFile::exists($oldimage))
                            JFile::delete($oldimage);

                        $row->image = $aurl;
                        $updated = true;
                    }

                    if ($updated) {
                        $row->store();
                        //$mainframe->enqueueMessage(JText::_('PLG_K2_FILMFANS_USER_AVATAR_UPLOAD_SUCCESS'), 'message');
                    }

                    $mainframe->redirect($url);

                    break;

                case 'profile.cover':
                case 'profilecover':

                    $url = base64_decode($mainframe->input->get('return'));
                    if (!$url) $url = JUri::current();

                    $uid = $mainframe->input->getInt('uid');
                    $aurl = trim($mainframe->input->getString('coverurl'));
                    $adel = $mainframe->input->getInt('coverdel', 0);
                    $file = $mainframe->input->files->get('cover');
                    if (is_array($file) && !isset($file['error'])) $file = array_shift($file);

                    if ($uid != $user->get('id')) {
                        $mainframe->enqueueMessage('Access denied', 'alert');
                        $mainframe->redirect($url);
                    }

                    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
                    $row = JTable::getInstance('K2User', 'Table');

                    $db->setQuery("SELECT `id` FROM `#__k2_users` WHERE `userID` = {$uid}");
                    $k2id = $db->loadResult();

                    if (!$k2id) {
                        $mainframe->enqueueMessage('Access denied', 'alert');
                        $mainframe->redirect($url);
                    }

                    $row->load($k2id);

                    $row->plugins = is_array($row->plugins) ? $row->plugins : json_decode($row->plugins, true);

                    $coverimage = &$row->plugins['ffcover'];

                    $savepath = JPATH_ROOT . '/media/k2/covers/';
                    $oldimage = $savepath.DIRECTORY_SEPARATOR.$coverimage;
                    $updated = false;

                    if ($adel) {

                        if (JFile::exists($oldimage))
                            JFile::delete($oldimage);

                        $coverimage = '';
                        $updated = true;
                    }

                    if (!empty($file['name']) && $file['error'] == 0) {

                        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_k2' . DS . 'lib' . DS . 'class.upload.php');

                        $params = JComponentHelper::getParams('com_k2');

                        $handle = new Upload($file);
                        $handle->allowed = array( 'image/*' );
                        if ($handle->uploaded) {
                            $handle->file_auto_rename = false;
                            $handle->file_overwrite = true;
                            $handle->file_new_name_body = $row->id;
                            $handle->image_resize = false;
                            $handle->Process($savepath);
                            $handle->Clean();

                            if ($handle->file_dst_name != $coverimage && JFile::exists($oldimage))
                                JFile::delete($oldimage);

                            $coverimage = $handle->file_dst_name;
                            $updated = true;

                        } else {
                            $mainframe->enqueueMessage(JText::_('PLG_K2_FILMFANS_USER_COVER_UPLOAD_ERROR') . '<br />' .$handle->error, 'alert');
                        }

                    } else if ($aurl) {

                        if (JFile::exists($oldimage))
                            JFile::delete($oldimage);

                        $coverimage = $aurl;
                        $updated = true;
                    }

                    $row->plugins = json_encode($row->plugins);

                    if ($updated) {
                        $row->store();
                        //$mainframe->enqueueMessage(JText::_('PLG_K2_FILMFANS_USER_COVER_UPLOAD_SUCCESS'), 'message');
                    }

                    $mainframe->redirect($url);

                    break;

                case 'item.add.review':

                    $language = JFactory::getLanguage();

                    $user->get('id') or FilmFansHelper::exit_json(array('error' => JText::_('PLG_K2_FILMFANS_REVIEW_ERROR_AUTH')));
                    JSession::checkToken() or FilmFansHelper::exit_json(array('error' => JText::_('PLG_K2_FILMFANS_REVIEW_ERROR_TOKEN')));

                    $language->load('com_k2');
                    $language->load('com_k2', JPATH_ADMINISTRATOR);

                    $catid = FilmFansHelper::categoryGroups('review');
                    $catid = array_shift($catid);
                    $catalias = FilmFansHelper::categoryParams($catid);
                    $catalias = $catalias['alias'];

                    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
                    $row = JTable::getInstance('K2Item', 'Table');

                    $post = $mainframe->input->post;

                    $data = array(
                        'title' => '',
                        'catid' => $catid,
                        'published' => 0,
                        'introtext' => trim(FilmFansHelper::strip_tags($post->getString('review'))),
                        'created' => date('Y-m-d H:i:s'),
                        'created_by' => $user->get('id'),
                        'publish_up' => date('Y-m-d'),
                        'access' => 1,
                        'language' => '*',
                        'plugins' => array('ffvars' => array(
                            'ffrate' => $post->getString('rate'),
                            'ffreaction' => $post->getString('react'),
                            'ffeval' => $post->getInt('evaluation')
                        ), 'fflink' => $post->getInt('mainid'))
                    );

                    $errors = array();

                    if ($data['plugins']['fflink']) {
                        $q = "SELECT i.`id`, i.`title`, i.`alias`, c.`alias` AS `categoryalias`, c.`id` AS `catid` FROM `#__k2_items` AS i LEFT JOIN `#__k2_categories` AS c ON c.`id` = i.`catid` WHERE i.`id` = " . (int) $data['plugins']['fflink'];
                        $db->setQuery($q);
                        $mainitem = $db->loadObject('JObject');
                    }

                    if (empty($data['introtext'])) $errors[] = 'PLG_K2_FILMFANS_REVIEW_ERROR_TEXT';
                    if (empty($data['plugins']['fflink']) || empty($mainitem->id)) $errors[] = 'PLG_K2_FILMFANS_REVIEW_ERROR_REQUEST';
                    if (empty($data['plugins']['ffvars']['ffrate'])) $errors[] = 'PLG_K2_FILMFANS_REVIEW_ERROR_RATE';
                    if (empty($data['plugins']['ffvars']['ffreaction'])) $errors[] = 'PLG_K2_FILMFANS_REVIEW_ERROR_REACTION';
                    if (empty($data['plugins']['ffvars']['ffeval'])) $errors[] = 'PLG_K2_FILMFANS_REVIEW_ERROR_EVAL';

                    if (!empty($errors))
                        FilmFansHelper::exit_json(array('error' => implode(PHP_EOL, array_map('JText::_', $errors))));

                    $data['title'] = implode(' ', array($user->get('name'), '-', $mainitem->title));
                    $data['introtext'] = '<p>'.preg_replace('/[\n\r]+/mi', "</p><p>", $data['introtext']).'</p>';

                    $row->bind($data);

                    if (!$row->check()) {
                        FilmFansHelper::exit_json(array('error' => implode(PHP_EOL, array_map('JText::_', $row->getErrors()))));
                    }

                    if (!$row->store() || !$row->id)
                        FilmFansHelper::exit_json(array('error' => JText::_('PLG_K2_FILMFANS_REVIEW_ERROR_UKNOWN')));

                    $mainframe->triggerEvent('onAfterK2Save', array(&$row, true));

                    $link = FilmFansHelper::getLink($row->id, $row->alias, $catid, $catalias, $mainitem->id, $mainitem->alias, $mainitem->catid, $mainitem->categoryalias);

                    $msg = '<div class="review-success">
                        <h4>'.JText::_('PLG_K2_FILMFANS_REVIEW_SUCCESS').'</h4>
                    </div>';

                    FilmFansHelper::exit_json(array('ok' => 1, 'url' => $link,'message' => $msg));

                    break;

                case 'logout':

                    $url = @$_SERVER['HTTP_REFERER'];

                    if ($url) {

                        $router = $mainframe->getRouter();
                        $router->parse(JUri::getInstance($url));

                        $vars = $router->getVars();
                        if (!empty($vars['option']) && $vars['option'] == 'com_users')
                            $url = '';
                    }

                    if (!$url) $url = jUri::base();

                    $mainframe->logout();

                    $mainframe->redirect($url);

                    break;

                case 'tag':

                    $filter = $mainframe->input->getString('filter');
                    $exist = array_map('trim', $mainframe->input->get('exist', array(), 'array'));
                    $term = $db->escape(trim($mainframe->input->getString('term')));

                    $flags = FilmFansHelper::uniteFlags($filter) ;

                    $q = "SELECT `name` AS `value`, `name` AS `label`
                        FROM `#__k2_filmfans_tags`
                        WHERE MATCH (`name`) AGAINST ('*{$term}*' IN BOOLEAN MODE)
                        ".(!empty($flags) ? "AND `flag` IN (".implode(', ', $db->q($flags)).")" : '')."
                        ".(!empty($exist) ? "AND `name` NOT IN (".implode(', ', $db->q($exist)).")" : '')."
                        ORDER BY MATCH (`name`) AGAINST ('*{$term}*' IN BOOLEAN MODE)
                        LIMIT 10
                        ";

                    $db->setQuery($q);
                    $tags = $db->loadAssocList();

                    FilmFansHelper::ob_get_clean_all();
                    echo json_encode($tags);
                    $mainframe->close();
                    break;

                case 'item':

                    $filter = $mainframe->input->getString('filter', 'movie');
                    $term = $db->escape(trim($mainframe->input->getString('term')));

                    $params = FilmFansHelper::categoryParams();

                    foreach ($params as $k=>$v) if ($v['ffBehaviour'] != $filter || empty($v['ffParent'])) unset($params[$k]);

                    $items = array();

                    if (count($params)) {

                        $q = "SELECT `id` AS `value`, `title` AS `label`
                        FROM `#__k2_items`
                        WHERE `title` LIKE '{$term}%'
                            AND `catid` IN (" . implode(', ', $db->q(array_keys($params))) . ")
                        ORDER BY `title` ASC, `published` DESC
                        LIMIT 10
                        ";

                        $db->setQuery($q);
                        $items = $db->loadAssocList();

                        foreach ($items as $k=>$v) {
                            $items[$k]['label'] = $v['label']. " [#{$v['value']}]";
                            $items[$k]['image'] = JUri::root(true).'/media/k2/items/cache/'.md5("Image".$v['value']).'_XS.jpg';
                        }
                    }

                    FilmFansHelper::ob_get_clean_all();
                    echo json_encode($items);
                    $mainframe->close();
                    break;
            }
        }

        $router = $mainframe->getRouter();

        if ($mainframe->input->get('option') == 'com_users' && $mainframe->input->get('view') == 'profile')
            $mainframe->input->set('layout', 'edit');

        if ($mainframe->isAdmin() || $mainframe->input->get('option') != 'com_k2' || $router->getMode() != JROUTER_MODE_SEF) return;

        if (!$mainframe->input->getInt('mainid')) {

            $menu = $mainframe->getMenu();
            $roots = $menu->getItems(array( 'parent_id', 'component' ), array( 1, 'com_k2' ));

            $uri = JUri::getInstance(str_replace(JUri::base(), '', JUri::getInstance()));

            $path = explode('/', trim($uri->getPath(), '/'));

            $catch = null;
            $newpath = array();
            foreach ($roots as $item) {
                $_i = array_search($item->route, $path, true);
                if ($_i > 0) {
                    $catch = $item;
                    $newpath = array_slice($path, $_i);
                    break;
                }
            }

            if (!empty($catch)) {

                $uri = JUri::getInstance(trim(JUri::base(), '/') . '/' . implode('/', $newpath) . '/');

                $vars = $router->getVars();

                $result = $router->parse($uri);
                $result['mainid'] = $mainframe->input->getString('id');
                unset($result['Itemid']);

                foreach ($vars as $k => $v) if ($k != 'Itemid') {
                    $mainframe->input->def($k, null);
                    $mainframe->input->set($k, null);
                    //Legacy
                    JRequest::setVar($k, null);
                }

                foreach ($result as $key => $value) {
                    $mainframe->input->def($key, $value);
                    //Legacy
                    JRequest::setVar($key, $value);
                }
            }

            //FilmFansHelper::_sd($uri, $result, $vars, $roots, $mainframe->input);
        }

        //Shorten Tags url reroute
        if ($params->get('miTag') && $mainframe->input->getInt('Itemid') == $params->get('miTag')) {
            $tag = $mainframe->input->get('id');
            $mainframe->input->set('view', 'itemlist');
            $mainframe->input->set('task', 'tag');
            $mainframe->input->set('tag', $tag);
            $mainframe->input->set('id', null);

            //Legacy
            JRequest::setVar('view', 'itemlist');
            JRequest::setVar('task', 'tag');
            JRequest::setVar('tag', $tag);
            JRequest::setVar('id', null);
        }

        switch ($action) {

            case 'item.like':

                $uid = $user->get('id');

                $ret = $mainframe->input->getString('ret');
                $fflike = $mainframe->input->getInt('fflike', -99);
                $id = intval($mainframe->input->get('id'));

                if (!$uid) {
                    FilmFansHelper::exit_json(array( 'message' => JText::_('PLG_K2_FILMFANS_ACTION_NEED_LOGIN'), 'redirect' => FilmFansHelper::routeMenuItem('miLogin', 'index.php'.($ret ? '?ret='.$ret : '')) ));
                }

                $res = array('ok' => 1);

                if ($fflike != -99 && $id) {

                    $db->setQuery("SELECT `id` FROM `#__k2_items` WHERE `published` = 1 AND `trash` = 0 AND `id` = ".$id);
                    $id = (int) $db->loadResult();

                    if ($id) {

                        $db->setQuery("SELECT `like` - `dislike` FROM `#__k2_filmfans_likes` WHERE `item` = {$id} AND `user` = {$uid}");
                        $current = (int) $db->loadResult();

                        if ($fflike == 0 || $fflike == $current) {
                            $db->setQuery("DELETE FROM `#__k2_filmfans_likes` WHERE `item` = {$id} AND `user` = {$uid}");
                            $db->execute();
                        } else {
                            $set = '`stamp` = NOW(), `like` = ' . ($fflike > 0 ? 1 : 0) . ', `dislike` = ' . ($fflike < 0 ? 1 : 0);
                            $db->setQuery("INSERT INTO `#__k2_filmfans_likes` SET `item` = {$id}, `user` = {$uid}, {$set} ON DUPLICATE KEY UPDATE {$set}");
                            $db->execute();

                            FilmFansHelper::itemHit($id, 10);

                            FilmFansHelper::FBPost($fflike > 0 ? 'like' : 'dislike', $uid, $id);
                        }
                    }
                }

                if (!$id) FilmFansHelper::exit_json(array());

                $db->setQuery("SELECT `like` - `dislike` FROM `#__k2_filmfans_likes` WHERE `item` = {$id} AND `user` = {$uid}");
                $res['value'] = (int) $db->loadResult();


                $db->setQuery("SELECT SUM(`like`) AS `likes`, SUM(`dislike`) AS `dislikes` FROM `#__k2_filmfans_likes` WHERE `item` = {$id}");
                $_tmp = $db->loadRow();
                if (is_array($_tmp))
                    list($res['likes'], $res['dislikes']) = $_tmp;

                FilmFansHelper::exit_json($res);

                break;

            case 'item.react':

                $uid = $user->get('id');

                $ret = $mainframe->input->getString('ret');
                $ffreaction = $mainframe->input->getInt('ffreaction', 0);
                $id = intval($mainframe->input->get('id'));

                if (!$uid) {
                    FilmFansHelper::exit_json(array( 'message' => JText::_('PLG_K2_FILMFANS_ACTION_NEED_LOGIN'), 'redirect' => FilmFansHelper::routeMenuItem('miLogin', 'index.php'.($ret ? '?ret='.$ret : '')) ));
                }

                $res = array('ok' => 1);

                if ($ffreaction && $id) {

                    $db->setQuery("SELECT `id` FROM `#__k2_items` WHERE `published` = 1 AND `trash` = 0 AND `id` = ".$id);
                    $id = (int) $db->loadResult();

                    if ($id) {

                        $db->setQuery("SELECT 1 FROM `#__k2_filmfans_reactions` WHERE `item` = {$id} AND `user` = {$uid} AND `reaction` = {$ffreaction}");
                        $thesame = $db->loadResult();

                        if (!$thesame) {
                            $set = "`stamp` = NOW(), `reaction` = {$ffreaction}";
                            $db->setQuery("INSERT INTO `#__k2_filmfans_reactions` SET `item` = {$id}, `user` = {$uid}, {$set} ON DUPLICATE KEY UPDATE {$set}");
                            $db->execute();

                            FilmFansHelper::itemHit($id, 10);

                            FilmFansHelper::FBPost('reaction', $uid, $id, array('reaction' => $ffreaction));
                        }
                    }
                }

                if (!$id) FilmFansHelper::exit_json(array());

                $db->setQuery("SELECT `reaction` FROM `#__k2_filmfans_reactions` WHERE `item` = {$id} AND `user` = {$uid}");
                $res['reaction'] = (int) $db->loadResult();

                FilmFansHelper::exit_json($res);

                break;
        }

    }

	function onRenderAdminForm(&$item, $type, $tab = '') {

        $mainframe = JFactory::getApplication();

        if ($mainframe->isAdmin()) {

            $action = $mainframe->input->get('ffaction');

            if ($action) {

                switch ($type) {

                    case 'item':

                        FilmFansHelper::ob_get_clean_all();

                        $this->_ItemForm($item, $mainframe->input->getInt('catid'));

                        $mainframe->close();
                        break;
                }
            }
        }

        if ($type == 'item' && $tab == 'content') {

            JHtml::_('script', 'media/filmfans/js/admin.js');
            JHtml::_('script', 'media/filmfans/js/tagit.js');
            JHtml::_('script', 'media/filmfans/js/afflinks.js');
            JHtml::_('script', 'system/html5fallback.js', false, true);
            JHtml::_('stylesheet', 'media/filmfans/css/admin.css');
            JHtml::_('stylesheet', 'media/filmfans/css/tagit.css');

            FilmFansHelper::addChosen();

            JFactory::getDocument()->addScriptDeclaration('
                jQuery(function() {
                    jQuery.filmFansAdmin();
                });
            ');

            return null;

        } else if ($type == 'category') {

            return $this-> _CategoryForm($item);
        }

        return null;
	}

    function onK2BeforeSetQueryLimit(&$limit) {
        $mainframe = JFactory::getApplication();
        $limit = $mainframe->input->getInt('fflimit', $limit);

        // to hack \com_k2\views\itemlist\view.html.php (before JRequest::setVar('limit', $limit)) <= $dispatcher->trigger('onK2BeforeSetQueryLimit', array(&$limit, &$limitstart)); //HACK [entire row] added due to unavailablity to set limit from outside
    }

    function onK2BeforeSetQuery(&$query) {

        static $ids, $terms;

        $mainframe = JFactory::getApplication();
        $menu = $mainframe->getMenu();
        $db = JFactory::getDBO();

        if ($mainframe->isAdmin()) return false;

        $maps = array(
            'allwhere' => '/WHERE.*ORDER/mi',
            'where' => '/WHERE /mi',
            'order' => '/ORDER BY.*$/mi',
            'allorder' => '/ORDER\s+BY.*$/mi',
            'select' => '/^SELECT /mi',
            'allselect' => '/^SELECT.*FROM/mi',
            'cat' => '/AND\s*c[`]*\.id[`]*\s*IN\s*\(([^\)]+)\)/mi',
            'tagx' => '/i.id\s+IN\s+\(\s*SELECT[^)]+\)/mi',
            //TODO issue to replace
            //(SELECT itemID FROM jff_k2_tags_xref INNER JOIN `jff_k2_tags_xref` AS tx ON tx.`itemID` = i.`id` WHERE tx.`tagID` = 2 AND c.`id` IN ('1') AND tagID=2)
            'cid' => '/c.id\s+IN\s+\([^)]+\)/mi',
            'count' => '/COUNT\(.*\*..*\)/mi',
            'user' => "/i.created_by\s*=\s*([0-9]*)\s*AND\s*i.created_by_alias\s*=\s*\'(\\.|[^\'])*\'/mi",
        );

        $total = preg_match($maps['count'], $query);

        $_cats = FilmFansHelper::categoryGroups();

        $tag = strtoupper($mainframe->input->getString('tag'));
        $uid = 0;

        if (preg_match( $maps['cat'], $query, $matches))
            $cats = $matches[1];

        $ffpopular = $mainframe->input->getInt('ffpopular', 0);
        $fffeatured = $mainframe->input->getInt('fffeatured', $menu->getActive() == $menu->getDefault());

        $ffcat = $mainframe->input->getString('ffcat', -1);
        $fftag = $mainframe->input->getInt('fftag', -1);
        $fford = $mainframe->input->getString('fford');

        $ffexclude = (array) $mainframe->input->getRaw('ffexclude');
        jimport('joomla.utilities.arrayhelper');
        JArrayHelper::toInteger($ffexclude);

        $mainid = explode(':', $mainframe->input->getString('mainid'));
        $mainid = (int) array_shift($mainid);
        $term = $mainframe->input->getString('term');
        $service = $mainframe->input->getString('service');

        if ($fftag == -1 && !empty($tag)) {
            $tags = array_flip(array_map('strtoupper', FilmFansHelper::getTags()));
            $fftag = isset($tags[$tag]) ? $tags[$tag] : 0;
        }

        $replaces = array();

        $replaces[] = array( $maps['tagx'], '1' );
        $replaces[] = array( $maps['allorder'], '' );
        $replaces[] = array( $maps['cid'], '1' );

        if ($fftag > 0) {
            $replaces['jointag'] = array( $maps['where'], 'INNER JOIN `#__k2_tags_xref` AS tx ON tx.`itemID` = i.`id` WHERE ' );
            $replaces[] = array( $maps['where'], 'WHERE tx.`tagID` = ' . $fftag . ' AND ' );
        }

        if (!empty($term) && $service == 'filmfans') {

            if (!isset($ids)) {

                $ids = array();
                if ($service == 'filmfans') {

                    require_once('models/search.php');

                    $mainframe->input->request->set('q', $term);
                    $mainframe->input->request->set('t', array(FFFinderModelSearch::_getTaxonomy('K2 Item')));

                    $model = new FFFinderModelSearch();
                    $model->getState();

                    $items = $model->getResults();

                    if ($items) {
                        foreach ($items as $item) {
                            parse_str($item->url, $_data);
                            if ($_data['view'] == 'item' && $_data['id'] > 0)
                                $ids[] = (int) $_data['id'];
                        }
                    }

                    $terms = $model->getTerms();

                    //FilmFansHelper::_sd($model, $items, $db->getLog());
                }

                if (empty($ids)) {

                    $_where = array();
                    foreach ($terms['required'] as $t) {
                        $t = $db->q('%'.$db->escape($t, true).'%');
                        $_where[] = "(`title` LIKE $t OR `plugins` LIKE $t)";
                    }

                    if (count($_where)) {
                        $q = 'SELECT `id` FROM `#__k2_items` WHERE '.implode(' AND ', $_where).' LIMIT 1000';
                        $db->setQuery($q);
                        $ids = $db->loadAssocList('id', 'id');
                    }
                }
            }

                if (!empty($ids)) {
                $_tmp = implode(', ', $db->q($ids));
                $replaces[] = array( $maps['where'], "WHERE i.`id` IN ($_tmp) AND " );
            } else
                $replaces[] = array( $maps['where'], 'WHERE 0 AND ' );
        }

        if (preg_match($maps['user'], $query, $matches)) {

            $uid = intval($matches[1]);

            if (!$uid) $uid = JFactory::getUser()->get('id');

            if ($uid) {
                $replaces[] = array($maps['where'], "LEFT JOIN `#__k2_filmfans_hits` AS hits ON hits.`item` = i.`id` AND hits.`user` = {$uid} AND hits.`weight` >= 5 WHERE " );
                $replaces[] = array($maps['user'], " ( i.`created_by` = $uid OR hits.`weight` > 0) ");
            }
        }

        $replaces[] = array($maps['allselect'], 'SELECT i.`id` FROM');

        $q = $query;
        if (count($replaces)) {
            foreach ($replaces as $v)
                $q = preg_replace($v[0], $v[1], $q);
        }

        /*
        $db->setQuery($q);
        $items_ids = $db->loadAssocList('id', 'id');
        if (!empty($items_ids))
            $items_ids = implode(', ', $db->q($items_ids));
        else
            $items_ids = 0;
        */

        $items_ids = $q;
        $replaces = array();

        $_tmp = 'i.`id` IN (#!#!#)';

        if (!$uid && !$ffpopular) {
            $_tmp = '( i.`id` IN (#!#!#) OR main.`mainID` IN (#!#!#) )';

            if (!empty($mainid))
                $_tmp = "i.`id` IN (#!#!#) AND main.`mainID` = $mainid";
        }

        $gropby = !$total ? 'GROUP BY i.`id`' : '';
        $replaces['all'] = array($maps['allwhere'], " LEFT JOIN `#__k2_filmfans_link` AS `main` ON main.`itemID` = i.`id` WHERE $_tmp $gropby ORDER");

        if (isset($_cats[$ffcat])) {
            $replaces[] = array( $maps['where'], 'WHERE i.`catid` IN (' . implode(', ', $db->q($_cats[$ffcat])) . ') AND ' );
        } else if (!empty($cats)) {
            $replaces[] = array( $maps['where'], "WHERE i.catid IN ($cats) AND " );
        }

        if (!empty($ffexclude)) {
            $replaces[] = array( $maps['where'], "WHERE i.`id` NOT IN (".implode(', ', $db->q($ffexclude)).") AND " );
        }

        if ($uid) {
            $replaces[] = array( $maps['where'], "LEFT JOIN `#__k2_filmfans_hits` AS hits ON hits.`item` = i.`id` AND hits.`user` = {$uid} WHERE " );
        }

        if (!$total && (!empty($fford) || $uid || $fffeatured || $ffpopular)) {

            $_tmp = array();

            switch ($fford) {
                case 'az':
                    $_tmp[] = 'i.`title` ASC';
                    break;
                case 'za':
                    $_tmp[] = 'i.`title` DESC';
                    break;
                case 'review':
                    $replaces['joinlinkr'] = array( $maps['where'], 'LEFT JOIN (SELECT COUNT(*) AS `count`, ffl.`itemID` FROM `#__k2_filmfans_link` AS ffl WHERE `flag` = '.FilmFansHelper::$behaviour['review'].' GROUP BY `itemID`) AS fflr ON fflr.`itemID` = i.`id` WHERE ' );
                    $_tmp[] = 'fflr.`count` DESC';
                    break;
                case 'share':
                    //TODO
                    //break
                case 'hits':
                    $_tmp[] = 'i.`hits` DESC';
                    $_tmp[] = 'i.`title` ASC';
                    break;
                case 'rate':
                    if (strpos($query, '#__k2_rating') === false) {
                        $replaces[] = array( $maps['select'], "SELECT (r.`rating_sum`/r.`rating_count`) AS `rating`, ");
                        $replaces[] = array( $maps['where'], "LEFT JOIN `#__k2_rating` AS r ON r.`itemID` = i.`id` WHERE ");
                    }

                    $_tmp[] = '`rating` DESC';
                    break;
            }

            if ($uid)
                $_tmp[] = 'MAX(hits.`stamp`) DESC, i.`id` DESC';
            else if ($ffpopular)
                $_tmp[] = 'i.`ffpopularity` DESC, i.`id` DESC';
            else if ($fffeatured)
                $_tmp[] = 'i.`featured` DESC, i.`id` DESC';
            else
                $_tmp[] = 'i.`id` DESC';

            if (!empty($_tmp)) {

                $_tmp = implode(', ', $_tmp);

                if (stripos($query, 'ORDER BY') !== false)
                    $replaces[] = array( $maps['order'], "ORDER BY $_tmp" );
                else
                    $query .= " ORDER BY $_tmp";
            }
        }

        if ($total)
            $query = "SELECT COUNT(DISTINCT i.`id`) FROM `#__k2_items` AS i WHERE 1 ORDER###";

        if (count($replaces)) {
            foreach ($replaces as $v)
                $query = preg_replace($v[0], $v[1], $query);
        }

        if ($total)
            $query = str_replace('ORDER###', '', $query);

        $query = str_replace('#!#!#', $items_ids, $query);

        //echo str_replace('#__', 'jff_', $query);

        //FilmFansHelper::_sd($replaces, $q, str_replace('#__', 'jff_', $query));

        return true;
    }

    function onAfterRender() {

        $mainframe = JFactory::getApplication();

        if ($mainframe->isAdmin() || !$mainframe->input->getBool('ffajax')) return false;

        FilmFansHelper::ob_get_clean_all();

        $body = jFactory::getDocument()->getBuffer();

        reset($body['component']);

        echo '<div id="ffajax">'.implode(current($body['component'])).'</div>';

        $mainframe->close();
    }

    private function _ItemForm(&$item, $catid) {

        if (!empty($catid)) {

            $params = FilmFansHelper::categoryParams($catid);

            if (empty($params['ffBehaviour']) || !$this->_form_output($item, $params))
                echo '<span class="ff-message">' . JText::_('PLG_K2_FILMFANS_ADMIN_NO_OPTIONS') . '</span>';

        } else
            echo '<span class="ff-message">' . JText::_('PLG_K2_FILMFANS_ADMIN_CHOOSE_CATEGORY') . '</span>';

        return null;
    }

    private function _form_output(&$item, $params) {

        jimport('joomla.form.form');
        $xml = __DIR__.'/models/item-'.$params['ffBehaviour'].'.xml';

        if (!file_exists($xml)) return false;

        $form = JForm::getInstance('itemFilmFans', $xml);

        $values = json_decode($item->plugins, true);
        $form->bind(array('plugins' => $values));

        ?>

        <h3><?php echo JText::_($form->getAttribute('title')); ?></h3>

        <table class="adminFormK2 table">
            <tbody>
        <?php foreach ($form->getFieldset('item-filmfans-layout') as $field) { ?>
                <tr>
                <?php if ($field->type == 'header') { ?>
                    <td colspan="2"><?php echo $field->input; ?></td>
                <?php } else if (strtolower($field->type) == 'spacer') { ?>
                    <td colspan="2">&nbsp;</td>
                <?php } else { ?>
                    <td class="adminK2LeftCol"><label for="name"><?php echo $field->label; ?></label></td>
                    <td class="adminK2RightCol"><?php echo $field->input; ?></td>
                <?php } ?>
                </tr>
        <?php } ?>
            </tbody>
        </table>

        <?php

        return true;
    }

    private function _CategoryForm(&$item) {

        JHtml::_('stylesheet', 'media/filmfans/css/admin.css');

        $plugin = new stdClass;
        $plugin->name = '';

        jimport('joomla.form.form');

        $form = JForm::getInstance('categoryFilmFans', __DIR__.'/models/category.xml');

        $values = array('params' => json_decode($item->params, true));
        $form->bind($values);

        ob_start();

        ?>
        <div id="filmFansAdmin" class="filmFansAdmin"><div class="filmFansAdmin-wrap"><div class="filmFansAdmin-container">
            <table class="adminFormK2 table">
                <tbody>
            <?php foreach ($form->getFieldsets() as $name => $fieldSet) { ?>
                <?php foreach ($form->getFieldset($name) as $field) { ?>
                    <tr>
                    <?php if ($field->type == 'header') { ?>
                        <td colspan="2"><?php echo $field->input; ?></td>
                    <?php } else if (strtolower($field->type) == 'spacer') { ?>
                        <td colspan="2">&nbsp;</td>
                    <?php } else { ?>
                        <td class="adminK2LeftCol"><label for="name"><?php echo $field->label; ?></label></td>
                        <td class="adminK2RightCol"><?php echo $field->input; ?></td>
                    <?php } ?>
                    </tr>
                <?php } ?>
            <?php } ?>
                </tbody>
            </table>
        </div></div></div>

        <?php

        $plugin->fields = ob_get_clean();
        return $plugin;
    }

    function onBeforeCompileHead() {

        $mainframe = JFactory::getApplication();

        if ($mainframe->isAdmin()) return;

        FilmFansHelper::disableAssets(array(
            'media/jui/js/jquery-migrate',
            'media/jui/js/jquery-noconflict',
            'media/jui/js/bootstrap',
            'media/jui/css/bootstrap',
            'media/system/js/frontediting.js',
            '/media/system/js/modal',
        ));
    }
}
