<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();

$url = &JURI::getInstance();
$url->setVar('start', null);

?>
<div class="header-after">
    <script type="text/javascript">
    /*<![CDATA[*/
        jQuery(function ($) {
            $(document).on('change', '.fffiltersimple label :input', function() {
                var $this = $(this);
                $this.closest('.itemnav').find('.active').removeClass('active');
                $this.closest('label').addClass('active');
            });
        });
    /*]]>*/
    </script>
    <ul class="itemnav">
        <?php if ($mainframe->input->get('view') == 'itemlist') { ?>
        <form class="fffilter fffiltersimple" method="get" action="<?php echo $url; ?>">
            <div class="btn-section last">
                <ul class="itemnav"><!--
                    <?php foreach ($cats as $k=>$v) { ?>
                    <?php if ($k != 'movie') { ?>
                    --><li>
                        <label class="ff-<?php echo $k; ?><?php if ($params->get('ffcat') == $k) echo ' active'; ?>">
                            <input type="radio" name="ffcat" value="<?php echo $k; ?>"<?php if ($params->get('ffcat') == $k) echo ' checked="checked"'; ?> autocomplete="off" /><span><?php echo JText::_('PLG_K2_FILMFANS_CAT_'.$k); ?></span>
                            <i></i>
                        </label>
                    </li><!--
                    <?php } else { ?>
                    --><li><a class="ff-<?php echo $k; ?><?php if (!$params->get('ffcat')) echo ' active'; ?> semi-active" href="<?php echo $mainitem->link; ?>"><span><?php echo $mainitem->title_text; ?></span><i></i></a></li><!--
                    <?php } ?>
                    <?php } ?>
                --></ul>
            </div>
            <div class="clearfix"></div>
        </form>
        <?php } else { ?>
        <ul class="itemnav"><!--
            <?php foreach ($cats as $k=>$v) { ?>
            <?php if ($k != 'movie') { ?>
            --><li><a class="ff-<?php echo $k; ?><?php if ($params->get('ffcat') == $k) echo ' active'; ?>" href="<?php echo FilmFansHelper::getCatLink($k, $mainitem->id, $mainitem->alias, $mainitem->catid, $mainitem->category->alias); ?>"><span><?php echo JText::_('PLG_K2_FILMFANS_CAT_'.$k); ?></span><i></i></a></li><!--
            <?php } else { ?>
            --><li><a class="ff-<?php echo $k; ?><?php if (!$params->get('ffcat')) echo ' active'; ?> semi-active" href="<?php echo $mainitem->link; ?>"><span><?php echo $mainitem->title_text; ?></span><i></i></a></li><!--
            <?php } ?>
            <?php } ?>
        --></ul>
        <?php } ?>
    </ul>
</div>