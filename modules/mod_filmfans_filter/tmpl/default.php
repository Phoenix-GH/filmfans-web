<?php
// no direct access
defined('_JEXEC') or die;

$ord = $params->get('fford');
$ords = array('az' => 1, 'za' => 1, 'hits' => 0, 'rate' => 0, 'share' => 0, 'review' => 0);

$url = &JURI::getInstance();
$url->setVar('start', null);

if (!defined('FF_MOD_FILTER')) {
    define('FF_MOD_FILTER', 1);
?>
<script type="text/javascript">
/*<![CDATA[*/
    jQuery(function($) {

        var updateSPTag = function () {

            $('.sp-tag').each(function() {
                var that = $(this);
                var form = that.closest('form');
                var radio = form.find('[name="fftag"]:checked');
                var val = that.data('placeholder');

                if (radio.val())
                    val = radio.siblings('span').first().html();

                that.find('.filter-option').html(val);
                that.toggleClass('selectpick-value', radio.val() > 0);
            });
        };

        updateSPTag();

        $(document).on('change', '.fffilter input[name="fford"]', function() {
            var parent = $(this).closest('.btn-group');
            var custom = parent.find('.ff-az-custom');
            custom.toggleClass('ff-az-active', parent.find('.ff-az :radio').prop('checked'));
            custom.toggleClass('ff-za-active', parent.find('.ff-za :radio').prop('checked'));
        });

        $(document).on('click', '.fffilter .sp-tag', function() {
            var that = $(this);
            var form = that.closest('.search_row');
            var tags = form.find('.sp-tags');
            tags.toggleClass('open');
            var top = Math.max(0, that.offset().top + that.outerHeight() + 10 - form.offset().top);
            tags.css('top', top + 'px');
        });

        $(document).on('click change', '.fffilter .sp-tags .tag :input', updateSPTag);

        $(document).on('click', '.fffilter .sp-tag-container .selectpick-clear', function() {
            var form = $(this).closest('form');
            form.find('[name="fftag"][value=""]').prop('checked', true).trigger('change');
            form.find('.sp-tags.open, .sp-tag-switch.open').removeClass('open');
            event.stopPropagation();
        });

        $(document).on('click', 'html', function() {
            $('.sp-tags.open, .sp-tag-switch.open').removeClass('open');
        });

        $(document).on('click', '.sp-tag', function(event){
            event.stopPropagation();
        });

        $(document).on('click', '.fffilter .ff-az-custom', function() {
            var $this = $(this);
            var parent = $this.closest('.btn-group');
            var az = parent.find('.ff-az');
            var za = parent.find('.ff-za');
            if (az.hasClass('active')) {
                za.eq(0).button('toggle');
            } else {
                az.eq(0).button('toggle');
            }
        });
    });
/*]]>*/
</script>
<?php } ?>

<div class="row feature search_row">
    <form class="fffilter" method="get" action="<?php echo $url; ?>">
        <h1 class="filter_heading"><?php echo $module->title; ?></h1>
	    <div class="search_filter">
            <input type="hidden" name="fffeatured" value="<?php echo $params->get('fffeatured', 0); ?>" />
            <input type="hidden" name="ffpopular" value="<?php echo $params->get('ffpopular', 0); ?>" />
            <input type="hidden" name="fftmpl" value="<?php echo $params->get('fftmpl'); ?>" />
            <input type="hidden" name="term" value="<?php echo $params->get('term', ''); ?>" />
            <input type="hidden" name="service" value="filmfans" />
            <div class="btn-section">
                <select name="ffcat" class="selectpick col-no" data-placeholder="<?php echo JText::_('PLG_K2_FILMFANS_FILTER_CATEGORY'); ?>" autocomplete="off">
                    <?php echo JHtml::_('select.options',  array('' => JText::_('PLG_K2_FILMFANS_FILTER_ALL')) + array_map('strtoupper', $cats), null, null, $params->get('ffcat')); ?>
                </select>
            </div>
            <div class="btn-section sp-tag-container">
                <div class="btn-group col-no sp-tag-switch">
                    <div data-toggle="dropdown" class="btn dropdown-toggle form-control btn-default sp-tag selectpick-value" type="button" data-placeholder="<?php echo JText::_('PLG_K2_FILMFANS_FILTER_TAG'); ?>" aria-expanded="true">
                        <span class="filter-option pull-left"></span>&nbsp;<span class="caret"></span>
                        <a class="selectpick-clear"><span class="glyphicon glyphicon-remove"></span></a>
                    </div>
                </div>
            </div>
            <div class="btn-section last">
                <div class="btn-group-label separator">
                    <span>SORT BY</span>
                </div>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary ff-az-custom<?php if ($ord == 'az' || $ord == 'za') echo ' ff-'.$ord.'-active'; ?>">
                        <span><b class="ff-az-text"><?php echo JText::_('PLG_K2_FILMFANS_FILTER_ORD_AZ'); ?></b><b class="ff-za-text"><?php echo JText::_('PLG_K2_FILMFANS_FILTER_ORD_ZA'); ?></b>&nbsp;
                        <span class="glyphicon glyphicon-arrow-up ff-za-arrow" aria-hidden="true"></span>
                        <span class="glyphicon glyphicon-arrow-down ff-az-arrow" aria-hidden="true"></span>
                        </span>
                    </label>
                    <?php foreach ($ords as $k=>$v) { ?>
                    <label class="btn btn-primary ff-<?php echo $k; ?><?php if ($ord == $k) echo ' active'; ?>"<?php if ($v) echo ' style="display: none;"'; ?>>
                        <input type="radio" name="fford" value="<?php echo $k; ?>"<?php if ($ord == $k) echo ' checked="checked"'; ?> autocomplete="off" /><span><?php echo JText::_('PLG_K2_FILMFANS_FILTER_ORD_'.$k); ?></span>
                    </label>
                    <?php } ?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <div class="sp-tags tags">
            <div class="sp-tags-wrap">
	            <?php foreach (array('' => '- '.JText::_('PLG_K2_FILMFANS_FILTER_ALL').' -') + $tags as $k=>$v) { ?>
	            <label class="tag <?php if ($params->get('fftag') == $k) echo ' active'; ?>">
	                <input type="radio" name="fftag" value="<?php echo $k; ?>"<?php if ($params->get('fftag') == $k) echo ' checked="checked"'; ?> autocomplete="off" /><span><?php echo $v; ?></span>
	            </label>
	            <?php } ?>
            </div>
        </div>
    </form>
</div>