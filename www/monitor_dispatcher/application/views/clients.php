<h2>Bot Matchs</h2>
<br/>
<div class="alert alert-info">
	<div class="pull-left">Total Client : <?=$count_client;?></div>	
	<!---->
	<div class="pull-left green3">Match Today : <?=$count_all;?></div>
	<div class="pull-left green1">Complete : <?=$count_complete;?></div>	
	<div class="pull-left red1">Fail : <?=$count_fail;?></div>
	<br clear="all"/>
</div>

<div class="row">
    <div class="span12">
	<table class="table table-bordered table-striped tablesorter" id="sort" border="0" cellspacing="5" cellpadding="5">
	  <tr>
	    <th width="50px">Client ID</th>
	    <th width="300px">Client Name</th>
	    <th width="100px">Last Match</th>
	    <th width="100px">All Subject</th>
	    <th width="100px">Match Complete</th>
	    <th width="100px">Match Fail</th>
	    <th width="100px">Matching...</th>
	  </tr>
	  <?PHP
		$i=0;
		foreach($rp as $row){
		    $i++;
	    ?>
	  <tr id="id_<?=$row["client_id"]?>">
	    <td><?=$row["client_id"];?></td>
	    <td><?=anchor('report/matchs/'.$row["client_id"], $row["client_name"], 'title="Show Matchs"');?></td>
	    <td style="text-align: center;"><?=$row["match_from"];?></td>
	    <td style="text-align: center;"><?=$row["count_subject"];?></td>
	    <td style="text-align: center;" class="font_green"><?= empty($row["match_complete"])? "" : $row["match_complete"]; ?></td>
	    <td style="text-align: center;" class="font_red"><?= empty($row["match_fail"])? "" : $row["match_fail"]; ?></td>
	    <td style="text-align: center;"><?= empty($row["matching"])? "" : $row["matching"]; ?></td>
	  </tr>
	  <?PHP } ?>
	</table>
    </div>
</div>