<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Monitor Kpiology Matchs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link type="text/css" rel="stylesheet" href="<?PHP echo config_item("assets_url"); ?>/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="<?PHP echo config_item("assets_url"); ?>/css/bootstrap-responsive.css" />
    <link type="text/css" rel="stylesheet" href="<?PHP echo config_item("assets_url"); ?>/css/style.css" />
    <link type="text/css" rel="stylesheet" href="<?PHP echo config_item("assets_url"); ?>/css/style_mote.css" />

	<link rel="shortcut icon" href="http://www.thothmedia.com/wp-content/themes/thoth/favicon.png">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/sortable/sortable.js"></script>    
    <!--<script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/script.js"></script>-->

    <!--2012-11-14>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/jquery-1.8.2.min.js"></script>
    <!--script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-modal.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-collapse.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-tab.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-button.js"></script>
    <link type="text/css" href="<?PHP echo config_item('assets_url'); ?>/js/jquery-ui-1.7.3.custom/development-bundle/themes/ui-lightness/ui.all.css" rel="stylesheet" />
    <!--2012-11-14-->
    
    <link type="text/css" rel="stylesheet" href="<?PHP echo config_item("assets_url"); ?>/js/jquery-ui-1.7.3.custom/css/ui-lightness/jquery-ui-1.7.3.custom.css" />
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/jquery-ui-1.7.3.custom/js/jquery-ui-1.7.3.custom.min.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/jquery-ui-1.7.3.custom/development-bundle/ui/ui.datepicker.js"></script>
        
</head> 
<body>
  <?PHP  $uri = $this->uri->segment(1); ?>
  
	 
      <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="<?=base_url();?>">Monitor Kpiology</a>
          <div class="nav-collapse in collapse" style="height: auto;">
            <ul class="nav">
               <li <?PHP if($uri == "" || $uri == "result") { ?> class="active" <?PHP } ?>>
                <a href="<?PHP echo base_url()?>index.php/result">Home</a>
              </li>
	      <li <?PHP if($uri == "match" || $uri == "report") { ?> class="active" <?PHP } ?>>
                <a href="<?PHP echo base_url()?>index.php/match">Matchs Kpiology</a>
              </li>
	      <li <?PHP if($uri == "match_warroom" || $uri == "report_warroom") { ?> class="active" <?PHP } ?>>
                <a href="<?PHP echo base_url()?>index.php/match_warroom">Matchs Warroom</a>
              </li>
              <li <?PHP if($uri == "page") { ?> class="active" <?PHP } ?> >
                <a href="<?PHP echo base_url()?>index.php/page">Fetch Pages</a>
              </li>
	      <li <?PHP if($uri == "reporting") { ?> class="active" <?PHP } ?> >
                <a href="<?PHP echo base_url()?>index.php/reporting">Bot Running Pages</a>
              </li>
	      <li <?PHP if($uri == "report_domain_new") { ?> class="active" <?PHP } ?> >
                <a href="<?PHP echo base_url()?>index.php/report_domain_new">Running Domain</a>
              </li>
	      <li <?PHP if($uri == "twitter") { ?> class="active" <?PHP } ?> >
                <a href="<?PHP echo base_url()?>index.php/twitter">Twitter</a>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
      
      
      <div style="width:95%; text-align:right; margin:10px auto;"><?PHP 
		  		$arr = $this->session->userdata('logged_in');
		  		echo $arr['first']." ".$arr['last'];
		  ?> <a href="<?php echo site_url('logout'); ?>">Logout</a></div>
    </div>

    <br/>

    <div class="container">
        <?PHP echo $module;?>
      <hr>

      <footer>
        <p>&copy; Thoth Media Co.,Ltd.</p>
      </footer>
    </div> 
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-transition.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-scrollspy.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-button.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-collapse.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-carousel.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-typeahead.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-tab.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-modal.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-dropdown.js"></script>
    <script type="text/javascript">
    $(function(){
      $('#menu-<?PHP echo $this->uri->segment(1);?>').addClass('active');
    });
    </script>

</body> 
</html>