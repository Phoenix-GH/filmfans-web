<?php
// no direct access
defined('_JEXEC') or die;

$url = &JURI::getInstance();
$url->setVar('start', null);

$cats = array_map('strtoupper', $cats);

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
    <form class="fffilter fffiltersimple" method="get" action="<?php echo $url; ?>">
        <input type="hidden" name="fffeatured" value="<?php echo $params->get('fffeatured', 0); ?>" />
        <input type="hidden" name="term" value="<?php echo $params->get('term', ''); ?>" />
        <input type="hidden" name="service" value="filmfans" />
        <div class="btn-section last">
            <ul class="itemnav"><!--
                --><li>
                    <label class="ff-all <?php if (!$params->get('ffcat')) echo ' active'; ?>">
                        <input type="radio" name="ffcat" value=""<?php if (!$params->get('ffcat')) echo ' checked="checked"'; ?> autocomplete="off" /><span><?php echo JText::_('PLG_K2_FILMFANS_FILTER_ALL'); ?></span>
                        <i class="arrow"></i>
                    </label>
                </li><!--
                <?php foreach ($cats as $k=>$v) { ?>
                --><li>
                    <label class="ff-<?php echo $k; ?><?php if ($params->get('ffcat') == $k) echo ' active'; ?>">
                        <input type="radio" name="ffcat" value="<?php echo $k; ?>"<?php if ($params->get('ffcat') == $k) echo ' checked="checked"'; ?> autocomplete="off" /><span><?php echo JText::_('PLG_K2_FILMFANS_CAT_'.$k); ?></span>
                        <i class="arrow"></i>
                    </label>
                </li><!--
                <?php } ?>
            --></ul>
        </div>
        <div class="clearfix"></div>
    </form>
</div>