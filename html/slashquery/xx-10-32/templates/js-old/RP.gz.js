$(document).ready(function() {
	$.blockUI.defaults.message = '<img src="/images/common/ajax/orange.gif" alt="loading..." />';
	$.blockUI.defaults.css.border = 'none';
	$.blockUI.defaults.css.backgroundColor = 'transparent';
	$.blockUI.defaults.overlayCSS.backgroundColor = '#dadada';

	$('#resetPasswordForm').validate({
		errorClass: 'errorForm',
		errorElement: 'em',
		debug: true,
		errorPlacement: function(error, element) {
			var errorContainer = ('#error-' + element.attr('id'));
			error.appendTo(errorContainer);
		},
		highlight: function(element, errorClass) {
			var okContainer = ('#ok-' + element.id);
			if ($(okContainer).length > 0) {
				$(okContainer).removeClass('valid');
			}
		},
		success: function(label) {
			var pid = label.parent().attr('id').split('-');
			var okContainer = '#ok-' + pid[1];
			if ($(okContainer).length > 0) {
				$(okContainer).addClass('valid').html("<em></em");
			}
		},
		rules: {
			p1: {
				required: true,
				minlength: 8
			},
			p2: {
				required: true,
				minlength: 8,
				equalTo: '#p1'
			}
		},
		messages: {
			p1: {
				required: 'Please type your password',
				minlength: 'Minimum {0} characters.'
			},
			p2: {
				required: 'Please confirm your password',
				minlength: 'Minimum {0} characters.',
				equalTo: 'Passwords do not match'
			}
		},
		submitHandler: function() {
			$('.box').block();
			$.ajax({
				type: 'POST',
				url: '/' + $('#captcha').val(),
				cache: false,
				data: {
					np: GibberishAES.enc($.sha1($('#p1').val()), $('#token').val()),
					token: $('#token').val()
				},
				dataType: 'json',
				success: function(status) {
					if (status) {
						$('#resetPasswordForm').hide();
						$('.box').unblock();
						$('#RPSuccess').fadeIn();
					} else {
						$('#resetPasswordForm').hide();
						$('.box').unblock();
						$('#RPSuccess').fadeIn().html('<p>error changing the password</p>');
					}
				},
				error: function(data) {
					$('#resetPasswordForm').hide();
					$('.box').unblock();
					$('#RPSuccess').fadeIn().html('<p>error submitting the data</p>');
				}
			});
		}
	});

});
