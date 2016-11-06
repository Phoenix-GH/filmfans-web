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

if (isset($params['ffBehaviour'])) $behaviour = $params['ffBehaviour'];

$title = $this->item->title;

$image = $this->item->imageMedium;

$image = FilmFansHelper::getThumb($image, $this->item->video);

if (!empty($this->item->mainitem)) {

    if ($behaviour == 'review' && empty($image)) {
        $image = FilmFansHelper::getThumb($image, '', 'M', $this->item->mainitem->id);
        $title = $this->item->mainitem->title;
    }
}

?>

<div class="ffitem-inner fffeed">
    <a class="image cat-<?php echo $behaviour ? $behaviour : 'unknown'; ?>" href="<?php echo $this->item->link; ?>">
        <img src="<?php echo $image; ?>" alt="" />
        <span class="ffitem-icon i-<?php echo $behaviour; ?>"></span>
        <div class="ffautor">
            <span><?php echo JText::_('PLG_K2_FILMFANS_BY'); ?></span>
            <b><?php echo $this->item->author->name; ?></b>
        </div>
        <div class="fftitle">
            <b><?php echo $title; ?></b>
            <span><?php echo JText::_('PLG_K2_FILMFANS_TAG_'.$behaviour); ?></span>
            <s><i></i></s>
        </div>
    </a>
    <?php if ($behaviour && $behaviour != 'movie') { ?>
        <div class="ffcomments">
            <a href="<?php echo $this->item->link; ?>#comments">
                <i>
                    <?php if (!isset($this->item->share)) { ?>
                    <fb:comments-count href="<?php echo trim(JUri::base(), '/').$this->item->link; ?>"></fb:comments-count>
                    <?php } else echo isset($this->item->comments) && $this->item->share->comment_count > count($this->item->comments) ? $this->item->share->comment_count - count($this->item->comments) : $this->item->share->comment_count; ?>
                </i>
                <?php if (isset($this->item->share)) { ?>
                    <span><?php echo JText::_(
                                $this->item->share->comment_count == 0 ? 'PLG_K2_FILMFANS_COMMENTS_BE_THE_FIRST' :
                                (isset($this->item->comments) && $this->item->share->comment_count > count($this->item->comments) ? 'PLG_K2_FILMFANS_COMMENTS_MORE' :
                                ($this->item->share->comment_count > 1 ? 'PLG_K2_FILMFANS_COMMENTS' : 'PLG_K2_FILMFANS_COMMENT')));
                        ?></span>
                <?php } ?>
            </a>
        </div>
        <?php if (!empty($this->item->comments)) { ?>
        <div class="ffcomments-list">
            <?php foreach ($this->item->comments as $comment) { ?>
            <div class="ffcomment">
                <b><img src="https://graph.facebook.com/<?php echo $comment->from->id; ?>/picture?type=square"  alt=""/></b>
                <div>
                    <span class="message">
                        <span class="author"><?php echo $comment->from->name; ?></span>
                        <?php echo $comment->message; ?>
                    </span>
                    <span class="ago"><?php echo JText::sprintf('PLG_K2_FILMFANS_REVIEW_CREATE_AGO', FilmFansHelper::timeAgo($comment->created_time)); ?></span>
                    <span class="shares"><i></i><?php echo $comment->like_count; ?></span>
                </div>
            </div>
            <?php } ?>
            <div class="clearfix"></div>
        </div>
        <?php } ?>
    <?php } ?>
</div>