<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();
$user = JFactory::getUser();
$uid = $user->get('id');

FilmFansHelper::prepareItem($this->item);

//FilmFansHelper::_sd($this->item);

$mainid = (int) $mainframe->input->getString('mainid');

if ($mainid && $mainid != $this->item->mainid)
    throw new Exception(JText::_('K2_ITEM_NOT_FOUND'), 404);

if (!$this->item->mainid) {
    $this->item->mainitem = &$this->item;
    $mainframe->input->set('~mainid', $this->item->id);
} else {
    $this->item->mainitem = FilmFansHelper::getItem($this->item->mainid);
    $mainframe->input->set('~behaviour', $this->item->behaviour);
}

$pglobal = FilmFansHelper::params();
$_mi = (int) $pglobal->get('miSearch');
$search_url = $_mi ? 'index.php?Itemid='.$_mi.'&service=filmfans&term=' : 'index.php?service=filmfans&term=';

$fulllink = trim(JUri::base(), '/').$this->item->link;

$data = &$this->item->plugins;

FilmFansHelper::itemHit($this->item->id, 2);
if ($this->item->mainid) FilmFansHelper::itemHit($this->item->mainid, 1);

?>
<a name="skip-header"></a>
<div class="row item-title">

    <?php include('item_title.php'); ?>

    <div class="tools">
        <div class="hist">
            <span class="hightlight"><?php echo $this->item->hits; ?></span> <?php echo JText::_('PLG_K2_FILMFANS_TOOLS_VIEWS'); ?>
        </div>
        <div class="likes">
            <?php
                $uri_like = clone JUri::getInstance();
                $uri_like->setVar('ffaction', 'item.like');
                $uri_like->setVar('fflike', 1);
                $uri_like->setVar('ret', base64_encode(JUri::current()));
                $uri_dislike = clone $uri_like;
                $uri_dislike->setVar('fflike', -1);
            ?>
            <a class="like<?php if ($this->item->userlike > 0) echo ' active'; ?>" title="<?php echo JText::_('PLG_K2_FILMFANS_LIKE'); ?>" data-ajax="<?php echo $uri_like; ?>"><i></i><span><?php echo (int) $this->item->likes; ?></span></a>
            <a class="dislike<?php if ($this->item->userlike < 0) echo ' active'; ?>" title="<?php echo JText::_('PLG_K2_FILMFANS_DISLIKE'); ?>" data-ajax="<?php echo $uri_dislike; ?>"><i></i><span><?php echo (int) $this->item->dislikes; ?></span></a>
        </div>
        <div class="social">
            <?php echo JText::_('PLG_K2_FILMFANS_TOOLS_SHARE'); ?> <?php echo implode(' ', FilmFansHelper::social($fulllink)); ?>
        </div>
    </div>
</div>

<?php switch ($this->item->behaviour) {
        default:

            $affiliate_links = FilmFansHelper::getVal('ffvars/affiliate-links', $data, array());

            $_sections = array();
            if (is_array($affiliate_links))
	            foreach ($affiliate_links as $v) {
		            if (!empty($v['s']))
			            $_sections[$v['s']][] = $v;
	            }


            JHtml::_('script', 'media/filmfans/js/jquery.dotdotdot.min.js');

            $db = JFactory::getDBO();
            $q = $db->getQuery(true)
                ->select('i.`id`, i.`title`, i.`video`, i.`video` AS `text`, r.`rating_sum` / r.`rating_count` AS `rating`')
                ->from('`#__k2_items` AS i')
                ->leftJoin('`#__k2_rating` AS r ON r.`itemID` = i.`id`')
                ->innerJoin('`#__k2_filmfans_link` AS m ON m.`itemID` = i.`id`')
                ->where('i.`catid` IN ('.implode(', ', $db->q(FilmFansHelper::categoryGroups('trailer'))).')')
                ->where('m.`mainID` = '.(int) $this->item->id)
                ->where('i.`video` <> ""')
                ->where('i.`published` > 0')
                ->order('i.`featured` DESC, i.`id` DESC');
            $db->setQuery($q);
            $videos = $db->loadObjectList('id');

            $vcurrent = 0;

            $params = new JRegistry();
            $params->set('autoplay', 1);
            foreach ($videos as $k=>$v) {
                if (!$vcurrent) {
                    $vcurrent = $k;
                    $params2 = new JRegistry();
                    $mainframe->triggerEvent('onContentPrepare', array('com_k2.item', &$videos[$k], &$params2, 0));
                    $videos[$k]->firsttext = $videos[$k]->text;
                    $videos[$k]->text = $videos[$k]->video;
                }

                $mainframe->triggerEvent('onContentPrepare', array('com_k2.item', &$videos[$k], &$params, 0));
                $videos[$k]->text =  preg_replace('/\s+/mi', ' ', preg_replace('/<!--(.*?)-->/mi', '', $videos[$k]->text));
                $videos[$k]->image = FilmFansHelper::getThumb('', $videos[$k]->video, 'xs', $v->id);
            }
?>

<?php if (count($videos)) {
?>
<script type="text/javascript">
/*<![CDATA[*/
    jQuery(function($){

        var dest = $('#vldestination');

        $(document).on('click', '.vl-item', function() {
            var that = jQuery(this);

            if (that.hasClass('active')) return;

            var data = that.data('vlitem');

            if (data.html) {

                that.offsetParent().find('.active').removeClass('active');
                that.addClass('active');
                dest.html(data.html);
            }
        });
    });
/*]]>*/
</script>
<div class="row container-video" id="vldestination">
    <?php echo $videos[$vcurrent]->firsttext; ?>
</div>
<div class="row item-videolist">
    <div class="videolist ffhscroll">
        <div class="vl-container"><!--
        <?php foreach ($videos as $k=>$v) { ?>
            --><a href="javascript:void(0)" class="vl-item<?php if ($k == $vcurrent) echo ' active'; ?>" data-vlitem='<?php echo json_encode(array('html' => $v->text)); ?>'>
                <span class="vl-image"><img src="<?php echo $v->image; ?>" alt="" /></span>
                <span class="vl-title"><?php echo $v->title; ?></span>
                <span class="ffrate-wrap">
                    <span class="ffrate"><span class="ffrate-active" style="width: <?php echo $v->rating*20; ?>%;"></span></span>
                </span>
            </a><!--
        <?php } ?>
        --></div>
        <a class="vl-next"><span aria-hidden="true" class="glyphicon glyphicon-chevron-right"></span></a>
        <a class="vl-prev"><span aria-hidden="true" class="glyphicon glyphicon-chevron-left"></span></a>
    </div>
</div>
<?php } ?>
<div class="row">
    <div class="item-content container-xs-height container-xs-desk">
        <div class="item-content row-xs-height">
            <div class="content col-md-8 col-xs-height">
                <div class="about">

                    <span class="image">
                        <img src="<?php echo $this->item->imageXSmall; ?>" alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>" />

                        <?php if (!empty($_sections['watch-button'])) { /*onclick="bootbox.alert('<?php echo JText::_('PLG_K2_FILMFANS_NOFUNC'); ?>')" */ ?>
                        <a class="movie-watch" href="<?php echo $_sections['watch-button'][0]['l']; ?>" ><?php echo JText::_('PLG_K2_FILMFANS_MOVIE_WATCH_MOVIE'); ?></a>
                        <?php } ?>
                    </span>

                    <div class="description">
                        <h4>STORYLINE</h4>
                        <div class="story"><?php echo $this->item->story; ?></div>
                        <?php foreach (array('director', 'writer', 'stars', 'cast') as $st) if (!empty($this->item->plugins['fftags'][$st])) { ?>
                        <div class="people">
                            <span class="ilabel"><?php echo JText::_('PLG_K2_FILMFANS_MOVIE_'.$st); ?>:</span>
                            <?php
                                $_tmp = array();
                                foreach ($this->item->plugins['fftags'][$st] as $v)
                                    $_tmp[] = '<a href="'.JRoute::_($search_url.urlencode($v)).'">'.$v.'</a>';
                                echo implode(', ', $_tmp)
                            ?>
                        </div>
                        <?php } ?>
                        <div class="tags">
                            <?php foreach ($this->item->tags as $tag) { ?>
                            <a class="tag" href="<?php echo str_replace('/tag/', '/', JRoute::_('index.php?Itemid=131&option=com_k2&view=itemlist&task=tag&tag='.urlencode(strtolower($tag->name)))); ?>"><?php echo $tag->name; ?></a>
                            <?php } ?>
                        </div>

                        <div class="clearfix"></div>

                        <?php $pg = trim(FilmFansHelper::getVal('ffvars/mppa', $data)); ?>
                        <?php if ($pg) { ?>
                        <div class="movie-mppa">
                            <div>
                                <b><?php echo $pg; ?></b>
                                <span><?php echo JText::_('PLG_K2_FILMFANS_MOVIE_MPPA_'.str_replace('-', '_', $pg)); ?></span>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <?php } ?>

                    </div>

                    <div class="clearfix"></div>

                </div>

            </div>
            <div class="content col-md-4 col-xs-height">
                <?php if (!empty($affiliate_links)) { ?>
                <div class="movie-actions">
                    <?php if (!empty($_sections['tickets'])) { ?>
                    <div class="btn-group">
	                    <?php if (count($_sections['tickets']) > 1) { ?>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <?php echo JText::_('PLG_K2_FILMFANS_MOVIE_BUY_TICKETS'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li class="dd-arrow"></li>
                            <?php foreach ($_sections['tickets'] as $v) echo FilmFansHelper::movieAfflink($v); ?>
                        </ul>
	                    <?php } else {
		                    reset($_sections['tickets']);
		                    $_tmp = current($_sections['tickets']);
		                    ?>
	                        <a class="btn btn-default dropdown-toggle" href="<?php echo $_tmp['l']; ?>" target="_blank">
		                        <?php echo JText::_('PLG_K2_FILMFANS_MOVIE_BUY_TICKETS'); ?>
	                        </a>
	                    <?php } ?>
                    </div>
                    <?php } ?>
                    <?php if (!empty($_sections['watch-home'])) { ?>
                    <div class="btn-group">
	                    <?php if (count($_sections['watch-home']) > 1) { ?>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <?php echo JText::_('PLG_K2_FILMFANS_MOVIE_WATCH_AT_HOME'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li class="dd-arrow"></li>
                            <?php foreach ($_sections['watch-home'] as $v) echo FilmFansHelper::movieAfflink($v); ?>
                        </ul>
	                    <?php } else {
		                    reset($_sections['watch-home']);
		                    $_tmp = current($_sections['watch-home']);
		                    ?>
	                        <a class="btn btn-default dropdown-toggle" href="<?php echo $_tmp['l']; ?>" target="_blank">
		                        <?php echo JText::_('PLG_K2_FILMFANS_MOVIE_WATCH_AT_HOME'); ?>
	                        </a>
	                    <?php } ?>
                    </div>
                    <?php } ?>
                    <?php if (!empty($_sections['watch-online'])) { ?>
                    <div class="btn-group">
	                    <?php if (count($_sections['watch-online']) > 1) { ?>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <?php echo JText::_('PLG_K2_FILMFANS_MOVIE_WATCH_ONLINE'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li class="dd-arrow"></li>
                            <?php foreach ($_sections['watch-online'] as $v) echo FilmFansHelper::movieAfflink($v); ?>
                        </ul>
	                    <?php } else {
		                    reset($_sections['watch-online']);
		                    $_tmp = current($_sections['watch-online']);
		                    ?>
	                        <a class="btn btn-default dropdown-toggle" href="<?php echo $_tmp['l']; ?>" target="_blank">
		                        <?php echo JText::_('PLG_K2_FILMFANS_MOVIE_WATCH_ONLINE'); ?>
	                        </a>
	                    <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <div class="clearfix"></div>

                <?php $rate = trim(FilmFansHelper::getVal('ffvars/imdb', $data)); ?>
                <?php if ($rate != '') {?>
                <div class="movie-rates">
                    <div class="movie-rate r-imdb<?php if ($rate == '') echo ' norate'; ?>">
                        <i></i>
                        <span><?php echo number_format(floatval($rate), 1); ?></span>
                        <div class="movie-rate-bar">
                            <div><div style="width: <?php echo floatval($rate)*10; ?>%;"></div></div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php $rate = trim(FilmFansHelper::getVal('ffvars/metacritic', $data)); ?>
                <?php if ($rate != '') {?>
                <div class="movie-rates">
                    <div class="movie-rate r-metacritic<?php if ($rate == '') echo ' norate'; ?>">
                        <i></i>
                        <span><?php echo intval($rate); ?></span>
                        <div class="movie-rate-bar">
                            <div><div style="width: <?php echo intval($rate); ?>%;"></div></div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php $rate = trim(FilmFansHelper::getVal('ffvars/rotten-tomatoes', $data)); ?>
                <?php if ($rate != '') {?>
                <div class="movie-rates">
                    <div class="movie-rate r-tomatoes<?php if ($rate == '') echo ' norate'; ?>">
                        <i></i>
                        <span><?php echo intval($rate); ?>%</span>
                        <div class="movie-rate-bar">
                            <div><div style="width: <?php echo intval($rate); ?>%;"></div></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php } ?>

                <div class="clearfix"></div>

            </div>

        </div>
        <div class="item-content row-xs-height">
            <div class="content reviews col-md-8 col-xs-height<?php if ($uid) echo ' allowed'; ?>">
                <a name="add-review"></a>

                <?php if ($uid) {
                        JHtml::_('script', 'media/filmfans/js/jquery.autosize.min.js');
                ?>
                <div class="review-add">

                    <script type="text/javascript">
                    /*<![CDATA[*/
                        jQuery(function ($) {
                            $('.review-add textarea').autosize();
                        });
                    /*]]>*/
                    </script>

                    <h4><?php echo JText::_('PLG_K2_FILMFANS_REVIEW_WRITE'); ?><small> - <?php echo $this->item->title; ?></small></h4>

                    <form action="<?php echo JUri::current(); ?>" method="post" class="ff-form ffajaxform" role="form" data-success="this.form.closest('.review-add').replaceWith(this.data.message); window.location.hash = '#add-review'; ">
                        <div class="loader"></div>
                        <fieldset>
                            <div class="editarea">
                                <div class="editheader">
                                    <span><?php echo JText::_('PLG_K2_FILMFANS_REVIEW_CHOOSE_RATE'); ?></span>

                                    <div class="ffrate-wrap">
                                        <div class="ffrate itemRatingList" data-toggle="buttons">
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 1, 10); ?>" class="btn one-star-minus"><input type="radio" name="rate" value="0.5" /></label>
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 2, 10); ?>" class="btn one-star"><input type="radio" name="rate" value="1" /></label>
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 3, 10); ?>" class="btn one-star-plus"><input type="radio" name="rate" value="1.5" /></label>
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 4, 10); ?>" class="btn two-stars"><input type="radio" name="rate" value="2" /></label>
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 5, 10); ?>" class="btn two-stars-plus"><input type="radio" name="rate" value="2.5" /></label>
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 6, 10); ?>" class="btn three-stars"><input type="radio" name="rate" value="3" /></label>
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 7, 10); ?>" class="btn three-stars-plus"><input type="radio" name="rate" value="3.5" /></label>
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 8, 10); ?>" class="btn four-stars"><input type="radio" name="rate" value="4" /></label>
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 9, 10); ?>" class="btn four-stars-plus"><input type="radio" name="rate" value="4.5" /></label>
                                            <label title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 10, 10); ?>" class="btn five-stars"><input type="radio" name="rate" value="5" /></label>
                                        </div>
                                    </div>

                                    <div class="btn-group btn-group-wantsee" data-toggle="buttons">
                                        <label class="btn btn-wantsee active">
                                            <i></i>
                                            <input type="radio" name="evaluation" value="1" checked required /> <?php echo JText::_('PLG_K2_FILMFANS_REVIEW_WANT_SEE'); ?>
                                        </label>
                                        <label class="btn btn-wantsee-not">
                                            <i></i>
                                            <input type="radio" name="evaluation" value="-1" required /> <?php echo JText::_('PLG_K2_FILMFANS_REVIEW_WANT_SEE_NOT'); ?>
                                        </label>
                                    </div>
                                </div>
                                <textarea name="review" rows="3" cols="60" required></textarea>
                            </div>

                            <h5><?php echo JText::_('PLG_K2_FILMFANS_REVIEW_CHOOSE_REACTION'); ?></h5>

                            <button type="submit" class="btn btn-submit"><?php echo JText::_('PLG_K2_FILMFANS_REVIEW_POST'); ?></button>

                            <div class="btn-group btn-group-react" data-toggle="buttons">
                                <?php foreach (FilmFansHelper::getDDList('reaction') as $k=>$v) if ($k) { ?>
                                <label class="btn">
                                    <input type="radio" name="react" value="<?php echo $k; ?>" required /> <?php echo $v; ?>
                                </label>
                                <?php } ?>
                            </div>

                        </fieldset>

                        <input type="hidden" name="mainid" value="<?php echo $this->item->mainitem->id; ?>" />
                        <input type="hidden" name="ffaction" value="item.add.review" />
                        <?php echo JHTML::_( 'form.token' ); ?>
                    </form>

                </div>
	            <?php } ?>

                <h4 class="ffsubitems-title">
	                <?php echo JText::_('PLG_K2_FILMFANS_CAT_REVIEW'); ?>
	                <a href="<?php echo FilmFansHelper::routeMenuItem('miLogin', 'index.php?ret='.base64_encode($this->item->link.'#add-review')); ?>" class="btn btn-submit btn-add-review"><?php echo JText::_('PLG_K2_FILMFANS_REVIEW_WRITE'); ?></a>
                </h4>
                <div class="ffsubitems loading" data-ajax="<?php echo FilmFansHelper::getCatLink('review', $this->item->id, $this->item->alias, $this->item->catid, $this->item->category->alias) ; ?>" data-request='{ "fftmpl" : "review" }' data-count="2" data-moretitle="<?php echo JText::_('PLG_K2_FILMFANS_SUB_MORE_REVIEWS'); ?>" data-more="2" data-empty="<?php echo htmlspecialchars($uid ? JText::_('PLG_K2_FILMFANS_SUB_EMPTY_REVIEWS') : '<a href="'.FilmFansHelper::routeMenuItem('miLogin', 'index.php?ret='.base64_encode($this->item->link.'#add-review')).'" class="btn btn-submit">'.JText::_('PLG_K2_FILMFANS_REVIEW_BE_THE_FRIST')).'</a>'; ?>" data-emptytoggle=".btn-add-review"></div>
                <div class="clearfix"></div>


                <h4><?php echo JText::_('PLG_K2_FILMFANS_MOVIE_INFO'); ?></h4>

                <div class="movie-info">

                    <?php foreach (FilmFansHelper::movieInfo($data) as $v) { ?>
                        <div class="<?php echo $v['class']; ?>">
                            <span><?php echo $v['span']; ?></span>
                            <b><?php echo $v['b']; ?></b>
                            <?php if ($v['img']) { ?>
                            <img src="<?php echo $v['img']; ?>" alt="" />
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>

            </div>
            <div class="ffgallery col-md-4 col-xs-height">
                <div class="images">
                    <h4><?php echo JText::_('PLG_K2_FILMFANS_CAT_IMAGE'); ?></h4>
                    <div class="ffsubitems loading" data-ajax="<?php echo FilmFansHelper::getCatLink('image', $this->item->id, $this->item->alias, $this->item->catid, $this->item->category->alias) ; ?>" data-count="5" data-empty="<?php echo JText::_('PLG_K2_FILMFANS_SUB_EMPTY_IMAGES'); ?>"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php break; case 'trailer': ?>
<div class="row container-video">
    <?php echo $this->item->video; ?>
</div>
<div class="row item-video">
    <div class="container-xs-height">
        <div class="row row-xs-height">
            <div class="content col-md-8 col-xs-height">
                <a name="comments"></a>
                <div class="comments">
                    <fb:comments href="<?php echo $fulllink; ?>" numposts="10" colorscheme="light" width="100%"></fb:comments>
                </div>
            </div>
            <div class="ffgallery col-md-4 col-xs-height">
                <h4><?php echo JText::_('PLG_K2_FILMFANS_SUB_RELATED_VIDEO'); ?></h4>
                <div class="ffsubitems loading" data-exclude="<?php echo $this->item->id; ?>" data-ajax="<?php echo FilmFansHelper::getCatLink('trailer', $this->item->mainitem->id, $this->item->mainitem->alias, $this->item->mainitem->catid, $this->item->mainitem->category->alias) ; ?>" data-count="3" data-empty="<?php echo JText::_('PLG_K2_FILMFANS_SUB_EMPTY_VIDEO'); ?>"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<?php break; case 'image': ?>

<div class="row item-image">
	<div class="item-image-wrap">
		<?php if (!empty($this->item->prevItem)) { ?>
			<a class="item-prev" href="<?php echo $this->item->prevItem['link']; ?>#skip-header" title="<?php echo htmlspecialchars($this->item->prevItem['title']); ?>"></a>
		<?php } ?>
        <img class="image" src="<?php echo $this->item->imageXLarge; ?>" alt="" />
		<?php if (!empty($this->item->nextItem)) { ?>
			<a class="item-next" href="<?php echo $this->item->nextItem['link']; ?>#skip-header" title="<?php echo htmlspecialchars($this->item->nextItem['title']); ?>"></a>
		<?php } ?>
	</div>
    <div class="container-xs-height">
        <div class="row row-xs-height">
            <div class="content col-md-8 col-xs-height">
                <a name="comments"></a>
                <div class="comments">
                    <fb:comments href="<?php echo $fulllink; ?>" numposts="10" colorscheme="light" width="100%"></fb:comments>
                </div>
            </div>
            <div class="ffgallery col-md-4 col-xs-height">
                <h4><?php echo JText::_('PLG_K2_FILMFANS_SUB_RELATED_IMAGES'); ?></h4>
                <div class="ffsubitems loading" data-exclude="<?php echo $this->item->id; ?>" data-ajax="<?php echo FilmFansHelper::getCatLink('image', $this->item->mainitem->id, $this->item->mainitem->alias, $this->item->mainitem->catid, $this->item->mainitem->category->alias) ; ?>" data-count="3" data-empty="<?php echo JText::_('PLG_K2_FILMFANS_SUB_EMPTY_IMAGES'); ?>"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<?php break; case 'review': ?>

<div class="row item-review">
    <div class="container-xs-height">
        <div class="row row-xs-height">
            <div class="content col-md-8 col-xs-height">
            <?php $single_mode = 1; include 'category_item_review.php';?>
            </div>
            <div class="content col-md-4 col-xs-height">
                <span class="image">
                    <img src="<?php echo $this->item->mainitem->imageSmall; ?>" alt="" />
                </span>
            </div>
        </div>
    </div>
    <div class="container-xs-height">
        <div class="row row-xs-height">
            <div class="content col-md-8 col-xs-height">
                <a name="comments"></a>
                <div class="comments">
                    <fb:comments href="<?php echo $fulllink; ?>" numposts="10" colorscheme="light" width="100%"></fb:comments>
                </div>
            </div>
            <div class="ffgallery col-md-4 col-xs-height">
                <h4><?php echo JText::_('PLG_K2_FILMFANS_SUB_RELATED_REVIEWS'); ?></h4>
                <div class="ffsubitems loading" data-exclude="<?php echo $this->item->id; ?>" data-ajax="<?php echo FilmFansHelper::getCatLink('review', $this->item->mainitem->id, $this->item->mainitem->alias, $this->item->mainitem->catid, $this->item->mainitem->category->alias) ; ?>" data-count="3" data-empty="<?php echo JText::_('PLG_K2_FILMFANS_SUB_EMPTY_REVIEWS'); ?>"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<?php } ?>