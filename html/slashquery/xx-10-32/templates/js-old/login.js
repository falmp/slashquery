$(document).ready(function() {

	$.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
	$.blockUI.defaults.css.border = 'none';
	$.blockUI.defaults.css.backgroundColor = 'transparent';
	$.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

/*
	function captcha() {
		$.getJSON('/cpanel/captcha', function(data) {
			$('.captcha > label').html('Capital of ' + data[0]);
			$('input[name=captcha]').val(data[1]);
			$('#hcaptcha').text(data[2]);
		});
	}

	$('#rcaptcha').click(function(e) {
		captcha();
	});

	$('#PR').click(function(e) {
		e.preventDefault();
		captcha();
		$('#badCredentials').hide();
		if ($(this).hasClass('go2login')) {
			$('#loginForm').show();
			$('#PRDiv').hide();
		} else {
			$('#loginForm').hide();
			$('#PRDiv').show();
		}
		$(this).toggleClass('go2login');
	});


	$('#PRForm').validate({
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
			remail: {
				required: {
					depends: function() {
						$(this).val($.trim($(this).val()));
						return true;
					}
				},
				email: true
			}
		},
		messages: {
			remail: {
				required: 'Please type your registered Email',
				email: 'Please enter a valid email address'
			}
		},
		submitHandler: function() {
			$.ajax({
				type: 'POST',
				url: '/cpanel/signin',
				cache: false,
				data: {
					remail: GibberishAES.enc($('#remail').val().toLowerCase(), $('#token').val()),
					captcha: $.trim($('#captcha').val())
				},
				dataType: 'json',
				beforeSend: function() {
					$('#login-form').block();
				},
				success: function(status) {
					$('#login-form').unblock();
					if (status) {
						$('#PRForm').hide();
						$('#successRecovery').fadeIn();
					} else {
						captcha();
						$('#error-captcha').html('<em class="errorForm">required</em>');
					}
				},
				error: function() {
					//		location.reload(true);
				}
			});
		}
	});
	*/

	$('#loginForm').validate({
		errorClass: "help-block",
		errorElement: "p",
		errorPlacement: function(error, element) {
			$(element).parent().after(error);
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
					case 'openid':
						window.location.replace(status['openid']);
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
									location.reload(true);
								}
							})
						});
						break;
					case 'csrf':
						location.reload(true);
						break;
					default:
						$("#loginForm").unblock();
					}
				},
				error: function(data) {
					location.reload(true);
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
