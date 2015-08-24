<?php
require_once('config.inc.php');
require_once('php/lg/common.php');
require_once('php/mileslyrics.class.php');
$database = array(_DB_HOST, _DB_USER, _DB_PASS, _DB_DATABASE);
$milesLyrics = new MilesLyrics($database);
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>milesLyrics</title>
	<link rel="stylesheet" href="css/bootstrap-slate.css" style="text/css" media="all" />
	<link rel="stylesheet" href="css/bootswatch.css" style="text/css" media="all" />
	<link rel="stylesheet" href="css/style.css" style="text/css" media="all" />
	<script type="text/javascript"><?php echo $milesLyrics->javascript; ?></script>
</head>
<body>
	
	<nav class="navbar navbar-default">
	  <div class="container-fluid">
	    <div class="navbar-header">
	      <a class="navbar-brand" href="#"><?php echo _SITE_NAME; ?></a>
	    </div>
	  </div>
	</nav>

	<div id="wrapper" style="display:none;">
		<div id="menu"><?php echo $milesLyrics->templateMainMenu(); ?></div>
		<div id="lyrics" class="container"></div>
	</div>
	
	<div id="admin">
		<ul class="nav nav-tabs">
		  <li class=""><a aria-expanded="false" href="#admin_home" data-toggle="tab">Home</a></li>
		  <li class=""><a aria-expanded="true" href="#admin_artist" data-toggle="tab">Artist</a></li>
		  <li class=""><a aria-expanded="true" href="#admin_album" data-toggle="tab">Album</a></li>
		  <li class="active"><a aria-expanded="true" href="#admin_track" data-toggle="tab">Track</a></li>
		</ul>
		<div id="myTabContent" class="tab-content">
		  <div class="tab-pane fade" id="admin_home">
		    <p>Bienvenue dans l'admin</p>
		    <p><input type="button" id="close_admin" value="X" /></p>
		  </div>
		  <div class="tab-pane fade" id="admin_artist">
		    <div id="createArtist"><?php echo $milesLyrics->templateCreateArtist(); ?></div>
		  </div>
		  <div class="tab-pane fade" id="admin_album">
		    <div id="createAlbum"><?php echo $milesLyrics->templateCreateAlbum(); ?></div>
		  </div>
		  <div class="tab-pane fade active in" id="admin_track">
		    <div id="createTracks"><?php echo $milesLyrics->templateCreateTracks(); ?></div>
		  </div>
		</div>
		<!--
		<input type="button" id="close_admin" value="X" />
		<div id="createArtist"><?php echo $milesLyrics->templateCreateArtist(); ?></div>
		<div id="createAlbum"><?php echo $milesLyrics->templateCreateAlbum(); ?></div>
		<div id="createTracks"><?php echo $milesLyrics->templateCreateTracks(); ?></div>
		-->
	</div>

	<script type="text/javascript" src="js/jquery.1.10.2.min.js"></script>
	<script type="text/javascript" src="js/jwerty.js"></script>
	<script type="text/javascript" src="js/ajaxfileupload.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript" src="js/bootswatch.js"></script>
	<script type="text/javascript" src="js/ready.js"></script>
	<script type="text/javascript" src="js/mileslyrics.js"></script>
</body>
</html>
