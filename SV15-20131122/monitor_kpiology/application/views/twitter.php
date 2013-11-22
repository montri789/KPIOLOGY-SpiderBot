
<h2>KPIology Twitter System Monitoring</h2>
<br/>

<div class="alert <?=($convertor["status"] == "up") ? "alert-success" : "alert-error"; ?>">
<i class="icon-arrow-<?=$convertor["status"];?>"></i>
<!--img src="<?PHP echo config_item("assets_url"); ?>/img/<?=$convertor["status"];?>-icon.gif"--><b>Author Convertor Bot</b>
System is <?=strtoupper($convertor["status"]);?>
<br/>
Last running <?=$convertor["last_running"];?>
<br/>
Tweet messages in queue =   <?=$convertor["tweet_messages"];?>
</div>

<div class="alert <?=($convertor["status"] == "up") ? "alert-success" : "alert-error"; ?>">
<i class="icon-arrow-<?=$convertor["status"];?>"></i>
<!--img src="<?PHP echo config_item("assets_url"); ?>/img/<?=$firehose["status"];?>-icon.gif"-->


<?PHP if(isset($firehose)){ ?>

<b>Twitter Firehose Connection</b>
<?PHP if(!isset($firehose["warning"])){ ?>
System is <?=strtoupper($firehose["status"]);?><?PHP } ?>

<?PHP if(isset($firehose["warning"])){ ?>
<font color=yellow> Warning</font>
<?PHP } ?>

<br>
Last running <?=$firehose["last_running"];?> <?PHP if(isset($firehose["tweetleft"])){ ?> at <?=$firehose["tweetleft"];?> messages/min <?PHP } ?>

<?PHP  }else{ ?>
<b>Twitter Firehose Connection</b><br/>
Tweet messages in queue =   <?=$tweetleft;?>
<?PHP }?>


</div>

<meta http-equiv="refresh" content="60" >