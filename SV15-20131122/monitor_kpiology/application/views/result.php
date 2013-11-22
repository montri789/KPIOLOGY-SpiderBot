<div>
    
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
    <div id="chart_div_pantip" style="width: 900px; height: 500px;"></div>
    <div id="chart_div_get_post" style="width: 900px; height: 500px;"></div>
    <div id="chart_div_get_page" style="width: 900px; height: 500px;"></div>
    <h2>Bot Matchs Kpiology</h2><br/>
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
    
    <h2>Bot Matchs Warroom</h2><br/>
    <div>
        <div class="alert alert-info">
            <div class="pull-left">Total Client : <?=$count_wr_client;?></div>	
            <div class="pull-left green3">Match Today : <?=$count_wr_all;?></div>
            <div class="pull-left green1">Complete : <?=$count_wr_complete;?></div>	
            <div class="pull-left red1">Fail : <?=$count_wr_fail;?></div>
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
    <br/>

    <h2>Bot Running Page</h2><br/>
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
	    <div class="pull-left green1">Total post : <span><?php echo number_format($total_post);?></span></div>
	    <div class="pull-left green1">Avg. post per day : <span><?php echo number_format($total_post/3);?></span></div>
	    <div class="pull-left green1">Total page : <span><?php echo number_format($total_page);?></span></div>
	    <div class="pull-left green1">Avg. page per day : <span><?php echo number_format($total_page/3);?></span></div>
	    <br clear="all"/>
    </div>
    <br/>
    
    <h2>Bot Running Post <?php echo count($arr_report);?> Domain</h2>
    <br/>
    <div class="alert alert-info" style="margin-bottom:1px">
	    <div class="pull-left">Today : </span></div>
	    <div class="pull-left green1">Post/Page : <span id="g_domain2"></span></div>
	    <div class="pull-left green3">Post or Page zero : <span id="g_domain3"></span></div>
	    <div class="pull-left red2">Post && Page zero : <span id="g_domain4"></span></div>
	<div class="pull-left red1">No Request : <span id="g_domain5"></span></div>
	<br clear="all"/>
    </div>
    <div class="alert alert-info" style="margin-bottom:1px">
	    <div class="pull-left">Yesterday : </span></div>
	    <div class="pull-left green1">Post/Page : <span id="y_domain2"></span></div>
	    <div class="pull-left green3">Post or Page zero : <span id="y_domain3"></span></div>
	    <div class="pull-left red2">Post && Page zero : <span id="y_domain4"></span></div>
	<div class="pull-left red1">No Request : <span id="y_domain5"></span></div>
	<br clear="all"/>
    </div>
    <div class="alert alert-info">
	    <div class="pull-left">SUM 7 DAY : </span></div>
	    <div class="pull-left green1">Post/Page : <span id="a_domain2"></span></div>
	    <div class="pull-left green3">Post or Page zero : <span id="a_domain3"></span></div>
	    <div class="pull-left red2">Post && Page zero : <span id="a_domain4"></span></div>
	<div class="pull-left red1">No Request : <span id="a_domain5"></span></div>
	<br clear="all"/>
    </div>
    
    <?php
		 $i['2']=0;		 $i['3']=0;		 $i['4']=0;		 $i['5']=0;
		 $y['2']=0;		 $y['3']=0;		 $y['4']=0;		 $y['5']=0;
		 $a7['2']=0;	 $a7['3']=0;	 $a7['4']=0;	 $a7['5']=0;
		 $now = date("Y-m-d");
		 $yes = date("Y-m-d",strtotime("-1 day"));
		 $avg = date("Y-m-d",strtotime("-6 day"));
		 $obj = new Result();
		 $obj->init();

	foreach ($arr_report as $key => $value) {
	    
		 $objNowValue = $obj->select_date_one($now,$value['domain_name']);
		 //print_r($objNowValue);
		 $objYesValue = $obj->select_date_yes($yes,$value['domain_name']);
		 $objsevenValue = $obj->select_date_seven($avg,$now,$value['domain_name']);
		
		if($objNowValue['sum_request_n']==0 and $objNowValue['sum_page_n']==0 and $objNowValue['sum_post_n']==0){
			$class_color1="red1";
			$i['5']++;
		}else if($objNowValue['sum_page_n']==0 and $objNowValue['sum_post_n']==0){
			$class_color1="red2";
			$i['3']++;
		}else if($objNowValue['sum_page_n'] > 0 and $objNowValue['sum_post_n'] > 0){
			$class_color1="green";
			$i['2']++;
		}else{
			$class_color1="green3";
			$i['4']++;
		}
		
		if($objYesValue['sum_request_y']==0 and $objYesValue['sum_page_y']==0 and $objYesValue['sum_post_y']==0){
			$class_color2="red1";
			$y['5']++;
		}else if($objYesValue['sum_page_y']==0 and $objYesValue['sum_post_y']==0){
			$class_color2="red2";
			$y['3']++;
		}else if($objYesValue['sum_page_y'] > 0 and $objYesValue['sum_post_y'] > 0){
			$class_color2="green";
			$y['2']++;
		}else{
			$class_color2="green3";
			$y['4']++;
		}
		
		if($objsevenValue['sum_request_7']==0 and $objsevenValue['sum_page_7']==0 and $objsevenValue['sum_post_7']==0){
			$class_color3="red1";
			$a7['5']++;
		}else if($objsevenValue['sum_page_7']==0 and $objsevenValue['sum_post_7']==0){
			$class_color3="red2";
			$a7['3']++;
		}else if($objsevenValue['sum_page_7'] > 0 and $objsevenValue['sum_post_7'] > 0){
			$class_color3="green";
			$a7['2']++;
		}else{
			$class_color3="green3";
			$a7['4']++;
		}
	}
	#echo "g2=>".$i['2'];
	
     ?>
    
    <script language="javascript">    
    $(document).ready(function() {
	    $('#g_domain2').text(<?php echo $i['2']?>);
	    $('#g_domain3').text(<?php echo $i['3']?>);
	    $('#g_domain4').text(<?php echo $i['4']?>);
	    $('#g_domain5').text(<?php echo $i['5']?>);
	    $('#y_domain2').text(<?php echo $y['2']?>);
	    $('#y_domain3').text(<?php echo $y['3']?>);
	    $('#y_domain4').text(<?php echo $y['4']?>);
	    $('#y_domain5').text(<?php echo $y['5']?>);
	    $('#a_domain2').text(<?php echo $a7['2']?>);
	    $('#a_domain3').text(<?php echo $a7['3']?>);
	    $('#a_domain4').text(<?php echo $a7['4']?>);
	    $('#a_domain5').text(<?php echo $a7['5']?>);
    });
    </script>
    
    <?php
    
        #$obj = new Monitor_facebook();
        #$now = date("Y-m-d");
        $yes1 = date("Y-m-d",strtotime("-1 day"));
        $yes2 = date("Y-m-d",strtotime("-2 day"));
    ?>
    <br/>

    <h2>Bot Twitter</h2>
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
    
</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<script type="text/javascript">

  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  google.setOnLoadCallback(drawChartPantip);
  
  function drawChart() {

    var data;
    var chart;
	    
    data = new google.visualization.DataTable();
	data.addColumn('string', 'Post Insert');
	data.addColumn('number', 'Data All');

	data.addRows([<?=$chart_post;?>]);

    var options = {
      title: 'Post Insert Daily',
      hAxis: {title: 'Count Post', titleTextStyle: {color: 'red'}},
      fontSize:12
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
  
   function drawChartPantip() {

    var data;
    var chart;
	    
    data = new google.visualization.DataTable();
	data.addColumn('string', 'Post Insert');
	data.addColumn('number', 'Pantip Page');
	data.addColumn('number', 'Pantip Post');

	data.addRows([<?=$chart_pantip;?>]);

    var options = {
      title: 'Pantip Page/Post',
      hAxis: {title: 'Count Pantip Page/Post', titleTextStyle: {color: 'red'}},
      colors:['#FF8000','#0174DF'],
      fontSize:12
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_pantip'));
    chart.draw(data, options);
  }
</script>

    <script type="text/javascript">/*
      function drawVisualization1() {
        // Create and populate the data table.
		var data = google.visualization.arrayToDataTable([
		<?PHP echo $graph_get_page?>
		]);
      
        // Create and draw the visualization.
        new google.visualization.ColumnChart(document.getElementById('chart_div_get_page')).
            draw(data,
                 {title:"Kpiology Get Page/Post",
                  width:900, height:500,
                  hAxis: {title: "Date"}}
            );
      }
      

      google.setOnLoadCallback(drawVisualization1);
    */</script>
    
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          <?PHP echo $graph_get_post?>
        ]);

        var options = {
          title: 'Kpiology Get Post',
          hAxis: {title: 'Date',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0},
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div_get_post'));
        chart.draw(data, options);
      }
    </script>
    
      <script type="text/javascript">/*
      function drawVisualization2() {
        // Create and populate the data table.
		var data = google.visualization.arrayToDataTable([
		<?PHP echo $graph_get_post?>
		]);
      
        // Create and draw the visualization.
        new google.visualization.ColumnChart(document.getElementById('chart_div_get_post')).
            draw(data,
                 {title:"Kpiology page no zero / post today",
                  width:900, height:500,
                  hAxis: {title: "Date"}}
            );
      }
      

      google.setOnLoadCallback(drawVisualization2);
    */</script>
    
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          <?PHP echo $graph_get_page?>
        ]);

        var options = {
          title: 'Kpiology page no zero / post today'
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div_get_page'));
        chart.draw(data, options);
      }
    </script>
