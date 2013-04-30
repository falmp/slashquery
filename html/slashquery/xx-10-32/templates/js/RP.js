$(document).ready(function() {
	$.blockUI.defaults.message = '<img src="/slashquery/xx-10-32/templates/img/ajax-mac.gif" alt="loading..." />';
	$.blockUI.defaults.css.border = 'none';
	$.blockUI.defaults.css.backgroundColor = 'transparent';
	$.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

	$('#resetPasswordForm').validate({
		errorClass: 'help-inline',
		errorElement: 'span',
		errorPlacement: function(error, element) {
			$(element).after(error);
		},
		highlight: function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('success');
			$(element).parents('.control-group').addClass('error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('error');
			$(element).parents('.control-group').addClass('success');
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
			$('#resetPasswordForm').block();
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
					$('#resetPasswordForm').unblock().hide();
					if (status) {
						$('#RPSuccess').fadeIn();
					} else {
						$('#RPError').fadeIn();
					}
				},
				error: function(data) {
					window.location.reload(true); // NOTE: 'true' forces NON-cached copy to be returned
				}
			});
		}
	});

	$('.cancelButton, .close').click(function() {
		window.location.replace('/');
	});

});
