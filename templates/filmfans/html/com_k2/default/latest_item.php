<?php
/**
 * @version		1.0
 * @package		FilmFans K2
 * @author		jThemes
 * @copyright	Copyright (c) 2010 - 2014 jThemes. All rights reserved.
 * @license		Private
 */
// no direct access
defined('_JEXEC') or die;

//FilmFansHelper::_sd($this->item);

$behaviour = '';

$params = FilmFansHelper::categoryParams($this->item->catid);

if (isset($params['ffBehaviour']))
    $behaviour = $params['ffBehaviour'];

$title = $this->item->title;
$intro = FilmFansHelper::cutString($this->item->introtext, 200);

$image = $this->item->imageMedium;

$image = FilmFansHelper::getThumb($image, $this->item->video);

if (!empty($this->item->mainitem)) {

    if (empty($intro)) {
        $intro = FilmFansHelper::cutString($this->item->mainitem->introtext, 200);
    }

    if ($behaviour == 'review' && empty($image)) {
        $image = FilmFansHelper::getThumb($image, '', 'M', $this->item->mainitem->id);
        $title = $this->item->mainitem->title;
    }
}

?>

<div class="ffitem-inner">
    <a class="image" href="<?php echo $this->item->link; ?>">
        <img src="<?php echo $image; ?>" alt="" />
        <span class="ffitem-icon i-<?php echo $behaviour; ?>"></span>
    </a>

    <div class="ffdesc cat-<?php echo $behaviour ? $behaviour : 'unknown'; ?>">
        <div class="fftitle">
            <a class="title" href="<?php echo $this->item->link; ?>"><?php echo $title; ?></a>
            <div class="clearfix"></div>
            <?php if ($behaviour == 'review' && $this->item->author) {
                $avatar = FilmFansHelper::getAvatar($this->item->author->profile->image, $this->item->author);
                ?>
            <a class="ffavatar" href="<?php echo $this->item->author->get('link'); ?>"><img src="<?php echo $avatar[0]; ?>" alt="" /></a>
            <h5><a href="<?php echo $this->item->author->get('link'); ?>"><?php echo $this->item->author->get('name'); ?></a></h5>
            <?php } ?>
            <p><?php echo $intro; ?></p>
        </div>
        <?php if ($behaviour) { ?>
        <div class="ffcat">
            <a href="<?php echo isset($this->item->category->link) ? $this->item->category->link : 'javascript:void(0)'; ?>">
                <?php echo !empty($this->item->categoryname) ? $this->item->categoryname : JText::_('PLG_K2_FILMFANS_TAG_'.$behaviour); ?>
                <i></i>
            </a>
        </div>
        <?php } ?>
        <div class="ffrating">
            <div class="ffrate-wrap">

                <?php if (!$this->item->numOfvotes) { ?>
                <a class="ffrate-zero" href="javascript:void(0)"><?php echo JText::_('PLG_K2_FILMFANS_RATE_ZERO'); ?></a>
                <?php } ?>
                <div class="ffitemRatingForm"<?php if (!$this->item->numOfvotes) echo ' style="display: none;"'; ?>>
                    <?php include('item_rating.php'); ?>
                </div>
                <span class="ffrate-num" id="itemRatingLog<?php echo $this->item->id; ?>">
                    <?php if ($this->item->numOfvotes) { ?>
                    <?php echo $this->item->_rate ? number_format($this->item->_rate*2, 2) : ''; ?><span>&nbsp;&nbsp;(<?php echo $this->item->numOfvotes .'&nbsp;'. ($this->item->numOfvotes > 1 ? JText::_('K2_VOTES') : JText::_('K2_VOTE')); ?>)</span>
                    <?php } ?>
                </span>
            </div>
            <?php if ($behaviour && $behaviour != 'movie') { ?>
            <div class="ffcomments">
                <a href="<?php echo $this->item->link; ?>#comments">
                    <fb:comments-count href="<?php echo trim(JUri::base(), '/').$this->item->link; ?>"></fb:comments-count>
                </a>
            </div>
            <?php } ?>
            <div class="clearfix"></div>
        </div>
    </div>
</div>