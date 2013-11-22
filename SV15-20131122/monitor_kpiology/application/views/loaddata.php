<!DOCTYPE html>
<html lang="th">
<head>
	<meta charset="utf-8">
	<title></title>
    <script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/jquery-1.7.1.min.js"></script>
    <script language="javascript">
	function loaddata() {
	
	jQuery("#loadBox").hide();
	jQuery(".pic").show();
	
	var TYPE="POST";
	var URL="<?PHP echo config_item("base_url"); ?>/index.php/report_domain_new/loaddata";
	var dataSet='';
	jQuery.ajax({type:TYPE,url:URL,data:dataSet,
		success:function(html){
			
			jQuery("#loadBox").show();
			jQuery("#loadBox").html(html);
			jQuery(".pic").hide();
			
		}
	});
	
	}
	
	loaddata();
	</script>
</head>
<body>
<div class="pic" style="text-align:center;">
  	<div style="height:10px;"></div><img src="<?PHP echo config_item("assets_url"); ?>/img/loading66.gif">
    <div style="font-size:16px;">กรุณารอสักครู่ ระบบกำลังประมวลผล..</div>
</div>
<div id="loadBox">

</div>
</body>
</html>