<h2>Fetch Pages</h2>
<br/>
<div class="alert alert-info">
	<div class="pull-left">Total Website : <span id="total"></span></div>	
	<div class="pull-left green1">วันนี้ : <span id="g1"><?PHP echo (isset($result)) ? $result["result1"] : 0 ?></span></div>
	<div class="pull-left green2">เมื่อวานนี้ : <span id="g2"><?PHP echo (isset($result)) ? $result["result2"] : 0 ?></span></div>
	<div class="pull-left green3">เมื่อวานซืน : <span id="g3"><?PHP echo (isset($result)) ? $result["result3"] : 0 ?></span></div>
	<div class="pull-left red2">ไม่เก็บลงตาราง : <span id="g4"><?PHP echo (isset($result)) ? $result["result4"] : 0 ?></span></div>
	<div class="pull-left red1">ตาย : <span id="g5"><?PHP echo (isset($result)) ? $result["result5"] : 0 ?></span></div>
	<div class="pull-left other">อื่นๆ : <span id="g6"><?PHP echo (isset($result)) ? $result["result6"] : 0 ?></span></div>
	<br clear="all"/>
	<input type="hidden" value="<?PHP echo (isset($result)) ? 1 : 0 ?>" name="result" id="result"/>
</div>

<div class="row" id="page">
    <div class="span12">
	<table class="table table-bordered table-striped tablesorter" id="sort" border="0" cellspacing="5" cellpadding="5">
		<thead>
			<tr>
				<th>#</th>
				<th>Website ID</th>
				<th>Website Name</th>
				<th>config</th>
				<th>Last Insert Date</th>
				<th>Latest Fetch</th>
				<th>Parse Date</th>
			</tr>
		</thead>
		<tbody><?PHP $i=0; foreach($website as $row){ $i++; ?>
			<tr id="id-<?PHP echo $row["id"];?>">
				<td><?PHP echo $i;?></td>
				<td class="website_id"><?PHP echo $row["id"];?></td>
				<td class="website_name" ><?PHP echo $row["name"];?></td>
				<td class="config_filename"><?PHP echo $row["config_filename"]; ?></td>
				<td class="last_insert_date"></td>
				<td class="latest_fetch"></td>
				<td class="parse_date"></td>
			</tr><?PHP } ?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	$(function(){
		
		var row = 50;
		var i = 0;
		var loop = 0;
		var rowCount = 0;
			
		function loadDate(){
			
			var currentTime = new Date();
			var month = (currentTime.getMonth() + 1)+'';
			var day = currentTime.getDate()+'';
			var year = currentTime.getFullYear();
			
			month = (month.length == 1) ? '0'+month : month;
			day = (day.length == 1) ? '0'+day : day;
			
			var currentDate = year+'-'+month+'-'+day;
			
			var url = '<?PHP echo base_url();?>index.php/page/website';
			
			rowCount = $('#page table tbody tr').length;
			loop = Math.ceil(rowCount/row);
			
			if(i < loop){
				
				var idArray = new Array();
				var arrayIndex = 0;
				for(var x = (i*row) ; x < (i*row+row); x++){
					if(x < rowCount){
						var websiteID = $('#page table tbody tr:eq('+x+') td.website_id').text();					
						idArray[arrayIndex++] = websiteID;
					}
				}
				
				var data = {id:idArray.join(',')};
				$.get(url,data,function(res){
					if(res != ''){
						$.each(res,function(index){
							var row = res[index];
							
							if(row.last_insert_date != null && row.last_insert_date != 0){
								var insert_date  = row.last_insert_date.split(' ');
								insert_date = insert_date[0];
								
								var dateDiff = DateDiff(new Date(currentDate),new Date(insert_date));
								
								if(insert_date == currentDate){
									$('#id-'+row.id+' td.last_insert_date').addClass('green');
									$('#id-'+row.id+' td.website_name').addClass('font_green');
									
									$('#id-'+row.id).addClass('g1');
								}else if(dateDiff == 1){
									$('#id-'+row.id+' td.last_insert_date').addClass('green2');
									$('#id-'+row.id+' td.website_name').addClass('font_green2');
									
									$('#id-'+row.id).addClass('g2');	
								}else if(dateDiff == 2){
									$('#id-'+row.id+' td.last_insert_date').addClass('green3');
									$('#id-'+row.id+' td.website_name').addClass('font_green3');

									$('#id-'+row.id).addClass('g3');
								}
							}
							
							if(row.latest_fetch != null && row.latest_fetch != 0){
								var latest_fetch = row.latest_fetch.split(' ');
								latest_fetch = latest_fetch[0];
								
								var dateDiff = DateDiff(new Date(currentDate),new Date(latest_fetch));
								
								if(dateDiff == 0){
									$('#id-'+row.id+' td.latest_fetch').addClass('green');
								}else if(dateDiff == 1){
									$('#id-'+row.id+' td.latest_fetch').addClass('green2');
								}else if(dateDiff == 2){
									$('#id-'+row.id+' td.latest_fetch').addClass('green3');
								}else{
									$('#id-'+row.id+' td.latest_fetch').addClass('red1');
								}
							}
							
							if(row.parse_date != '0000-00-00 00:00:00' && row.parse_date != ''){
								var parse_date = row.parse_date.split(' ');
								parse_date = parse_date[0];
								
								var dateDiff = DateDiff(new Date(currentDate),new Date(parse_date));
																
								$('#id-'+row.id+' td.parse_date').removeClass('green');
								$('#id-'+row.id+' td.parse_date').removeClass('green2');								
								$('#id-'+row.id+' td.parse_date').removeClass('green3');
								$('#id-'+row.id+' td.parse_date').removeClass('red1');
													
																
								if(dateDiff == 0){
									$('#id-'+row.id+' td.parse_date').addClass('green');
								}else if(dateDiff == 1){
									$('#id-'+row.id+' td.parse_date').addClass('green2');
								}else if(dateDiff == 2){
									$('#id-'+row.id+' td.parse_date').addClass('green3');
								}else{
									$('#id-'+row.id+' td.parse_date').addClass('red1');
								}
							}
																					
							var insert_date_class = $('#id-'+row.id+' td.last_insert_date').attr('class').split(' ');
							var latest_fetch_class = $('#id-'+row.id+' td.latest_fetch').attr('class').split(' ');
							
							insert_date_class = jQuery.trim(insert_date_class[1]);
							latest_fetch_class = jQuery.trim(latest_fetch_class[1]);
							
							//console.log(latest_fetch_class+'/'+latest_fetch_class);	
							//alert(insert_date_class);
							
							if(insert_date_class == '' && latest_fetch_class == 'red1'){
								$('#id-'+row.id+' td.last_insert_date').addClass('red1');
								$('#id-'+row.id+' td.website_name').addClass('font_red1');
								
								$('#id-'+row.id).addClass('g5');
							}else if(insert_date_class == '' && (latest_fetch_class != '') ){
								$('#id-'+row.id+' td.last_insert_date').addClass('red2');
								$('#id-'+row.id+' td.website_name').addClass('font_red2');
								
								$('#id-'+row.id).addClass('g4');
							}else if(insert_date_class == '' && latest_fetch_class == ''){
								$('#id-'+row.id).addClass('g6');															
							}
							
							$('#id-'+row.id+' td.last_insert_date').text(row.last_insert_date);
							$('#id-'+row.id+' td.latest_fetch').text(row.latest_fetch);
							$('#id-'+row.id+' td.parse_date').text(row.parse_date);
						
							if($('#result').val() == 0){
								getResult();	
							}							
						});
					}					
				},'json');
				
				setTimeout(loadDate,1000);
				
				i++;
			}else{
				i = 0;
				
				var  g6 = $('#page table tbody tr.g6').length;
				$('#g6').text(g6);
				
				getResult();
			
				update();
				setTimeout(loadDate,60000);
			}		
		}
			
		function DateDiff(date1, date2) {
			var datediff = date1.getTime() - date2.getTime();
			return (datediff / (24*60*60*1000));    
		}
		function getResult(){
			var  g1 = $('#page table tbody tr.g1').length;
			var  g2 = $('#page table tbody tr.g2').length;
			var  g3 = $('#page table tbody tr.g3').length;
			var  g4 = $('#page table tbody tr.g4').length;
			var  g5 = $('#page table tbody tr.g5').length;
			
			$('#g1').text(g1);
			$('#g2').text(g2);
			$('#g3').text(g3);
			$('#g4').text(g4);
			$('#g5').text(g5);	
		}
		function update(){
			var url  = '<?PHP echo base_url();?>index.php/page/update';
			var data ={g1:$('#g1').text(),g2:$('#g2').text(),
				   g3:$('#g3').text(),g4:$('#g4').text(),
				   g5:$('#g5').text(),g6:$('#g6').text()};
			
			$.post(url,data,function(){   });	
		}
		
		loadDate();
		
		$('#total').text($('#page table tbody tr').length);
	});
</script>