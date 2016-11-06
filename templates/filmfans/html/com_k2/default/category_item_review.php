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

$title = $this->item->title;

if (!empty($this->item->mainitem)) {
    $title = $this->item->mainitem->title;
}

$ago = JText::sprintf('PLG_K2_FILMFANS_REVIEW_CREATE_AGO', FilmFansHelper::timeAgo($this->item->created));

$fulllink = trim(JUri::base(), '/').$this->item->link;

$avatar = FilmFansHelper::getAvatar($this->item->author->profile->image, $this->item->author);

?>

<div class="ffrowitem">

    <div class="col-avatar">
        <a class="ffavatar" href="<?php echo $this->item->author->get('link'); ?>"><img src="<?php echo $avatar[0]; ?>" alt="" /></a>
    </div>
    <div class="col-title">
        <div class="tools">
            <div><?php echo $ago; ?></div>
        </div>
        <h5>
            <a class="author" href="<?php echo $this->item->author->get('link'); ?>"><?php echo $this->item->author->get('name'); ?></a>
            <a class="movie" href="<?php echo $this->item->link; ?>"><?php echo $title; ?></a>
        </h5>
        <span class="ffrate-wrap" data-toggle="tooltip" title="<?php echo isset($this->item->plugins['ffvars']['ffrate']) ? number_format($this->item->plugins['ffvars']['ffrate']*2, 1) : '0.0'; ?>/10.0" data-placement="bottom">
            <span class="ffrate"><span class="ffrate-active" style="width: <?php echo isset($this->item->plugins['ffvars']['ffrate']) ? $this->item->plugins['ffvars']['ffrate']*20 : 0; ?>%;"></span></span>
        </span>
        <span class="reaction"><?php if (!empty($this->item->plugins['ffvars']['ffreaction'])) echo FilmFansHelper::getDD('reaction', $this->item->plugins['ffvars']['ffreaction'], $this->item->plugins['ffvars']['ffreaction']); ?></span>
        <div class="clearfix"></div>
    </div>
    <div class="col-text <?php if (!isset($single_mode)) echo ' ellipsis'; ?>">
        <?php echo $this->item->text; ?>
        <?php if (!isset($single_mode)) { ?>
        <a class="readmore"><span aria-hidden="true" class="glyphicon glyphicon-chevron-down"></span> Read more</a>
        <?php } ?>
    </div>
    <div class="clearfix"></div>
    <?php if (isset($single_mode)) { ?>
    <div class="reaction-form" data-role="form">
        <?php
            $uri = clone JUri::getInstance();
            $uri->setVar('ffaction', 'item.react');
            $uri->setVar('ret', base64_encode(JUri::current()));
            $userreact = isset($this->item->userreact) ? $this->item->userreact : 0;
        ?>
        <fieldset>
            <div class="btn-group btn-group-react" data-toggle="buttons" data-ajax="<?php echo $uri; ?>">
                <?php foreach (FilmFansHelper::getDDList('reaction') as $k=>$v) if ($k) { ?>
                <label class="btn<?php if ($userreact == $k) echo ' active'; ?>">
                    <input type="radio" name="react" value="<?php echo $k; ?>"<?php if ($userreact == $k) echo ' checked'; ?> /> <?php echo $v; ?>
                </label>
                <?php } ?>
            </div>
        </fieldset>
    </div>
    <?php } ?>
    <div class="ffsocial">
        <div><span><?php echo JText::_('PLG_K2_FILMFANS_REVIEW_SHARE'); ?></span> <?php echo implode(' ', FilmFansHelper::social($fulllink)); ?></div>
        <?php if (!isset($single_mode)) { ?>
        <a class="count" href="<?php echo $this->item->link; ?>#comments">
            <span>
                <fb:comments-count href="<?php echo $fulllink; ?>"></fb:comments-count>
            </span>
        </a>
        <?php } ?>
    </div>
    <?php if (!isset($single_mode)) { ?>
    <div class="comments">
        <!--<fb:comments href="<?php echo trim(JUri::base(), '/').$this->item->link; ?>" numposts="3" colorscheme="light" width="100%"></fb:comments>-->
    </div>
    <?php } ?>
</div>