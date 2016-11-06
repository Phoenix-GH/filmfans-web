/**
 * @version		$Id: socialconnect.js 2674 2013-04-03 15:01:35Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */
function socialConnectPopupWindow(url, name, width, height, scroll) {
	var left = (screen.width - width) / 2;
	var top = (screen.height - height) / 2;
	var properties = 'height=' + height + ',width=' + width + ',top=' + top + ',left=' + left + ',scrollbars=' + scroll + ',resizable'
	window.open(url, name, properties).focus();
}

function socialConnectPlaceholderSupport() {
	var input = document.createElement('input');
	return ('placeholder' in input);
}

function socialConnectIEBrowserClass() {
	var IEClass;
	if (navigator.userAgent.toLowerCase().indexOf('msie 6') != -1) {
		IEClass = 'scIsIE6';
	} else if (navigator.userAgent.toLowerCase().indexOf('msie 7') != -1) {
		IEClass = 'scIsIE7';
	} else if (navigator.userAgent.toLowerCase().indexOf('msie 8') != -1) {
		IEClass = 'scIsIE8';
	}
	return IEClass;
}

if ( typeof (jQuery) !== 'undefined') {
	jQuery(document).ready(function() {
		// Add IE browser class to body
		jQuery('body').addClass(socialConnectIEBrowserClass());
		// Sign out event
		jQuery('.socialConnectSignOutButton').click(function() {
			jQuery('.socialConnectSignOutForm')[0].submit();
		});
		// Sign in events
		jQuery('.socialConnectServiceButton').click(function(e) {
			e.preventDefault();
			socialConnectPopupWindow(this.href, this.text, 480, 360);
		});
		// Compact module layout
		jQuery('.socialConnectCompactLayout .socialConnectToggler').click(function(event) {
			event.preventDefault();
			jQuery('.socialConnectCompactLayout .socialConnectModal').toggleClass('socialConnectVisible');
		});
		// Placeholder fallback
		if (!socialConnectPlaceholderSupport()) {
			jQuery('.socialConnectInput').each(function() {
				if (jQuery(this).attr('placeholder')) {
					jQuery(this).val(jQuery(this).attr('placeholder'));
					jQuery(this).focus(function() {
						if (jQuery(this).val() == jQuery(this).attr('placeholder')) {
							jQuery(this).val('');
						}
					});
					jQuery(this).blur(function() {
						if (jQuery(this).val() == '') {
							jQuery(this).val(jQuery(this).attr('placeholder'));
						}
					});
				}

			});
		}
	});
} else {
	window.addEvent('domready', function() {
		// Add IE browser class to body
		$$('body').addClass(socialConnectIEBrowserClass());
		// Sign out event
		$$('.socialConnectSignOutButton').addEvent('click', function() {
			$$('.socialConnectSignOutForm')[0].submit();
		});
		// Sign in events
		$$('.socialConnectServiceButton').addEvent('click', function(e) {
			e.preventDefault();
			socialConnectPopupWindow(this.href, this.text, 480, 360);
		});
		// Compact module layout
		$$('.socialConnectCompactLayout .socialConnectToggler').addEvent('click', function(event) {
			event.preventDefault();
			$$('.socialConnectCompactLayout .socialConnectModal').toggleClass('socialConnectVisible');
		});
		// Placeholder fallback
		if (!socialConnectPlaceholderSupport()) {
			$$('.socialConnectInput').each(function(input) {
				if (input.placeholder) {
					input.value = input.placeholder;
					input.addEvents({
						focus : function() {
							if (input.value == input.placeholder) {
								input.value = '';
							}
						},
						blur : function() {
							if (input.value == '') {
								input.value = input.placeholder;
							}
						}
					});
				}
			});
		}
	});
}