<?php
// no direct access
defined('_JEXEC') or die;

?>

<div class="row container-black">

	<div class="container">

        <div class="ffwarning ffinfo">
            <a href="javascript:void(0)" class="ffwarning-close"><span class="glyphicon glyphicon-remove"></span></a>
            <div class="row">
                <div class="ufe1 col-md-8 col-lg-8">
                    <h2><?php echo JText::sprintf('K2_USER_NOT_FOUND'); ?></h2>
                </div>
                <div class="ufe2 col-md-4 col-lg-4">
                    <?php echo JText::sprintf('PLG_K2_FILMFANS_USER_FEED_EMPTY2', FilmFansHelper::routeMenuItem('miFAQ')); ?>
                </div>
            </div>
        </div>
    </div>
</div>