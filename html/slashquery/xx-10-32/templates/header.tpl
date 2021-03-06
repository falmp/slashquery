 <!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
			<meta charset="utf-8">
			<title><?php echo $this->router->site; ?></title>
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
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
				<a class="brand" href="/"><img src="<?php echo $this->getPath();?>img/logo-sq.png" alt="slashquery"></a>
        <div class="nav-collapse">
          <ul class="nav pull-right">
            <li class="dropdown">
              <a href="/"><i class="icon-home icon-white"></i> Home</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
