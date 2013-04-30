$(document).ready(function() {

	$.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
	$.blockUI.defaults.css.border = 'none';
	$.blockUI.defaults.css.backgroundColor = 'transparent';
	$.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

	$('#loginForm').validate({
		errorClass: "help-block",
		errorElement: "p",
		errorPlacement: function(error, element) {
			$(element).parent().after(error);
		},
		highlight: function(element, errorClass, validClass) {
			$(element).closest('.control-group').removeClass('success').addClass('error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).closest('.control-group').removeClass('error').addClass('success');
		},
		rules: {
			email: {
				required: {
					depends: function() {
						$(this).val($.trim($(this).val()));
						return $('#oi').val() ? !$(this).val('') : true;
					}
				},
				email: true
			},
			pw: {
				required: {
					depends: function() {
						return $('#oi').val() ? !$(this).val('') : true;
					}
				},
				minlength: 8
			},
			oi: {
				required: {
					depends: function() {
						$(this).val($.trim($(this).val()));
					}
				}
			}
		},
		messages: {
			email: {
				required: 'Please type your email.',
				email: 'Please enter a valid email address.'
			},
			pw: {
				required: 'Please type your password.',
				minlength: 'At least {0} characters required.'
			}
		},
		submitHandler: function() {
			$('#loginForm').block(); /* data, email, password */
			var d, e, p;
			d = {};
			e = GibberishAES.enc($('#email').val().toLowerCase(), $('input[name="token"]').val());
			p = GibberishAES.enc($.sha1($('#pw').val()), $('input[name="token"]').val());
			d.t = $('input[name="token"]').val();
			d.e = e;
			d.p = p;
			d.oi = $.trim($('#oi').val());
			d.r = $('#rmb').is(':checked');
			d.rc = $('#recaptcha_challenge_field').val();
			d.rr = $('#recaptcha_response_field').val();

			$.ajax({
				type: 'POST',
				url: '/cpanel/sqRules/signIn',
				cache: false,
				data: d,
				dataType: 'json',
				success: function(data) {
					switch (data) {
					case true:
						window.location.replace('/cpanel');
						break;
					case 'avoid_logging_twice':
						$("#loginForm").unblock().hide();
						$('#forceLoginAlert').show();
						break;
					case 'forcelogin':
						$('#loginForm').unblock().hide();
						$('#forceLoginAlert').show();
						$('#forceLoginContinue').show();
						$('#continueButton').click(function() {
							$('#loginForm').block();
							d.f = $('#forcelogin').is(':checked');
							$.ajax({
								type: 'POST',
								url: '/cpanel/sqRules/signIn',
								cache: false,
								data: d,
								dataType: 'json',
								success: function(data) {
									window.location.replace('/cpanel');
								},
								error: function(data, textStatus) {
									window.location.reload(true);
								}
							})
						});
						break;
					case 'r':
						window.location.reload(true);
						break;
					default:
						if (data.openid) {
							window.location.replace(data.openid);
						}
						break;
					}
					$("#loginForm").unblock();
				},
				error: function(data) {
					window.location.reload(true);
				}
			});
		}
	});

	$('.cancelButton').click(function() {
		window.location.replace('/');
	});

	$('#fp').click(function(e) {
		e.preventDefault();
		$('#loginForm').hide();
		$('#pr').show();
	});

	$('#prForm').submit(function() {
		var t = $(this);
		t.block();
		$.post('/cpanel/sqRules/resetPassword', $(this).serializeArray(), function(data) {
			t.unblock().slideUp();
			$('#prSuccess').show();
		}, 'json');
		return false;
	});

});
