if ( typeof (jQuery) != 'undefined') {
	jQuery(document).ready(function() {
		jQuery('#socialConnectAutoPostToggler').click(function(event) {
			event.preventDefault();
			jQuery('#socialConnectAutoPostInner').toggleClass('socialConnectAutoPostOpen');
			jQuery('#socialConnectAutoPost').toggleClass('socialConnectAutoPostOpen');
		});
		jQuery('#socialConnectAutoPostCloseButton').click(function(event) {
			event.preventDefault();
			jQuery('#socialConnectAutoPostInner').removeClass('socialConnectAutoPostOpen');
			jQuery('#socialConnectAutoPost').removeClass('socialConnectAutoPostOpen');
		});
		jQuery('#socialConnectAutoPost input, #socialConnectAutoPost textarea').change(function(event) {
			event.preventDefault();
			var form = jQuery('form[name="adminForm"]');
			form.find('input.socialconnectAutoPostInput').remove();
			var services = jQuery('#socialConnectAutoPost input:checked').clone();
			services.attr('type', 'hidden').addClass('socialconnectAutoPostInput');
			form.append(services);
			var suffix = jQuery('<input type="hidden" />').val(jQuery('#socialConnectAutoPostSuffix').val()).attr('name', 'socialConnectAutoPostSuffix').addClass('socialconnectAutoPostInput');
			form.append(suffix);
		});
	});
} else {
	window.addEvent('domready', function() {
		$('socialConnectAutoPostToggler').addEvent('click', function(event) {
			event.preventDefault();
			if ($('socialConnectAutoPostInner')) {
				$('socialConnectAutoPostInner').toggleClass('socialConnectAutoPostOpen');
			}
			if ($('socialConnectAutoPost')) {
				$('socialConnectAutoPost').toggleClass('socialConnectAutoPostOpen');
			}
		});
		$('socialConnectAutoPostCloseButton').addEvent('click', function(event) {
			event.preventDefault();
			if ($('socialConnectAutoPostInner')) {
				$('socialConnectAutoPostInner').removeClass('socialConnectAutoPostOpen');
			}
			if ($('socialConnectAutoPost')) {
				$('socialConnectAutoPost').removeClass('socialConnectAutoPostOpen');
			}
		});
		function socialConnectAutoPost() {
			if ( typeof ($$('input.socialconnectAutoPostInput').destroy) == 'function') {
				$$('input.socialconnectAutoPostInput').destroy();
			} else {
				$$('input.socialconnectAutoPostInput').each(function(el) {
					el.remove();
				})
			}
			var form = $$('form[name="adminForm"]');
			var services = $$('#socialConnectAutoPost input');
			services.each(function(service) {
				if (service.getProperty('checked')) {
					var clone = service.clone();
					clone.setProperty('type', 'hidden').addClass('socialconnectAutoPostInput');
					form.adopt(clone);
				}
			});
			var suffixValue = $$('#socialConnectAutoPostSuffix').getProperty('value');
			var suffix = new Element('input', {
				'type' : 'hidden',
				'value' : suffixValue,
				'name' : 'socialConnectAutoPostSuffix'
			}).addClass('socialconnectAutoPostInput');
			form.adopt(suffix);
		}


		$$('#socialConnectAutoPost input').addEvent('change', function(event) {
			event.preventDefault();
			socialConnectAutoPost();

		});
		$$('#socialConnectAutoPost textarea').addEvent('change', function(event) {
			event.preventDefault();
			socialConnectAutoPost();
		});
	});
}

