<?php 
date_default_timezone_set("Asia/Bangkok");
$obj = new Monitor_facebook();
$now = date("Y-m-d");
$yes1 = date("Y-m-d",strtotime("-1 day"));
$yes2 = date("Y-m-d",strtotime("-2 day"));
?>
<h2>Bot Running Facebook 
  <!--button class="btn btn-warning" type="button" onclick="loaddata();">Refresh</button--></h2>
<br/>
<div class="alert alert-info">
	<div class="pull-left">รายวัน : </span></div>
	<div class="pull-left green1">วันนี้ : <span id="g1"><?php echo number_format($obj->get_report_date($now));?></span></div>
	<div class="pull-left green2">เมื่อวาน : <span id="g2"><?php echo number_format($obj->get_report_date($yes1));?></span></div>
	<div class="pull-left green3">เมื่อวานซีน : <span id="g3"><?php echo number_format($obj->get_report_date($yes2));?></span></div>
    <div class="pull-left red2">Total Page Facebook : <span id="g4"><?php echo number_format($obj->get_page_facebook());?></span> Page</div>
	<br clear="all"/>
</div>

<div class="row" id="page">
    <div class="span12">
    <table class="table table-bordered" id="sort" border="0" cellspacing="5" cellpadding="5">
		<thead>
			<tr>
            	<th>Start Datetime</th>
                <th>End Datetime</th>
                <th>Count Post</th>
             </tr>
         </thead>
         <tbody>   
         <?php 
		 foreach ($arr_report as $key => $value) {
		 $start_date = $obj->timemin($value['start_date'].' + 7 hour');
		 $end_date = $obj->timemin($value['end_date'].' + 7 hour');
		 ?> 
             <tr>
            	<td><?php echo $obj->dateThaiEngTimeShort($start_date,1);?></td>
                <td><?php echo $obj->dateThaiEngTimeShort($end_date,1);?></td>
                <td><div style="text-align:center"><?php echo number_format($value['sum_post']);?></div></td>
             </tr>
          <?php }?>
       	</tbody>
    </table>
</div>
<META HTTP-EQUIV='Refresh' CONTENT='600; URL=<?=base_url()?>index.php/monitor_facebook'> 