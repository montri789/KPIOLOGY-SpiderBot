<h2>Bot Running Pages</h2>
<br/>
<?php $num_row_bot = count($dir_bot);?>
<div class="alert alert-info">
	<div class="pull-left green1">Bot : <span id="g1"><?php echo $num_row_bot; ?></span></div>
	<div class="pull-left <?php if($bot_running <= ($num_row_bot - round($num_row_bot*0.25))){
		echo "red2";
		}elseif($bot_running <= ($num_row_bot - round($num_row_bot*0.50))){
		echo "red1";
		}else{
		echo "green1";
		}?>">Running : <span id="g1"><?php echo $bot_running; ?></span></div>
	<div class="pull-left green1">Total post : <span id="g1"><?php echo number_format($total_post);?></span></div>
	<div class="pull-left green1">Avg. post per day : <span id="g1"><?php echo number_format($total_post/3);?></span></div>
	<div class="pull-left green1">Total page : <span id="g2"><?php echo number_format($total_page);?></span></div>
	<div class="pull-left green1">Avg. page per day : <span id="g1"><?php echo number_format($total_page/3);?></span></div>
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
				<th>Date</th>
				<th>Post</th>
				<th>Page</th>
			</tr>
		</thead>
		<tbody>
			<?php
			    
			    for($i=0;$i<$num_row_bot;$i++){
			?>
			<tr class= alert-info>
			    <td colspan=3><?php echo $dir_bot[$i]["name"]; ?> </td>
			    
			</tr>
		        <?PHP
			$chk_row = 0;
			$sum_post = 0;
			$sum_page = 0;
			foreach($var_row as $row){
				if($dir_bot[$i]["name"]==$row["bot"]){
			    ?>
			<tr id="id-<?=$row["bot"];?>">
				<td class="website_name" ><?=$row["date"];?> <font color="#CCCCCC">[<?=$row["ip"];?>]</font></td>
				<td class="last_insert_date"><?=number_format($row["post_count"]);?></td>
				<td class="latest_fetch"><?=number_format($row["page_count"]);?></td>
			</tr>
			<?PHP 	$chk_row++; $sum_post = $sum_post + $row["post_count"]; $sum_page = $sum_page + $row["page_count"];}
			    }

			    if($chk_row == 0){
				?>
				<tr>
				    <td colspan=3 class="last_insert_date red1" style=text-align:center;>No Data.</td>
				</tr>
				<?php
			    
			    }else{
				?>
				<tr>
				    <td class="website_name" >Total :</td>
				    <td class="last_insert_date green"><?php echo number_format($sum_post);?></td>
				    <td class="last_insert_date green"><?php echo number_format($sum_page);?></td>
				</tr>
				<?php
				
			    }
			
			} ?>
			
		</tbody>
	</table>
</div>