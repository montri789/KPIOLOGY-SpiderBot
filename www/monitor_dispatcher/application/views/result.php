<div>
    <h2>Bot Matchs</h2><br/>
    <div>
        <div class="alert alert-info">
            <div class="pull-left">Total Client : <?=$count_client;?></div>	
            <div class="pull-left green3">Match Today : <?=$count_all;?></div>
            <div class="pull-left green1">Complete : <?=$count_complete;?></div>	
            <div class="pull-left red1">Fail : <?=$count_fail;?></div>
            <br clear="all"/>
        </div>
    </div>
    <br/>
    <h2>Fetch Pages</h2><br/>
    <div> 
        <div class="alert alert-info">
	<div class="pull-left">Total Website : <span id="total"><?=$website_total;?></span></div>	
	<div class="pull-left green1">วันนี้ : <span id="g1"><?=(isset($result)) ? $result["result1"] : 0 ?></span></div>
	<div class="pull-left green2">เมื่อวานนี้ : <span id="g2"><?=(isset($result)) ? $result["result2"] : 0 ?></span></div>
	<div class="pull-left green3">เมื่อวานซืน : <span id="g3"><?=(isset($result)) ? $result["result3"] : 0 ?></span></div>
	<div class="pull-left red2">ไม่เก็บลงตาราง : <span id="g4"><?=(isset($result)) ? $result["result4"] : 0 ?></span></div>
	<div class="pull-left red1">ตาย : <span id="g5"><?=(isset($result)) ? $result["result5"] : 0 ?></span></div>
	<div class="pull-left other">อื่นๆ : <span id="g6"><?=(isset($result)) ? $result["result6"] : 0 ?></span></div>
	<br clear="all"/>
        </div>
    </div>
    
</div>