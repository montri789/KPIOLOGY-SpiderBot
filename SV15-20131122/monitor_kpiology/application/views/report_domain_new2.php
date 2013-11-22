<?PHP 
date_default_timezone_set("Asia/Bangkok");
?>
<h2>Running <?php echo count($arr_report);?> Domain <button class="btn btn-warning" type="button" onclick="loaddata();" style="display:none">Refresh</button></h2>
<br/>
<div class="alert alert-info" style="margin-bottom:1px">
	<div class="pull-left">Today : </span></div>
	<div class="pull-left green1">Post/Page : <span id="g2"></span></div>
	<div class="pull-left green3">Post or Page zero : <span id="g3"></span></div>
	<div class="pull-left red2">Post && Page zero : <span id="g4"></span></div>
    <div class="pull-left red1">No Request : <span id="g5"></span></div>
	<!--div class="pull-left green3">เมื่อวานซืน : <span id="g3"><?=(isset($result)) ? $result["result3"] : 0 ?></span></div>
	<div class="pull-left red2">ไม่เก็บลงตาราง : <span id="g4"><?=(isset($result)) ? $result["result4"] : 0 ?></span></div>
	<div class="pull-left red1">ตาย : <span id="g5"><?=(isset($result)) ? $result["result5"] : 0 ?></span></div>
	<div class="pull-left other">อื่นๆ : <span id="g6"><?=(isset($result)) ? $result["result6"] : 0 ?></span></div-->
	<br clear="all"/>
	<!--input type="hidden" value="<?=(isset($result)) ? 1 : 0 ?>" name="result" id="result"/-->
</div>

<div class="alert alert-info" style="margin-bottom:1px">
	<div class="pull-left">Yesterday : </span></div>
	<div class="pull-left green1">Post/Page : <span id="y2"></span></div>
	<div class="pull-left green3">Post or Page zero : <span id="y3"></span></div>
	<div class="pull-left red2">Post && Page zero : <span id="y4"></span></div>
    <div class="pull-left red1">No Request : <span id="y5"></span></div>
    <br clear="all"/>
</div>

<div class="alert alert-info">
	<div class="pull-left">SUM 7 DAY : </span></div>
	<div class="pull-left green1">Post/Page : <span id="a2"></span></div>
	<div class="pull-left green3">Post or Page zero : <span id="a3"></span></div>
	<div class="pull-left red2">Post && Page zero : <span id="a4"></span></div>
    <div class="pull-left red1">No Request : <span id="a5"></span></div>
    <br clear="all"/>
</div>

<div class="row" id="page">
    <div class="span12">
    <table class="table table-bordered tablesorter running-domain" id="sort" border="0" cellspacing="5" cellpadding="5">
		<thead>
			<tr>
		<th>ID</th>
            	<th>Domain</th>
		<th>Last Insert Date</th>
		<th>Latest Fetch</th>
		<th class="parse_date">Parse Date</th>
                <th colspan="3"><?php echo date("Y-m-d");?></th>
                <th colspan="3"><?php echo date("Y-m-d",strtotime("-1 day"));?></th>
                <th colspan="3">SUM(<?php echo date("Y-m-d");?> - <?php echo date("Y-m-d",strtotime("-6 day"));?>)</th>
             </tr>
        	<tr>
            	<th></th>
		<th></th>
		<th></th>
		<th ></th>
		<th class="parse_date"></th>
                <th >Request</th>
                <th>Page</th>
                <th>Post</th>
                <th>Request</th>
                <th>Page</th>
                <th>Post</th>
                <th>Request</th>
                <th>Page</th>
                <th>Post</th>
             </tr>
         </thead>
         <tbody>   
         <?php 
		 
		 $i['2']=0;		 $i['3']=0;		 $i['4']=0;		 $i['5']=0;
		 $y['2']=0;		 $y['3']=0;		 $y['4']=0;		 $y['5']=0;
		 $a7['2']=0;	 $a7['3']=0;	 $a7['4']=0;	 $a7['5']=0;
		 $now = date("Y-m-d");
		 $yes = date("Y-m-d",strtotime("-1 day"));
		 $avg = date("Y-m-d",strtotime("-6 day"));
		 $obj = new Report_domain_new();
	

		 foreach ($arr_report as $key => $value) {
		 $objNowValue = $obj->select_date_one($now,$value['domain_name']);
		 $objYesValue = $obj->select_date_yes($yes,$value['domain_name']);
		 $objsevenValue = $obj->select_date_seven($avg,$now,$value['domain_name']);
		 
		 $objFetchPage = $obj->select_fetch_page($value['domain_name']);
		 
		 
		
		if($objNowValue['sum_request_n']==0 and $objNowValue['sum_page_n']==0 and $objNowValue['sum_post_n']==0){
			$class_color1="red1";
			$i['5']++;
		}else if($objNowValue['sum_page_n']==0 and $objNowValue['sum_post_n']==0){
			$class_color1="red2";
			$i['3']++;
		}else if($objNowValue['sum_page_n'] > 0 and $objNowValue['sum_post_n'] > 0){
			$class_color1="green";
			$i['2']++;
		}else{
			$class_color1="green3";
			$i['4']++;
		}
		
		if($objYesValue['sum_request_y']==0 and $objYesValue['sum_page_y']==0 and $objYesValue['sum_post_y']==0){
			$class_color2="red1";
			$y['5']++;
		}else if($objYesValue['sum_page_y']==0 and $objYesValue['sum_post_y']==0){
			$class_color2="red2";
			$y['3']++;
		}else if($objYesValue['sum_page_y'] > 0 and $objYesValue['sum_post_y'] > 0){
			$class_color2="green";
			$y['2']++;
		}else{
			$class_color2="green3";
			$y['4']++;
		}
		
		if($objsevenValue['sum_request_7']==0 and $objsevenValue['sum_page_7']==0 and $objsevenValue['sum_post_7']==0){
			$class_color3="red1";
			$a7['5']++;
		}else if($objsevenValue['sum_page_7']==0 and $objsevenValue['sum_post_7']==0){
			$class_color3="red2";
			$a7['3']++;
		}else if($objsevenValue['sum_page_7'] > 0 and $objsevenValue['sum_post_7'] > 0){
			$class_color3="green";
			$a7['2']++;
		}else{
			$class_color3="green3";
			$a7['4']++;
		}
		
		 ?> 
             <tr>
		<td class="id"><?PHP echo $objFetchPage['id']; ?></td>
            	<td class="domain_name"><?php echo $value['domain_name'];?></td>
		<td class="last_insert_date <?PHP echo $obj->get_color($objFetchPage['last_insert_date']); ?>"><?PHP echo $objFetchPage['last_insert_date']; ?></td> 
		<td class="latest_fetch <?PHP echo $obj->get_color($objFetchPage['latest_fetch']); ?>"><?PHP echo $objFetchPage['latest_fetch']; ?></td>
		<td class="parse_date <?PHP echo $obj->get_color($objFetchPage['parse_date']); ?>"><?PHP echo $objFetchPage['parse_date']; ?></td>
                <td class="<?php echo $class_color1?>"><?php echo number_format($objNowValue['sum_request_n']);?></td>
                <td class="<?php echo $class_color1?>"><?php echo number_format($objNowValue['sum_page_n']);?></td>
                <td class="<?php echo $class_color1?>"><?php echo number_format($objNowValue['sum_post_n']);?></td>
                <td class="<?php echo $class_color2?>"><?php echo number_format($objYesValue['sum_request_y']);?></td>
                <td class="<?php echo $class_color2?>"><?php echo number_format($objYesValue['sum_page_y']);?></td>
                <td class="<?php echo $class_color2?>"><?php echo number_format($objYesValue['sum_post_y']);?></td>
                <td class="<?php echo $class_color3?>"><?php echo number_format($objsevenValue['sum_request_7']);?></td>
                <td class="<?php echo $class_color3?>"><?php echo number_format($objsevenValue['sum_page_7']);?></td>
                <td class="<?php echo $class_color3?>"><?php echo number_format($objsevenValue['sum_post_7']);?></td>
             </tr>
          <?php }?>
       	</tbody>
    </table>
    <script language="javascript">
	
	$(document).ready(function() {
		$('#g2').text(<?php echo $i['2']?>);
		$('#g3').text(<?php echo $i['3']?>);
		$('#g4').text(<?php echo $i['4']?>);
		$('#g5').text(<?php echo $i['5']?>);
		$('#y2').text(<?php echo $y['2']?>);
		$('#y3').text(<?php echo $y['3']?>);
		$('#y4').text(<?php echo $y['4']?>);
		$('#y5').text(<?php echo $y['5']?>);
		$('#a2').text(<?php echo $a7['2']?>);
		$('#a3').text(<?php echo $a7['3']?>);
		$('#a4').text(<?php echo $a7['4']?>);
		$('#a5').text(<?php echo $a7['5']?>);
	});
</script> 
</div> 
<META HTTP-EQUIV='Refresh' CONTENT='600; URL=<?=base_url()?>index.php/report_domain_new'> 