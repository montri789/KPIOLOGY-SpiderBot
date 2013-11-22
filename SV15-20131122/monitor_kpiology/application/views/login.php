<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Login Monitor Kpiology</title>

	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background: #477fae;
    	color: #000;
    	font: 13px/20px normal Helvetica, Arial, sans-serif;
		margin:0;
		padding:0;
	}

	
	#container{
		margin: 10px;
		//border: 1px solid #D0D0D0;
		//-webkit-box-shadow: 0 0 8px #D0D0D0;
		width:500px;
		width:300px;
	}
	#header {
    background: #193356;
    color: #fff;
	width:100%;
	font-size:25px;
	padding:20px
	}
	#footer {
    background: #193356;
    color: #fff;
	width:100%;
	font-size:15px;
	padding:5px;
	}
	#content { 
	text-align:center;
	border: 1px solid #f1f1f1;
	-webkit-box-shadow: 0 0 8px #f1f1f1;
	width:170px;
	margin: 10% auto;
	 }
	</style>
</head>
<body>
<div id="header">Monitor Kpiology</div>
<div id="content">
    <a href="<?php echo site_url('login_other/google_openid_signin'); ?>">
	<img style="margin-top:5px;" src="<?php echo base_url(); ?>assets/img/google_connect_button.png" alt="google open id" border="0"/>
</a>
</div>
<div id="footer">Copyright © 2013 Thothmedia</div>
</body>
</html>