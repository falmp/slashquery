<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
			<meta charset="utf-8">
			<title>cPanel::Login</title>
			<meta name="description" content="SlashQuery cPanel Login">
			<meta name="viewport" content="width=device-width">

			<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
			<link rel="stylesheet" href="<?php echo $this->getPath(); ?>css/bootstrap.min.css">
			<link rel="stylesheet" href="<?php echo $this->getPath(); ?>css/custom.css">
			<link rel="stylesheet" href="<?php echo $this->getPath(); ?>css/bootstrap-responsive.min.css">
			<script src="<?php echo $this->getPath(); ?>js/vendor/modernizr-2.6.2.min.js"></script>
	</head>
	<body>
			<!--[if lt IE 7]>
					<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
			<![endif]-->

	  <div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar">&nbsp;</span>
						<span class="icon-bar">&nbsp;</span>
						<span class="icon-bar">&nbsp;</span>
					</button>

					<a class="brand" href="/cpanel"><img src="<?php echo $this->getPath();?>img/logo-sq.png" alt="slashquery"></a>

					<div class="nav-collapse">
						<ul class="nav pull-right">
							<li class="dropdown"><a href="/"><?php echo $this->router->site; ?></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="span12">
					<div class="centeredBlock" style="height: 500px;">
						<div class="centeredBlockDiv">
							<div class="box-header">
								Control Panel <span class="pull-right"><small><a href="/">Home</a></small></span>
							</div>

							<div class="box">

								<script type="text/javascript">var RecaptchaOptions = {theme : 'white'};</script>

								<form id="loginForm" action="/cpanel" method="post">
									<input type="hidden" name="token" value="<?php echo $this['token']; ?>">

									<fieldset class="control-group">
										<label for="email" class="control-label">Email address</label>

										<div class="input-prepend controls">
											<span class="add-on"><i class="icon-envelope">&nbsp;</i></span><input type="email" name="email" id="email" class="input-xlarge" required="required">
										</div>
									</fieldset>

									<fieldset class="control-group">
										<label for="pw" class="control-label">Password</label>

										<div class="input-prepend controls">
											<span class="add-on"><i class="icon-lock">&nbsp;</i></span><input type="password" name="pw" id="pw" class="input-xlarge" required="required">
										</div>
									</fieldset>

									<label class="checkbox">
										<input type="checkbox" name="rmb" id="rmb"> Remember me
									</label>

									<?php
									if ($this['abuse']) {
										echo recaptcha_get_html($this['publickey']);
									}
									?>

									<div class="form-actions">
										<button type="submit" class="btn btn-primary pull-left">Sign in</button> <a href="/cpanel" id="fp" class="pull-right" style="margin-top: 6px;"><small>Forgot your password ?</small></a>
									</div>

									<fieldset class="control-group">
										<label for="oi" class="control-label">Sign in using OpenID</label>

										<div class="input-prepend input-append controls" style="white-space: nowrap">
											<span class="add-on"><i class="icon-openid">&nbsp;</i></span><input type="text" name="oi" id="oi"> <button type="submit" class="btn">Go!</button>
										</div>
									</fieldset>
								</form>

								<div id="forceLoginAlert" class="alert alert-info hide">
								  There is another user logged using this username.
								</div>

								<div id="forceLoginContinue" class="hide">
									<label class="checkbox">
									  <input type="checkbox" name="forcelogin" id="forcelogin">Disconnect the other user and let me in.
									</label>

									<div class="form-actions">
										<button id="continueButton" type="submit" class="btn btn-primary pull-left">Continue</button>&nbsp;
										<button class="btn cancelButton" type="button">Cancel</button>
									</div>
								</div>

								<div id="pr" class="hide">

									<form id="prForm" action="/cpanel" method="post">
										<input type="hidden" name="token" value="<?php echo $this['token']; ?>">
										<fieldset class="control-group">
											<legend>Password recovery</legend>
											<label for="email2" class="control-label">Enter the email address for your account.</label>

											<div class="input-prepend controls">
												<span class="add-on"><i class="icon-envelope">&nbsp;</i></span><input type="email" name="email2" id="email2" class="input-xlarge" required="required">
											</div>
										</fieldset>


										<div class="form-actions">
											<button type="submit" class="btn btn-primary pull-left">Reset Password</button>&nbsp;
											<button class="btn cancelButton" type="button">Cancel</button>
										</div>
									</form>

									<div id="prSuccess" class="hide">
										<div class="alert alert-info">
											An email with a confirmation code has been sent.
										</div>
										<p><a class="btn" href="/">Return</a></p>
									</div>

								</div>

							</div>
						</div>
					</div>
				</div>
			</div>

			<hr>

			<footer>
				<p class="fl"><?php echo $_SERVER['HTTP_HOST']; ?></p><p class="fr"><?php echo sqTools::dateISO8601Z(); ?></p>
			</footer>
		</div>


    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo $this->getPath(); ?>js/vendor/jquery-1.9.0.min.js"><\/script>')</script>

		<script src="<?php echo $this->getPath(); ?>js/vendor/gibberish-aes.min.js"></script>
		<script src="<?php echo $this->getPath(); ?>js/vendor/bootstrap.min.js"></script>
		<script src="<?php echo $this->getPath(); ?>js/vendor/jquery.blockUI.js"></script>
		<script src="<?php echo $this->getPath(); ?>js/vendor/jquery.validate.min.js"></script>
		<script src="<?php echo $this->getPath(); ?>js/vendor/jquery.sha1.js"></script>

		<script src="<?php echo $this->getPath(); ?>js/plugins.js"></script>
		<script src="<?php echo $this->getPath(); ?>js/login.js"></script>
	</body>
</html>
