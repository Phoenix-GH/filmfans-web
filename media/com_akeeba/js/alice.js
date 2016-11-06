if(typeof(akeeba) == 'undefined') {
	var akeeba = {};
}
if(typeof(akeeba.jQuery) == 'undefined') {
	akeeba.jQuery = jQuery.noConflict();
}

var AKEEBA_ANALYZE_SUCCESS =  1;
var AKEEBA_ANALYZE_WARNING =  0;
var AKEEBA_ANALYZE_FAILURE = -1;

akeeba.jQuery(document).ready(function($)
{
	var log_selector = $('#log');
	var analyze      = $('#analyze-log');

	log_selector.attr('disabled', null);

	log_selector.change(function()
	{
		jQuery(this).val() ? analyze.show() : analyze.hide();
	});

	analyze.click(function(){

		if(jQuery(this).data('started') == true){
			return false;
		}

		jQuery(this).addClass('btn-inverse').removeClass('btn-primary').data('started', true);
		log_selector.attr('disabled', true);
        jQuery('#stepper-complete').empty();

		var stepper = new AkeebaStepper({
			onBeforeStart : function(polling){
				polling.data.log = jQuery('#log').val();
			},
			onComplete : function(result){
				var testHolder = jQuery(document.createElement('div')).addClass('well');
                var striped    = jQuery(document.createElement('div'))
                                    .addClass('row-striped')
                                    .appendTo(testHolder);

                var results    = JSON.parse(result.Results);
                var successTxt = Joomla.JText._('AKEEBA_ALICE_SUCCESSS');
                var warningTxt = Joomla.JText._('AKEEBA_ALICE_WARNING');
                var errorTxt   = Joomla.JText._('AKEEBA_ALICE_ERROR');

				jQuery.each(results, function(idx, item){
					var test = jQuery(document.createElement('div'))
							.addClass('row-fluid').css('width', '99%');

                    var lblResult = 'label-success';
                    var text      = successTxt;

                    if(item.result == AKEEBA_ANALYZE_WARNING){
                        text      = warningTxt;
                        lblResult = 'label-warning';
                    }
                    else if(item.result == AKEEBA_ANALYZE_FAILURE){
                        text = errorTxt;
                        lblResult = 'label-important';
                    }

                    jQuery(document.createElement('div')).addClass('pull-left').html(item.check).appendTo(test);
                    jQuery(document.createElement('div')).addClass('pull-right label ' + lblResult).html(text).appendTo(test);
                    jQuery(document.createElement('div')).addClass('clearfix').appendTo(test);

                    if(!item.passed)
                    {
                        var holder = jQuery(document.createElement('div')).addClass('help-block').appendTo(test);

                        jQuery(document.createElement('div')).html(item.error).appendTo(holder);
                        jQuery(document.createElement('div'))
                            .html(jQuery(document.createElement('em')).html(item.solution)).appendTo(holder);
                    }

                    test.appendTo(striped);
				});

                testHolder.appendTo('#stepper-complete');

				jQuery('#stepper-progress-pane').hide("fast");
				jQuery('#stepper-complete').show();
				analyze.removeClass('btn-inverse').addClass('btn-primary').data('started', false);
				log_selector.attr('disabled', null);
			}
		});

		stepper.init();

		return false;
	});

	log_selector.change();
});