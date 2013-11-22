<h2>Running Domain <button class="btn btn-warning" type="button" onclick="loaddata();">Refresh</button></h2>
<br/>
<div class="alert alert-info">
	<div class="pull-left">Today : </span></div>
	<div class="pull-left green1">Domain : <span id="g1"><?php echo count($arr_report);?></span></div>
	<div class="pull-left green1">Get Post/Page : <span id="g2"></span></div>
	<div class="pull-left green3">Get Post or Page by Zero : <span id="g3"></span></div>
	<div class="pull-left red2">Get Post/Page by Zero : <span id="g4"></span></div>
	<!--div class="pull-left green3">เมื่อวานซืน : <span id="g3"><?=(isset($result)) ? $result["result3"] : 0 ?></span></div>
	<div class="pull-left red2">ไม่เก็บลงตาราง : <span id="g4"><?=(isset($result)) ? $result["result4"] : 0 ?></span></div>
	<div class="pull-left red1">ตาย : <span id="g5"><?=(isset($result)) ? $result["result5"] : 0 ?></span></div>
	<div class="pull-left other">อื่นๆ : <span id="g6"><?=(isset($result)) ? $result["result6"] : 0 ?></span></div-->
	<br clear="all"/>
	<input type="hidden" value="<?=(isset($result)) ? 1 : 0 ?>" name="result" id="result"/>
</div>

<div class="row" id="page">
    <div class="span12">
    <table class="table table-bordered" id="sort" border="0" cellspacing="5" cellpadding="5">
		<thead>
			<tr>
            	<th>Domain</th>
                <th colspan="3"><?php echo date("Y-m-d");?></th>
                <th colspan="3"><?php echo date("Y-m-d",strtotime("-1 day"));?></th>
                <th colspan="3">AVG(<?php echo date("Y-m-d");?> - <?php echo date("Y-m-d",strtotime("-6 day"));?>)</th>
             </tr>
        	<tr>
            	<th></th>
                <th>Request</th>
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
		 $i['2']=0;
		 $i['3']=0;
		 $i['4']=0;
		 foreach ($arr_report as $key => $value) {
		 
		if($value['sum_page_n']==0 and $value['sum_post_n']==0){
			$class_color1="red2";
			$i['3']++;
		}else if($value['sum_page_n'] > 0 and $value['sum_post_n'] > 0){
			$class_color1="green";
			$i['2']++;
		}else{
			$class_color1="green3";
			$i['4']++;
		}
		if($value['sum_page_y']==0 and $value['sum_post_y']==0){
			$class_color2="red2";
		}else if($value['sum_page_y'] > 0 and $value['sum_post_y'] > 0){
			$class_color2="green";
		}else{
			$class_color2="green3";
		}
		if($value['sum_page_7']==0 and $value['sum_post_7']==0){
			$class_color3="red2";
		}else if($value['sum_page_7'] > 0 and $value['sum_post_7'] > 0){
			$class_color3="green";
		}else{
			$class_color3="green3";
		}
		 ?> 
             <tr>
            	<td><?php echo $value['domain_name'];?></td>
                <td class="<?php echo $class_color1?>"><?php echo number_format($value['sum_request_n']);?></td>
                <td class="<?php echo $class_color1?>"><?php echo number_format($value['sum_page_n']);?></td>
                <td class="<?php echo $class_color1?>"><?php echo number_format($value['sum_post_n']);?></td>
                <td class="<?php echo $class_color2?>"><?php echo number_format($value['sum_request_y']);?></td>
                <td class="<?php echo $class_color2?>"><?php echo number_format($value['sum_page_y']);?></td>
                <td class="<?php echo $class_color2?>"><?php echo number_format($value['sum_post_y']);?></td>
                <td class="<?php echo $class_color3?>"><?php echo number_format($value['sum_request_7']);?></td>
                <td class="<?php echo $class_color3?>"><?php echo number_format($value['sum_page_7']);?></td>
                <td class="<?php echo $class_color3?>"><?php echo number_format($value['sum_post_7']);?></td>
             </tr>
          <? }?>
       	</tbody>
    </table>
    <script language="javascript">
	$(document).ready(function() {
		$('#g2').text(<?php echo $i['2']?>);
		$('#g3').text(<?php echo $i['3']?>);
		$('#g4').text(<?php echo $i['4']?>);
	});
	</script>
</div>