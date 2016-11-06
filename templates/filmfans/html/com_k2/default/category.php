<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();

if ($mainframe->input->getString('service', 'filmfans') != 'filmfans') {
    require('search.php');
    return true;
}

$this->fftmpl = $mainframe->input->get('fftmpl', isset($this->fftmpl) ? $this->fftmpl : '');

$mainid = (int) $mainframe->input->getString('mainid');

if ($mainid) {
    $this->item = FilmFansHelper::getItem($mainid);;
    $mainid = $this->item->id;
    if (!$mainid)
        throw new Exception(JText::_('K2_ITEM_NOT_FOUND'), 404);
}


$ids = array();
$parents = array();

if (!empty($this->leading))
    foreach($this->leading as $k=>$item) {
        $this->leading[$k]->plugins = json_decode($item->plugins, true);
        $ids[] = $item->id;
        if (!empty($item->plugins['fflink']))
            $parents[$k] = (int) $item->plugins['fflink'];
    }

if (!empty($parents)) {

    $db = JFactory::getDBO();

    $q = "SELECT i.`id`, i.`title`, i.`introtext`, i.`alias`, c.`alias` AS `categoryalias`, c.`id` AS `catid` FROM `#__k2_items` AS i LEFT JOIN `#__k2_categories` AS c ON c.`id` = i.`catid` WHERE i.`id` IN (" . implode(',', $parents) . ")";
    $db->setQuery($q);
    $this->_mainitems = $db->loadObjectList('id', 'JObject');

    foreach ($parents as $k=>$v) if (isset($this->_mainitems[$v])) {
        $this->leading[$k]->mainitem = &$this->_mainitems[$v];
        $this->leading[$k]->link = FilmFansHelper::getLink($this->leading[$k]->id, $this->leading[$k]->alias, $this->leading[$k]->catid, $this->leading[$k]->categoryalias, $this->_mainitems[$v]->id, $this->_mainitems[$v]->alias, $this->_mainitems[$v]->catid, $this->_mainitems[$v]->categoryalias);
    }
}

if (!empty($ids)) {
    $db = JFactory::getDBO();
    $query = "SELECT * FROM `#__k2_rating` WHERE `itemID` IN (" . implode(',', $ids) . ")";
    $db->setQuery($query);
    $this->_votes = $db->loadObjectList('itemID');

    foreach ($this->leading as $k=>$v) {
        $this->leading[$k]->numOfvotes = $_rcount = isset($this->_votes[$v->id]) ? intval($this->_votes[$v->id]->rating_count) : 0;
        $this->leading[$k]->_rate = isset($this->_votes[$v->id]) ? number_format(intval($this->_votes[$v->id]->rating_sum) / $_rcount, 2) : 0;
        unset($_rcount);
    }

    FilmFansHelper::FBInit();

    if ($this->fftmpl == 'feed' && defined('FACEBOOK_SDK_V4_SRC_DIR')) {

        $fbsession = FilmFansHelper::FBAppSession();

        $fbbatch = $fbbatch_map = array();

        foreach ($this->leading as $k => $v) {
            $this->leading[$k]->fulllink = trim(JUri::base(), '/') . $v->link;

            $fbbatch[] = array( 'method' => 'GET', 'relative_url' => '/' . urlencode($this->leading[$k]->fulllink) . '/' );
            $fbbatch_map[$this->leading[$k]->fulllink] = $k;

            $this->leading[$k]->numOfvotes = $_rcount = isset($this->_votes[$v->id]) ? intval($this->_votes[$v->id]->rating_count) : 0;
            $this->leading[$k]->_rate = isset($this->_votes[$v->id]) ? number_format(intval($this->_votes[$v->id]->rating_sum) / $_rcount, 2) : 0;
            unset($_rcount);
        }

        try {

            if ($fbsession !== false) {
                $request = new Facebook\FacebookRequest($fbsession, 'POST', '?batch=' . json_encode($fbbatch));
                $response = $request->execute();
                $graphObject = $response->getGraphObject();

                $newbatch = $newbatch_map = array();
                foreach ($graphObject->asArray() as $v) if ($v->code == 200) {
                    $obj = json_decode($v->body);
                    if (!isset($obj->og_object)) continue;
                    if (!isset($fbbatch_map[$obj->og_object->url])) continue;
                    $id = $fbbatch_map[$obj->og_object->url];
                    $newbatch[] = array( 'method' => 'GET', 'relative_url' => "/{$obj->og_object->id}/comments?limit=3" );
                    $this->leading[$id]->share = $obj->share;
                    $newbatch_map[$obj->og_object->id] = $id;
                }

                $request = new Facebook\FacebookRequest($fbsession, 'POST', '?batch=' . json_encode($newbatch));
                $response = $request->execute();
                $graphObject = $response->getGraphObject();

                foreach ($graphObject->asArray() as $v) if ($v->code == 200) {
                    $obj = json_decode($v->body);
                    if (empty($obj->data)) continue;
                    foreach ($obj->data as $comment) {
                        $id = explode('_', $comment->id);
                        $id = array_shift($id);
                        if (!isset($newbatch_map[$id])) continue;
                        $id = $newbatch_map[$id];
                        if (!isset($this->leading[$id]->comments))
                            $this->leading[$id]->comments = array();
                        $this->leading[$id]->comments[] = $comment;
                    }
                }
            }

        } catch (Facebook\FacebookRequestException $e) {

        }
    }
}

FilmFansHelper::prepareFilter();

$this->pagination->setAdditionalUrlParam('ffcat', $mainframe->input->getString('ffcat'));
$this->pagination->setAdditionalUrlParam('fftag', $mainframe->input->getInt('fftag'));
$this->pagination->setAdditionalUrlParam('fford', $mainframe->input->getString('fford'));
$this->pagination->setAdditionalUrlParam('fffeatured', $mainframe->input->getInt('fffeatured'));

$pages = str_replace(str_replace('sss=www', '', JRoute::_('&sss=www')), JUri::current().'?', $this->pagination->getPagesLinks());

$lang_more = explode('|', JText::_('PLG_K2_FILMFANS_CATEGORY_MORE'));


$menu = $mainframe->getMenu();
$is_front = $menu->getActive() == $menu->getDefault();

?>

<div class="row ffcontainer">
    <div class="col-lg-12 col-md-12 col-sm-12">

        <?php

        if (!$mainframe->input->get('ffajax') && empty($this->ffnofiterform)) {

            if (!$mainid) {

                $modules = JModuleHelper::getModules("fffilter");

                $attribs = array();
                $attribs['style'] = 'raw';

                $menu = $mainframe->getMenu();

                foreach ($modules as $mod) {
                    $mod->title = $is_front ? JText::_('PLG_K2_FILMFANS_FEATURED') : '';
                    $mod->params = json_decode($mod->params, true);
                    $mod->params['fffeatured'] = 1;
                    $mod->params = json_encode($mod->params);
                    echo JModuleHelper::renderModule($mod, $attribs);
                }

            } else if (!empty($this->item)) {
                echo '<div class="row item-title">';
                include('item_title.php');
                echo '</div>';
            }
        }

        ?>

        <div class="ffitems-wrap">

            <div class="ffloader"></div>

            <div class="row ffitems ffresult">
            <?php if (!empty($this->leading)) { ?>
                    <?php foreach($this->leading as $key=>$item) { ?>
                    <div class="ffitem">
                    <?php
                        $this->item = &$this->leading[$key];
                        echo $this->loadTemplate('item'.($this->fftmpl ? '_'.$this->fftmpl : ''));
                    ?>
                    </div>
                    <?php } ?>
            <?php } else { ?>
                <div class="ffwarning ffwarning-narrow">
                    <a href="javascript:void(0)" class="ffwarning-close"><span class="glyphicon glyphicon-remove"></span></a>
                    <div class="row">
                        <div class="ufe1 col-md-8 col-lg-8">
                            <h2><?php echo JText::_('PLG_K2_FILMFANS_CATEGORY_EMPTY'); ?></h2>
                        </div>
                        <div class="ufe2 col-md-4 col-lg-4">
                            <?php echo JText::sprintf('PLG_K2_FILMFANS_USER_FEED_EMPTY2', FilmFansHelper::routeMenuItem('miFAQ')); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div>

        </div>

        <div class="ffpagination" style="display: none;">
            <?php echo $pages; ?>
        </div>

        <div class="ffmore"<?php if ($this->pagination->pagesTotal <= $this->pagination->pagesCurrent) echo ' style="display: none;"'; ?>>
            <a class="ffmore-do" href="javascript:void(0)">
               <span class="more-top"><?php echo $lang_more[0]; ?></span>
               <span class="more-bot"><?php echo @$lang_more[1]; ?><br /><span><?php echo @$lang_more[2]; ?></span></span>
            </a><a class="ffmore-loading">
               <span class="more-top"></span>
               <span class="more-bot"></span>
            </a>
        </div>

    </div>
</div>

<?php if ($is_front && !$mainframe->input->get('ffajax')) { ?>
<div class="row ffcontainer">
    <div class="col-lg-12 col-md-12 col-sm-12">

        <?php

        $modules = JModuleHelper::getModules("fffilter");

        $attribs = array();
        $attribs['style'] = 'raw';

        foreach ($modules as $mod) {
            $mod->title = JText::_('PLG_K2_FILMFANS_POPULAR');
            $mod->params = json_decode($mod->params, true);
            $mod->params['ffpopular'] = 1;
            $mod->params['fftmpl'] = 'feed';
            $mod->params = json_encode($mod->params);
            echo JModuleHelper::renderModule($mod, $attribs);
        }

        ?>

        <div class="ffitems-wrap">

            <div class="ffloader"></div>

            <div class="row ffitems ffresult">
            </div>

        </div>

        <div class="ffpagination" style="display: none;" data-ajax="<?php echo JUri::current(); ?>">

        </div>

        <div class="ffmore" style="display: none;">
            <a class="ffmore-do" href="javascript:void(0)">
               <span class="more-top"><?php echo $lang_more[0]; ?></span>
               <span class="more-bot"><?php echo @$lang_more[1]; ?><br /><span><?php echo @$lang_more[2]; ?></span></span>
            </a><a class="ffmore-loading">
               <span class="more-top"></span>
               <span class="more-bot"></span>
            </a>
        </div>

    </div>
</div>

<?php } ?>