<h2>Running Domain</h2>
<br/>
<div class="alert alert-info">
	<div class="pull-left">Today : </span></div>
	<div class="pull-left green1">Domain : <span id="g1"><?php echo $domain; ?></span></div>
	<div class="pull-left green1">Get Post/Page : <span id="g1"><?php echo number_format($arr_color_show["green"]);?></span></div>
	<div class="pull-left green3">Get Post or Page by Zero : <span id="g1"><?php echo number_format($arr_color_show["yello"]);?></span></div>
	<div class="pull-left red2">Get Post/Page by Zero : <span id="g1"><?php echo number_format($arr_color_show["red"]); ?></span></div>
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
				<?php for($i=0;$i<=2;$i++){
					if($i<2){	
				?>
				<th>Page/Post (<?php echo $dateEnd = date("Y-m-d",strtotime("-".$i." day"));?>)</th>
				<?php }else{
					?><th>Avg. 1 week</th><?php
					}
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			$num_domain = count($arr_domain);
			for($j=1;$j<$num_domain;$j++){?>
			<tr>
				<td><?php echo $arr_domain[$j]["domain_name"];?></td>
				<?php for($i=0;$i<=2;$i++){
					$num_domain_date = count($arr_bot_show[$i]);
					$chk_col = 0;
				?>
				
				<?php
					for($k=0;$k<$num_domain_date;$k++){
						if($arr_domain[$j]["domain_name"] == $arr_bot_show[$i][$k]["domain_name"]){
							
							if($arr_bot_show[$i][$k]["post_count"]==0 and $arr_bot_show[$i][$k]["page_count"]==0){
								$class_color="red2";
							}else if($arr_bot_show[$i][$k]["post_count"] > 0 and $arr_bot_show[$i][$k]["page_count"] > 0){
								$class_color="green";
							}else{
								$class_color="green3";
							}
							
							?><td class="<?php echo $class_color?>"><?php
							echo $arr_bot_show[$i][$k]["page_count"]." / ".$arr_bot_show[$i][$k]["post_count"];
							?></td><?php
							$chk_col = 1;
						}
					}
					if($chk_col==0){
						?><td class="red1"></td><?php
					}
				}?>
			</tr>
			<?php }?>
		</tbody>
	</table>
</div>