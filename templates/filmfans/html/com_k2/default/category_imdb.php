<?php
// no direct access
defined('_JEXEC') or die;




if (empty($this->found)) {
    ?>
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
    </div><?php
    return;
}

?>
<div class="row">
    <div class="content imdb-search">
        <h4>
            <a href="http://www.imdb.com/"><img src="<?php echo JUri::base(true); ?>/media/filmfans/images/imdb-logo.png" alt="IMDB.COM" /></a>
        </h4>
        <?php foreach ($this->found as $item) { ?>
            <?php echo $item; ?>
        <?php } ?>
        <div class="clearfix"></div>
    </div>
</div>