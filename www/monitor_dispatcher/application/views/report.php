<input type="hidden" id="client_id" value="<?=$client_id;?>" />
<div class="row">
    <div class="span12">
	<div class="div_hd">
	    <div class="div_client">
		Client : <?=$client_name;?>
	    </div>
	    <div class="div_gohome">
		<?=anchor('welcome/index/', "กลับหน้าหลัก");?>
	    </div>
	</div>
	<table class="table table-bordered table-striped tablesorter" id="sort" border="0" cellspacing="5" cellpadding="5">
	  <thead>
	  <tr>
	    <th width="50px">#</th>
	    <th width="300px">Subject</th>
	    <th width="125px">Start Date</th>
	    <th width="125px">End Date</th>
	    <th width="100px">Match All</th>
	    <th width="100px">Match Insert</th>
	    <th width="100px">WPC All</th>
	    <th width="100px">WPC Insert</th>
	  </tr>
	  </thead>
	  <tbody>
	  </tbody>
	</table>
    </div>
</div>

<script type="text/javascript">
	$(function(){
	
		function loadData(){
			
			var url = '<?=base_url();?>index.php/report/getData';
			var data = {client_id:$('#client_id').val()};
						
			$.get(url,data,function(res){
				if(res != ''){
					$('.table tbody').html('');
					var html = '';
					$.each(res.rp,function(i){
						
						var row = res.rp[i];
						var color = '';
						var color_wpc = '';
						
						if(row.match_all == row.match_insert){
							color = 'font_green';
						}else{
							color = 'font_red';
						}
						    
						if(row.wpc_all == row.wpc_insert){
							color_wpc = 'font_green';
						}else{
							color_wpc = 'font_red';
						}						
						
						html += '<tr id="id_'+row.id+'">'+
						     '<td class="name">'+(i+1)+'<\/td>'+
						     '<td class="name">'+row.subject+'<\/td>'+
						     '<td class="name">'+row.start_datetime+'<\/td>'+
							'<td class="name">'+row.stop_datetime+'<\/td>'+
							'<td class="name" style="text-align: center;">'+row.match_all+'<\/td>'+
							'<td class="'+color+'" style="text-align: center;">'+row.match_insert+'<\/td>'+
							'<td class="name" style="text-align: center;">'+row.wpc_all+'<\/td>'+
							'<td class="'+color_wpc+'" style="text-align: center;">'+row.wpc_insert+'<\/td>'+
						      '</tr>';
					});
						      
					$('.table tbody').html(html);
					
				}
				setTimeout(loadData,1000);	
				
			},'json');
		}
	
		loadData();
	});
</script>