<h2>Bot Matchs Warrooom</h2>
<br/>
<div class="alert alert-info">
	<div class="pull-left">Total Client : <?=$count_client;?></div>	
	<div class="pull-left green3">Match Today : <?=$count_all;?></div>
	<div class="pull-left green1">Complete : <?=$count_complete;?></div>	
	<div class="pull-left red1">Fail : <?=$count_fail;?></div>
	<br clear="all"/>
</div>

<div class="row" id="page">
    <div class="span12">
	<table class="table table-bordered table-striped tablesorter" id="sort" border="0" cellspacing="5" cellpadding="5">
	  <tr>
	    <th width="80px">Client ID</th>
	    <th width="250px">Client Name</th>
	    <th width="80px">แมทข้อมูลถึงวันที่</th>
	    <th width="80px">วันที่รันแมทเก่าสุด</th>
	    <th width="80px">วันที่รันแมทล่าสุด</th>
	    <th width="80px">Subject</th>
	    <!--<th width="80px">All Subject</th>
	    <th width="80px">Match Complete</th>
	    <th width="80px">Sum Match</th>
	    <th width="80px">Sum Match (Month)</th>
	    <th width="80px">Match Fail</th>
	    <th width="80px">Matching...</th>-->
	  </tr>
	  <?PHP
	  
		$i=0;
		foreach($rp as $row){
		    $i++;
		    
		    $match_to_diff =(strtotime(date("Y-m-d")) - strtotime($row["match_to_last"]))/( 60 * 60 * 24 );
		    $min_date_diff =(strtotime(date("Y-m-d")) - strtotime($row["min_date"]))/( 60 * 60 * 24 );
		    $max_date_diff =(strtotime(date("Y-m-d")) - strtotime($row["max_date"]))/( 60 * 60 * 24 );
		    //echo 'max_date_diff ='.$max_date_diff;
		    
		    $match_color ="default";
		    
		    if($match_to_diff == 0){
			$match_color ="green";
		    }else if($match_to_diff == 1){
			$match_color ="green2";
		    }else if($match_to_diff == 2){
			$match_color ="green3";
		    }else{
			$match_color ="red1";
		    }
		    
		    $mindate_color ="default";
		    
		    if($min_date_diff == 0){
			$mindate_color ="green";
		    }else if($min_date_diff == 1){
			$mindate_color ="green2";
		    }else if($min_date_diff == 2){
			$mindate_color ="green3";
		    }else{
			$mindate_color ="red1";
		    }
		    
		    $maxdate_color ="default";
		    
		    if($max_date_diff == 0){
			$maxdate_color ="green";
		    }else if($max_date_diff == 1){
			$maxdate_color ="green2";
		    }else if($max_date_diff == 2){
			$maxdate_color ="green3";
		    }else{
			$maxdate_color ="red1";
		    }
		
	    ?>
	  <tr id="id_<?=$row["id"]?>">
	    <td><?=$row["id"];?></td>
	    <!--<td><?=anchor('report_warroom/matchs/'.$row["id"], $row["name"], 'title="Show Matchs"');?></td>-->
	    <td><a href="javascript:void(0);" id="<?=$row["id"];?>" class="chart"><?=$row["name"];?></a>
	    <td style="text-align: center;" class="<?=$match_color;?>"><?=$row["match_to_last"];?></td>
	    <td style="text-align: center;" class="<?=$mindate_color;?>"><?=$row["min_date"];?></td>
	    <td style="text-align: center;" class="<?=$maxdate_color;?>"><?=$row["max_date"];?></td>
	    <td style="text-align: center;"><?=$row["count_subject"];?></td>
	    <!--<td style="text-align: center;"><?=$row["count_subject"];?></td>
	    <td style="text-align: center;" class="font_green"><?= empty($row["match_complete"])? "" : $row["match_complete"]; ?></td>
	    <td style="text-align: center;"><?=$row["sum_match"];?></td>
	    <td style="text-align: center;"><?=$row["sum_match_month"];?></td>
	    <td style="text-align: center;" class="font_red"><?= empty($row["match_fail"])? "" : $row["match_fail"]; ?></td>
	    <td style="text-align: center;"><?= empty($row["matching"])? "" : $row["matching"]; ?></td>-->
	  </tr>
	  <?PHP } ?>
	</table>
    </div>
</div>


<div id="graph">Loading graph...</div>
<input type="hidden" value="2" id="client_id" />

<div>Last Update : <?=$last_update;?> <button name="btnRF" id="btnRF">Refresh</button></div>
<img id="imgs" src="<?PHP echo config_item("assets_url"); ?>/img/imgload01.gif" />

<script type="text/javascript" src="<?PHP echo config_item("assets_url"); ?>/js/jscharts.js"></script>
<script type="text/javascript">
	
	$(function(){
		
		$("#imgs").hide();
					
		$('a.chart').click(function(){
			var id = $(this).attr('id');			
			$('#client_id').val(id);
			loadData();				
		});
		
		$('#btnRF').click(function() {
			$("#imgs").fadeIn();
			updateData();
		});
		
		function updateData(){				
			
			var url = '<?=base_url();?>index.php/match_warroom/updateData';
			
			var data = {};
			console.log('0');
			$.get(url,data,function(res){
				if(res !=''){
					location.reload();		
				}				
			});
		}
				
		function loadData(){				
			
			var url = '<?=base_url();?>index.php/match_warroom/getData';
			var data = {client_id:$('#client_id').val()};
			console.log('0');
			$.get(url,data,function(res){
				if(res !=''){
					createChart(res);		
				}				
			});
		}
		
		function createChart(myData){

			//var colors = ['#60B6F0', '#60B6F0', '#60B6F0', '#60B6F0', '#60B6F0', '#60B6F0', '#60B6F0', '#60B6F0', '#60B6F0', '#60B6F0'];
			var myChart = new JSChart('graph', 'bar');

			myData = myData.split('#');
			var myData1 = Array();  
			$.each(myData,function(i){
			     myData1[i] = eval(myData[i]);
			});

			myChart.setDataArray(myData1);
			
			//var title = $('#id_'+$('#client_id').val()+' a.chart').text();
			var title = $('#id_'+$('#client_id').val()+' a.chart').text();
			
			
			
			//console.log(title);
			
			//myChart.colorizeBars(colors);
			myChart.setTitle(title);
			myChart.setTitleColor('#8E8E8E');
			myChart.setAxisNameX('');
			myChart.setAxisNameY('');
			myChart.setAxisColor('#C4C4C4');
			myChart.setAxisNameFontSize(16);
			myChart.setAxisNameColor('#999');
			myChart.setAxisValuesColor('#777');
			myChart.setAxisColor('#B5B5B5');
			myChart.setAxisWidth(1);
			myChart.setBarValuesColor('#2F6D99');
			myChart.setBarOpacity(0.5);
			myChart.setAxisPaddingTop(60);
			myChart.setAxisPaddingBottom(40);
			myChart.setAxisPaddingLeft(45);
			myChart.setTitleFontSize(11);
			myChart.setBarBorderWidth(0);
			myChart.setBarSpacingRatio(50);
			myChart.setBarOpacity(0.9);
			myChart.setFlagRadius(6);
			//myChart.setTooltip(['North America', 'U.S.A and Canada']);
			myChart.setTooltipPosition('nw');
			myChart.setTooltipOffset(3);
			myChart.setSize(800, 321);
			//myChart.setBackgroundImage('chart_bg.jpg');
			myChart.draw();	
		}
		
		loadData();
	});
	
</script>