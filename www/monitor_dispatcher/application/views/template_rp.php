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
  
	<?PHP $groups = $this->session->userdata("wr_groups");?>
	<?PHP if($groups != NULL){ ?>
    <div class="navbar navbar-inverse navbar-fixed-top">	
      <div class="navbar-inner">
        <div class="container">
          <a class="btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar">&nbsp;</span>
            <span class="icon-bar">&nbsp;</span>
            <span class="icon-bar">&nbsp;</span>
          </a>
          <a class="brand" href="<?=base_url();?>">Monitor Kpiology</a>
	 
            <ul class="nav">
	        <li <?PHP if($uri == "result") { ?> class="active" <?PHP } ?>>
                <a href="<?=base_url()?>index.php/result">Home</a>
              </li>
              <li <?PHP if($uri == "" || $uri == "welcome" || $uri == "report") { ?> class="active" <?PHP } ?>>
                <a href="<?=base_url()?>index.php/welcome">Matchs</a>
              </li>
              <li <?PHP if($uri == "page") { ?> class="active" <?PHP } ?> >
                <a href="<?=base_url()?>index.php/page">Fetch Pages</a>
              </li>
	      <li <?PHP if($uri == "reporting") { ?> class="active" <?PHP } ?> >
                <a href="<?=base_url()?>index.php/reporting">Bot Running Pages</a>
              </li>
            </ul>
        
        </div>
      </div>
    </div>

	<?PHP } else { ?>
	 
	<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar">&nbsp;</span>
            <span class="icon-bar">&nbsp;</span>
            <span class="icon-bar">&nbsp;</span>
          </a>
          <a class="brand" href="<?=base_url();?>">Monitor Kpiology</a>
          <ul class="nav">
               <li <?PHP if($uri == "result") { ?> class="active" <?PHP } ?>>
                <a href="<?=base_url()?>index.php/result">Home</a>
              </li>
	      <li <?PHP if($uri == "" || $uri == "welcome" || $uri == "report") { ?> class="active" <?PHP } ?>>
                <a href="<?=base_url()?>index.php/welcome">Matchs</a>
              </li>
              <li <?PHP if($uri == "page") { ?> class="active" <?PHP } ?> >
                <a href="<?=base_url()?>index.php/page">Fetch Pages</a>
              </li>
	      <li <?PHP if($uri == "reporting") { ?> class="active" <?PHP } ?> >
                <a href="<?=base_url()?>index.php/reporting">Bot Running Pages</a>
              </li>
            </ul>
	  
	  <div class="nav-collapse">
            <ul class="nav">
            </ul>
                <ul class="nav pull-right">
                    <li>
                        <!--<form style="margin:0 0;padding:10px 10px 11px;" class="form-inline" method="post">
                          <input name="username" style="padding:0 0; height: 18px;" type="text" class="input-small" placeholder="Username">
                          <input name="password" style="padding:0 0; height: 18px;" type="password" class="input-small" placeholder="Password">
                          <button name="submit" style="margin: 0 0; padding: 1px 4px;" type="submit" class="btn">Go</button>
                        </form>-->
                    </li>
                </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
	<?PHP } ?>

    <br/>

    <div class="container">
        <?=$module;?>
      <hr>

      <footer>
        <p>&copy; Thoth Media Co.,Ltd.</p>
      </footer>
    </div> 
    
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-tab.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-modal.js"></script>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/bootstrap-dropdown.js"></script>
    <script type="text/javascript">
    $(function(){
      $('#menu-<?=$this->uri->segment(1);?>').addClass('active');
    });
    </script>

</body> 
</html>