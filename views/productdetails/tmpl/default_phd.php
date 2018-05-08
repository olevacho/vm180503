<div class="customer-reviews">
  <h4>Past Performance</h4>

  <?php 

  $productId = $this->product->virtuemart_product_id;

  // $productid = (isset($_GET['product']) && $_GET['product'] != '' ? $_GET['product'] : false);

  // Get a db connection.
  $db = JFactory::getDbo();
  // Create a new query object.
  $query = $db->getQuery(true);


  $query->select(array('fxbotmarketx.id', 'fxbotmarketx_duplicum.account_id', 'virtuemart.published', 'virtuemart.virtuemart_product_id'));
  $query->from('#__virtuemart_products as virtuemart');
  $query->join('LEFT', '#__fxbotmarketx_files_products as fxbotmarketx on fxbotmarketx.product_id = virtuemart.virtuemart_product_id');
  $query->join('LEFT', '#__fxbotmarketx_duplicum as fxbotmarketx_duplicum on fxbotmarketx_duplicum.id_product = fxbotmarketx.id');
  $query->where('virtuemart.virtuemart_product_id = '. $productId);
  $db->setQuery($query);

  $results = $db->loadAssoc();
//var_dump($results);
  if(!class_exists('FxbotmarketProductcustomer')) {
            include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/productcustomer.php';
  }
  $productcustomer = new FxbotmarketProductcustomer();
  
  $id_fx = (int)$results['id'];
  //var_dump($id_fx);
  $perf_data = $productcustomer->preparePhD($id_fx);
  //var_dump($perf_data);
  $phd_stat = $productcustomer->getPhDStat($id_fx);
  $tat = 0;
  if(is_object($phd_stat)){
      $tat = $productcustomer->formatMoney(floatval($phd_stat->tat), 2, 'USD') ;
  }

  $phd_total_stat = $productcustomer->getPhDTotalStat($id_fx);
  $total_stat_isset = false;
  if(is_object($phd_total_stat) && isset($phd_total_stat->tnmt) && $phd_total_stat->tnmt > 0 ){
      $total_stat_isset = true;
  }

/*        $result->tnmt = $tnmt;//Total Number of Months traded
        $result->nwm =  $nwm;//Number of winning months
        $result->nlm = $nlm ;//Number of losing months
        $result->amr = $amr ;//Average monthly return
        $result->bnm = $bnm ;//Biggest Drawdown (biggest negative month)
*/
  //var_dump($stat);

/*
Total Number of Months traded: 

Number of winning months:

Number of losing months:

Average monthly return:

Biggest Drawdown (biggest negative month):
 *  */


?>
<!--
<link rel="stylesheet" type="text/css" href="https://www.highcharts.com/samples/static/highslide.css" />


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://www.highcharts.com/samples/static/highslide-full.min.js"></script>
<script src="https://www.highcharts.com/samples/static/highslide.config.js" charset="utf-8"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
-->
<link rel="stylesheet" type="text/css" href="https://www.highcharts.com/samples/static/highslide.css" />

<!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js" ></script> -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://www.highcharts.com/samples/static/highslide-full.min.js"></script>
<script src="https://www.highcharts.com/samples/static/highslide.config.js" charset="utf-8"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<form method="post" class="col-md-12">
    <div class="container-fluid">
        <div class="row tap-product" id="tab-productdes">

            <?php

                ?>
            
                <div class="col-md-3 cus-p">
                    <div class="tab-sidebar">
                        <h3>Statistic</h3>
                        <div id="stats2" class="cus-design">
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    
                    
                    <div class="clearfix"></div>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        
                        <div role="tabpanel" class="tab-pane active" id="profile">
                            <?php 


                            $i = 1;
                            foreach ($perf_data as $yrec){
                                if(!$productcustomer->checkBrokenPhdData($yrec)){
                                    continue;
                                }
                                ?>
                                <div role="tabpanel" class="tab-pane active" id="profile" style="margin-top: 30px;position:relative;">
                                    <?php if($phd_stat->approved > 0){ ?>
                                    
                                    <div style="position:absolute;left:60px;top:60px;z-index:1000;">
                                        <img src="components/com_fxbotmarket/assets/jsgrid/FXbot-turquoise.png" style="max-width:30px;" />
                                        <span style="color:#4F817E;">Verified</span> 
                                    </div>
                                    <?php } ?>
                                                        <div id="balance<?php echo $i;?>" style="width: 100%; height: 400px; margin: 0 auto"></div>
                                                        <?php if($phd_stat->approved > 0){ ?>
                                                        <p style="font-size: 11px;color:#8A8889;">We have independently verified this account statement to be a true and accurate reflection of a live account.  Individual monthly performance numbers may vary from account to account.  For more information see <a href="http://fxbot.market/policies"> our policy</a> </p>
                                                        <?php } ?>
                                </div>
                            <?php
                            $i++;
                            }
                                ?>
                        </div>
                       
                    </div>

                  

                </div>
            <?php

            ?>
            
            
        </div>

       
    </div>
</form>



<script type="text/javascript">
/*
    var balance = {
	"status":"ok","response":
	{"category":["2017-11-29 10:02:04","2017-11-29 11:02:35","2017-11-29 12:48:27","2017-11-30 11:05:03","2017-11-30 11:56:55","2017-12-04 12:50:02","2017-12-04 15:57:32","2017-12-04 16:11:09","2017-12-04 16:52:19","2017-12-05 12:23:31","2017-12-05 14:27:30","2017-12-05 15:54:47","2017-12-06 11:18:18","2017-12-06 11:42:13","2017-12-07 07:22:01","2017-12-07 11:19:22","2017-12-07 12:25:13","2017-12-11 14:16:27","2017-12-11 14:56:30","2017-12-11 15:18:30","2017-12-12 09:46:59","2017-12-12 10:35:01","2017-12-12 11:27:38","2017-12-12 14:43:00","2017-12-13 09:52:22","2017-12-13 12:22:46","2017-12-13 12:22:59","2017-12-13 14:30:03","2017-12-13 14:30:03","2017-12-13 14:34:37","2017-12-13 14:53:05","2017-12-13 16:30:50","2017-12-13 16:40:52","2017-12-13 20:00:02","2017-12-13 20:00:03","2017-12-14 09:08:46","2017-12-14 09:33:25","2017-12-14 10:27:41","2017-12-14 10:49:38","2017-12-15 12:09:18","2017-12-15 15:24:25","2017-12-19 10:24:23","2017-12-19 10:24:24","2017-12-19 13:10:21","2017-12-19 14:30:27","2017-12-19 21:28:54","2017-12-20 12:54:31","2017-12-20 14:39:36","2017-12-20 16:08:38","2017-12-20 17:29:27","2017-12-21 10:05:52","2017-12-21 13:29:27","2017-12-21 13:34:52","2017-12-21 17:21:51","2017-12-27 16:01:35","2017-12-27 16:01:44","2017-12-28 13:08:14","2017-12-28 14:45:38","2017-12-29 15:44:02","2018-01-02 10:57:34","2018-01-03 16:22:08","2018-01-03 16:22:10","2018-01-03 16:22:12","2018-01-05 14:46:32","2018-01-05 15:36:55","2018-01-05 17:29:23","2018-01-09 16:55:09","2018-01-10 11:42:59","2018-01-11 13:16:25","2018-01-16 00:27:54","2018-01-16 00:27:56","2018-01-16 03:54:27","2018-01-16 03:54:30","2018-01-16 13:21:33","2018-01-16 16:34:05","2018-01-17 11:01:25","2018-01-17 11:01:30","2018-01-22 11:57:46","2018-01-22 13:39:03","2018-01-23 09:57:22","2018-01-23 09:57:24","2018-01-25 14:53:32","2018-01-25 15:20:13","2018-01-29 14:56:13","2018-01-29 14:56:17","2018-01-29 16:47:10","2018-01-30 16:22:13","2018-01-30 20:28:00","2018-01-31 04:56:11","2018-02-05 13:22:52","2018-02-05 13:22:57","2018-02-05 13:22:58","2018-02-05 14:23:57","2018-02-05 15:07:15","2018-02-06 16:55:56","2018-02-07 16:57:44","2018-02-07 20:21:26","2018-02-08 18:03:25","2018-02-12 15:00:12","2018-02-12 15:03:19","2018-02-12 18:30:12","2018-02-13 14:40:47","2018-02-13 14:41:29","2018-02-15 14:13:10","2018-02-15 19:00:16","2018-02-20 20:47:57","2018-02-21 16:10:33","2018-02-22 17:20:31","2018-02-26 15:14:59","2018-02-27 17:05:50","2018-02-28 16:04:53","2018-02-28 16:58:53","2018-03-01 16:16:22","2018-03-01 17:13:50","2018-03-01 17:20:29","2018-03-02 15:03:49","2018-03-02 16:54:29","2018-03-05 15:19:45","2018-03-06 13:54:31","2018-03-06 13:56:11","2018-03-07 17:00:54","2018-03-07 17:24:39","2018-03-08 13:46:34","2018-03-08 13:46:43","2018-03-12 13:43:28","2018-03-12 16:11:41","2018-03-12 16:52:29","2018-03-13 14:41:12","2018-03-13 14:41:14","2018-03-14 13:24:32","2018-03-14 13:24:33","2018-03-14 16:38:05","2018-03-14 16:38:07","2018-03-15 15:27:21","2018-03-19 13:40:43","2018-03-19 13:40:45","2018-03-19 13:40:47","2018-03-20 15:32:05","2018-03-20 15:53:02","2018-03-21 13:48:17","2018-03-21 13:52:10","2018-03-22 14:38:39","2018-03-22 16:03:36","2018-03-22 17:28:26","2018-03-26 15:04:53","2018-03-26 15:37:12","2018-03-27 13:58:03","2018-03-27 13:58:05","2018-03-27 13:58:07","2018-03-27 14:11:02","2018-03-28 14:33:29","2018-03-28 15:00:14","2018-03-28 15:00:17","2018-03-29 14:58:19","2018-03-29 14:58:21","2018-03-29 16:10:12","2018-03-29 16:19:56","2018-03-29 16:28:56","2018-03-29 16:37:15"]
	,"showMarker":true,
	"sNumberSuffix":" USD",
	"prefix":"USD",
	"series":	[
	{"name":"Balance",
	"color":"#ed423a",
	"type":"line",
	"data":[1019.8,1027.29,1017.34,1025.19,1029.19,1017.66,1005.96,991.16,977.81,966.84,973.49,984.74,992.84,999.62,1013.22,1019.77,1029.81,1036.53,1027.03,1020.07,1027.75,1034.2,1026.25,1033.85,1025.3,1026.56,1025.6,1014.45,1008.1,997.34,988.66,981.64,972.81,980.06,987.66,980.26,967.43,975.88,968.58,976.33,985.82,977.5,965.85,972.3,982.4,974.07,978.58,986.08,992.83,983.56,985.26,998.66,998.86,1002.91,1014.77,1026.57,1030.19,1027.64,1028.34,1041.65,1035.55,1028.38,1033.67,1041.07,1049.07,1045.72,1051.47,1064.37,1065.87,1062.67,1052.12,1049.17,1045.72,1054.17,1061.97,1071.02,1077.77,1082.17,1073.69,1098.62,1103.52,1119.39,1117.29,1120.14,1137.53,1142.18,1146.97,1153.55,1101.88,1109.99,1121.24,1132.69,1128.54,1138.24,1148.19,1162.88,1162.19,1148.84,1161.41,1159.67,1169.06,1161.26,1139.66,1141.96,1147,1151.8,1161.94,1171.14,1137.74,1147.44,1139.59,1139.59,1146.92,1141.64,1142.19,1137.59,1135.19,1137.81,1146.71,1150.86,1143.41,1139.5,1152.55,1166.8,1150.58,1148.04,1157.43,1151.83,1153.18,1159.43,1165.48,1177.83,1190.69,1173.99,1165.74,1157.89,1155.34,1149.14,1156.09,1153.07,1150.27,1154.72,1145.12,1135.72,1122.95,1125.6,1111.95,1102.56,1089.86,1095.66,1096.5,1102.55,1102.4,1097.95,1090.55,1089.28,1094.78,1091.28,1097.57]},{"name":"Equity","color":"#ffc209","type":"line","data":[1019.8,1027.29,1017.34,1025.19,1029.19,1017.66,1005.96,991.16,977.81,966.84,973.49,984.74,992.84,999.62,1013.22,1019.77,1029.81,1036.53,1027.03,1020.07,1027.75,1034.2,1026.25,1033.85,1025.3,1026.56,1025.6,1014.45,1008.1,997.34,988.66,981.64,972.81,980.06,987.66,980.26,967.43,975.88,968.58,976.33,985.82,977.5,965.85,972.3,982.4,974.07,978.58,986.08,992.83,983.56,985.26,998.66,998.86,1002.91,1014.77,1026.57,1030.19,1027.64,1028.34,1041.65,1035.55,1028.38,1033.67,1041.07,1049.07,1045.72,1051.47,1064.37,1065.87,1062.67,1052.12,1049.17,1045.72,1054.17,1061.97,1071.02,1077.77,1082.17,1073.69,1098.62,1103.52,1119.39,1117.29,1120.14,1137.53,1142.18,1146.97,1153.55,1101.88,1109.99,1121.24,1132.69,1128.54,1138.24,1148.19,1162.88,1162.19,1148.84,1161.41,1159.67,1169.06,1161.26,1139.66,1141.96,1147,1151.8,1161.94,1171.14,1137.74,1147.44,1139.59,1139.59,1146.92,1141.64,1142.19,1137.59,1135.19,1137.81,1146.71,1150.86,1143.41,1139.5,1152.55,1166.8,1150.58,1148.04,1157.43,1151.83,1153.18,1159.43,1165.48,1177.83,1190.69,1173.99,1165.74,1157.89,1155.34,1149.14,1156.09,1153.07,1150.27,1154.72,1145.12,1135.72,1122.95,1125.6,1111.95,1102.56,1089.86,1095.66,1096.5,1102.55,1102.4,1097.95,1090.55,1089.28,1094.78,1091.28,1097.57
	]}]
        }
    }
    */
<?php 
$i = 1;

foreach ($perf_data as $yrec){
    $startvar = 1000;
    $check = $productcustomer->checkBrokenPhdData($yrec);
    if(!$check){
        continue;
    }
    ?>
        
    var balance<?php echo $i;?> = {
        "status":"ok","response":
	{"category":["<?php echo $yrec['year'];?> Jan","<?php echo $yrec['year'];?> Feb","<?php echo $yrec['year'];?> Mar","<?php echo $yrec['year'];?> Apr","<?php echo $yrec['year'];?> May","<?php echo $yrec['year'];?> Jun","<?php echo $yrec['year'];?> Jul","<?php echo $yrec['year'];?> Aug","<?php echo $yrec['year'];?> Sep","<?php echo $yrec['year'];?> Oct","<?php echo $yrec['year'];?> Nov","<?php echo $yrec['year'];?> Dec"]
	,"showMarker":true,
	"sNumberSuffix":" ",
	"prefix":"USD",
	"series":	[
	{"name":"<?php echo $yrec['year'];?>",
	"color":"#ed423a",
	"type":"line",
	"data":[<?php 
        if(array_key_exists('Jan', $yrec) && $yrec['Jan'] !== '') {
            $startvar += $startvar/100*$yrec['Jan']; echo $startvar;
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('Feb', $yrec) && $yrec['Feb'] !== '') {
            $startvar += $startvar/100*$yrec['Feb']; echo $startvar;
            
        }else{
            echo 'null';} ?>,<?php 
        if(array_key_exists('March', $yrec) && $yrec['March'] !== '') {
            $startvar += $startvar/100*$yrec['March']; echo $startvar;
            
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('Apr', $yrec) && $yrec['Apr'] !== '') {
            $startvar += $startvar/100*$yrec['Apr']; echo $startvar;
            
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('May', $yrec) && $yrec['May'] !== '') {
            $startvar += $startvar/100*$yrec['May']; echo $startvar;
            
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('Jun', $yrec) && $yrec['Jun'] !== '') {
            $startvar += $startvar/100*$yrec['Jun']; echo $startvar;
            
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('Jul', $yrec) && $yrec['Jul'] !== '') {
            $startvar += $startvar/100*$yrec['Jul']; echo $startvar;
            
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('Aug', $yrec) && $yrec['Aug'] !== '') {
            $startvar += $startvar/100*$yrec['Aug']; echo $startvar;
            
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('Sep', $yrec) && $yrec['Sep'] !== '') {
            $startvar += $startvar/100*$yrec['Sep']; echo $startvar;
            
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('Oct', $yrec) && $yrec['Oct'] !== '') {
            $startvar += $startvar/100*$yrec['Oct']; echo $startvar;
            
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('Nov', $yrec) && $yrec['Nov'] !== '') {
            $startvar += $startvar/100*$yrec['Nov']; echo $startvar;
            
        }else{
            echo 'null';}?>,<?php 
        if(array_key_exists('Dec', $yrec) && $yrec['Dec'] !== '') {
            $startvar += $startvar/100*$yrec['Dec']; echo $startvar;
            
        }else{
            echo 'null';}?>
        ]},
    ]
        }
    }
     
        <?php
        $i++;
}
?>

    jQuery(document).ready(function(){
     <?php 
$i = 1;

foreach ($perf_data as $yrec){
    if(!$productcustomer->checkBrokenPhdData($yrec)){
        continue;
    }
    ?>   
        //createBalanceGraph(balance,'balance','2017');
        createBalanceGraph2(balance<?php echo $i;?>,'balance<?php echo $i;?>','<?php echo $yrec['year'];?>');
<?php 
$i++;
}
    ?>        
        jQuery('.highcharts-credits').hide();
        //createGrowthGraph();
        //createProfitGraph();
        //createDropdownGraph();
        //createMultistats();
        //createAverageholdtime();
        //createCurrencypopularity();
        //creatMonthReport();
        //creatCapsulestats();
        createMultistats2();
    });

    function ChartParent2(divId) 
    {
        this.series;
        this.categoriesLabels;
        this.categories;
        this.onclickResponse = [];
        this.parseAsTime = false;
        this.hasDataText = false;
        this.legendNoLimit = false;
        this.divId = divId;
        this.sNumberSuffix = '';
        this.numberSuffix = '';
        this.prefix = '';
        this.sprefix = '';
        this.showSeriesName = true;
        this.sharedTooltip = false;
        this.maxDivXLines = 5;
        this.maxDivYLines = 5;
        this.gridLineWidth = 1;
        this.titleSize = '11px';
        this.title = '';

        this.options = {
            colors: ["rgba(180, 134, 180, 1)","rgba(226, 134, 134, 1)","rgba(90, 180, 180, 1)","rgba(255, 183, 137, 0.9)","rgba(181, 211, 93, 0.9)","rgba(246, 210, 99, 0.9)","rgba(204, 230, 250, 0.9)","rgba(203, 198, 90, 0.9)","rgba(192, 175, 210, 0.9)","rgba(146, 174, 114, 0.9)","rgba(192, 98, 101, 0.9)","rgba(90, 180, 226, 0.9)"],

            lang: {
                //customize the no data message
                noData: ''
            },
            noData: {
                position :{  x: 0, y: 0,  verticalAlign: "middle"}
            },
            chart:  {
                renderTo: this.divId
            },

            title: {
                text: null,
                style: {
                    fontSize: this.titleSize,
                    fontFamily: 'Arial',
                    fontWeight: 'bold'

                }
            },
            labels: {
                style: {
                    fontSize: '11px',
                    fontFamily: 'Arial'
                }
            }
            ,
            credits: {
                enabled: false
            },
            series: [],
            legend: {
                useHTML: true,
                x: 30,
                margin: 2,
                itemStyle: {
                    fontSize:'11px',
                    color: '#666666',
                    fontWeight:'normal'
                }
            }
        };

        this.setEmptyMessage = function(noData) {
            if (jQuery.browser.msie && jQuery.browser.version <= 8) {
                this.options.lang.noData = noData;
            }
        };

        this.isEmpty = function() {
            return this.options.series.length == 0;
        };
        this.getOptions = function() {
            return this.options;
        };

        this.render = function(value) {
            /*if (Math.abs(value) >= 1000 && Math.abs(value) < 1000000) {
                return this.toFixed((value / 1000), 1000) + 'K';
            } else if (Math.abs(value) >= 1000000) {
                return this.toFixed((value / 1000000), 1000000) + 'M';
            }*/
            return  this.toFixed(value, 100);
        };

        //fixed and round big numbers
        this.toFixed = function(num, fixed) {
            return Math.round(num * fixed) / (fixed);
        };

        //check for which yaxis show suffix
        this.checkSuffix = function(series) {
            if (series.type == "column") {
                return this.sNumberSuffix;
            } else {
                return this.numberSuffix;
            }
        };


        //check for which yaxis show suffix
        this.checkPrefix = function(series) {
            if (series.type == "column") {
                return this.sprefix;
            } else {
                return this.prefix;
            }
        };


        //chnage duration to num+str
        this.duration = function(millis) {
            var m = millis;
            var week = Math.floor(m / WEEK);
            m -= week * WEEK;
            var days = Math.floor(m / DAY);
            m -= days * DAY;
            var hours = Math.floor(m / HOUR);
            m -= hours * HOUR;
            var minutes = Math.floor(millis / MINUTE);
            m -= minutes * MINUTE;
            var seconds = Math.floor(m / SECOND);

            if (week > 0) {
                return this.toFixed(millis / WEEK, 100) + "wk";
            } else if (days > 0) {
                return this.toFixed(millis / DAY, 100) + "day";
            } else if (hours > 0) {
                return this.toFixed(millis / HOUR, 100) + "hr";
            } else if (minutes > 0) {
                return this.toFixed(millis / MINUTE, 100) + "min";
            } else {
                return this.toFixed(millis / SECOND, 100) + "sec";
            }
        };

        this.initData = function(json, type) {
            if (hasText(json.categories)) {
                if (type == 0) {
                    this.options.xAxis.categories = json.categories;
                } else {
                    this.options.xAxis.tickPositions = json.categories;
                }
                this.categories = json.categories;

            }
            if (hasText(json.series)) {
                this.options.series = json.series;
                this.series = json.series;
                if (this.series.length > 0) {
                    if (this.series[0].dataText != undefined) {
                        this.hasDataText = true;
                    }
                }

            }
            if (hasText(json.title)) {
                this.options.title.text = json.title;
            }

            if (json.xAxisTitle) {
                this.options.xAxis.title.text = json.xAxisTitle;
            }
            if (json.categoriesFontSize && this.options.xAxis) {
                this.options.xAxis.labels.style.fontSize = json.categoriesFontSize;
            }
            if (json.categoriesFont && this.options.xAxis) {
                this.options.xAxis.labels.style.fontFamily = json.categoriesFont;
            }
            if (hasText(json.categoryRotation)) {
                this.options.xAxis.labels.rotation = json.categoryRotation;
            }


            if (json.categoriesLabels) {
                this.categoriesLabels = json.categoriesLabels;
            }
            if (json.onclickResposne) {
                this.onclickResponse = json.onclickResposne;
                this.options.plotOptions.series.cursor = 'pointer'
            }

            if (json.colorByPoint) {
                this.options.plotOptions.series.colorByPoint = true;
            }

            if (hasText(json.sNumberSuffix)) {
                this.sNumberSuffix = json.sNumberSuffix;
            }

            if (hasText(json.numberSuffix)) {
                this.numberSuffix = json.numberSuffix;
            }

            if (hasText(json.prefix)) {
                this.prefix = json.prefix;
            }

            if (hasText(json.sprefix)) {
                this.sprefix = json.sprefix;
            }

            if (hasText(json.showSeriesName)) {
                this.showSeriesName = json.showSeriesName;
            }

            if (hasText(json.parseAsTime)) {
                this.parseAsTime = json.parseAsTime;
            }

            if (hasText(json.showStackLabels)) {
                this.options.yAxis.stackLabels.enabled = json.showStackLabels;
            }

            if (!(hasText(json.legendNoLimit) || json.legendNoLimit)) {
                this.options.legend.itemWidth = 180;
                this.options.legend.labelFormatter = function() {
                    if (this.name.length > 25) {
                        return this.name.slice(0, 25) + '...'
                    }
                    else {
                        return this.name
                    }
                };
            }

            if (hasText(json.appendWidth) && json.appendWidth) {
                this.options.plotOptions.series.appendWidth = json.appendWidth
            }

            if (hasText(json.colors) && json.colors) {
                this.options.colors = json.colors
            }
            if (hasText(json.pointRadius) && json.pointRadius) {
                this.options.plotOptions.scatter.marker.radius = json.pointRadius
            }
            if (hasText(json.sharedTooltip) && json.sharedTooltip) {
                this.options.tooltip.shared = json.sharedTooltip;
                this.sharedTooltip = json.sharedTooltip;
            }

            if (hasText(json.maxPointWidth)) {
                this.options.plotOptions.series.maxPointWidth = json.maxPointWidth;
                this.sharedTooltip = json.sharedTooltip;
            }
            if (hasText(json.maxDivXLines)) {
                this.maxDivXLines = json.maxDivXLines;
            }
            if (hasText(json.maxDivYLines)) {
                this.maxDivYLines = json.maxDivYLines;
            }

            if (hasText(json.gridLineWidth)) {
                this.gridLineWidth = json.gridLineWidth;
            }

            if (hasText(json.titleSize)) {
                this.titleSize = json.titleSize;
                this.options.title.style.fontSize = this.titleSize;
            }
        };
    }


    function graph2(parent)
    {
        Highcharts.chart(parent.divId, {
            title: {
                text: parent.title
            },
            chart: {
                zoomType: 'x',
                animation:false,
                plotBorderColor:'#ECEBEB',
                plotBorderWidth:1,
                style: {
                    fontSize:'11px',
                    fontWeight:'normal'
                },
                events: {
                    selection: function (event) {
                        try {
                            if (event.xAxis) {
                                var extremesObject = event.xAxis[0],
                                        min = extremesObject.min,
                                        max = extremesObject.max;
                                calculatePointRadius(this, max - min);
                            } else {
                                calculatePointRadius(this, parent.categories.length);
                            }
                        } catch(e) {
                        }
                    }
                }
            },
            xAxis: {
                labels: {
                    style: {
                        fontSize: '11px',
                        fontFamily: 'Arial'
                    }
                },
                categories: parent.categories,
                gridLineColor: '#ECEBEB',
                gridLineWidth:parent.gridLineWidth       ,
                startOnTick:true,
                //            showFirstLabel: true,
                tickColor:'#ffffff',
                //calculate the labels ticks
                tickPositioner: function (min, max) {
                    var t = [];
                    var minInterval = max - min;
                    var tick = Math.floor(minInterval / parent.maxDivXLines);
                    while (min <= max) {
                        var num = Math.floor(min);
                        if (num == 0) {
                            min++;
                            t.push(num);
                            continue;
                        }
                        if (minInterval <= parent.maxDivXLines || num % tick == 0) {
                            t.push(num);
                        }
                        min++;
                    }
                    return t;
                }
            },
            yAxis: [
                    {
                        minorGridLineColor: '#F9F9F9',
                        minorTickInterval: 'auto',
                        minorGridLineWidth: parent.gridLineWidth,
                        title: {
                            text: null
                        },
                        offset: -10,
                        labels: {
                            useHTML:true,
                            formatter: function() {
                                return parent.prefix + parent.render(this.value) + parent.numberSuffix;
                            }
                        },
                        gridLineColor: '#ECEBEB'
                    },
                    {
                        title: {
                            text: null
                        },
                        labels: {
                            enabled:false
                        },
                        gridLineColor: '#ECEBEB',
                        gridLineWidth:0
                    }
                ],
                tooltip: {
                    useHTML:true,
                        
                    formatter: function() {
                        if (parent.sharedTooltip) {
                            var s = this.x + "</br>";
                            jQuery.each(this.points, function () {
                                s += '<span style="color:' + this.series.color + '">â— </span>' + this.series.name + " ," + parent.prefix + parent.render(this.y) + parent.numberSuffix + ((this.series.index != parent.series.length - 1) ? '<br/>' : '');
                            });

                            return s;
                        }


                        return parent.hasDataText ? parent.series[this.series.index].dataText[this.point.index] : ((parent.showSeriesName ? this.series.name + '<br/>' : '') + this.x + '<br/><b>' + parent.checkPrefix(this.series) + parent.render(this.y) + parent.checkSuffix(this.series) + '</b>');
                    },
                    shared: false,
                    animation:false
                },
                plotOptions : {
                    animation: false,
                    column: {
                        borderWidth: 0.01
                    },
                    series:
                    {
                        maxPointWidth: 60,
                        connectNulls : true,
                        lineWidth:1,
                        animation: false,
                        marker:
                        {
                            fillColor: '#FFFFFF',
                            lineWidth: 1.3,
                            radius : 1.5,
                            lineColor: null,
                            enabled: false
                        },
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                },
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom',
                    itemStyle:{
                        fontWeight:"normal",
                        fontSize:"11px"
                    },
                    maxHeight:70
                },
                series: parent.series
        });
    }

    function createBalanceGraph2(balancevar,balanceid,balancelabel)
    {
        if(balancevar.status == 'ok')
        {
            var data = balancevar.response;
            var parent = new ChartParent2(balanceid);
            parent.categories = data.category;
            parent.showMarker= data.showMarker;
            parent.numberSuffix= data.sNumberSuffix;
            parent.series= data.series;
            parent.title= 'PhD ' + balancelabel;                    
            graph2(parent);
        }
    }

    
    function createMultistats2()
    {

            //var data = multistats.response;
            var st = '<ul class="nav">'+
                        '<li><label>Total Assets Traded </label><span class="text-success">'+ '<?php echo $tat; ?>' +'</span></li>'+
                        '<li><label>Number of Months traded</label><span>'+  '<?php echo $phd_total_stat->tnmt; ?>' +'</span></li>'+
                        '<li><label>Number of winning months </label><span>'+  '<?php echo $phd_total_stat->nwm; ?>' +'</span></li>'+
                        '<li><label>Number of losing months</label><span>'+  '<?php echo $phd_total_stat->nlm; ?>' +'</span></li>'+
                        '<li><label>Average monthly return </label><span>'+  '<?php echo $productcustomer->formatMoney($phd_total_stat->amr/100,2,''); ?>' +'%</span></li>'+
                        '<li><label>Biggest Drawdown  </label><span style="color:red;">'+   '<?php echo $productcustomer->formatMoney($phd_total_stat->bnm/100,2,''); ?>' +'%</span></li>'+
                        '<li class="margin"></li>'+
                        
                    '</ul>';
            jQuery('div#stats2').html(st);

    }
/*
*  $productcustomer->formatMoney(floatval($phd_stat->tat), 2, 'USD ') ;
* $result->tnmt = $tnmt;//Total Number of Months traded
        $result->nwm =  $nwm;//Number of winning months
        $result->nlm = $nlm ;//Number of losing months
        $result->amr = $amr ;//Average monthly return
        $result->bnm = $bnm ;//Biggest Drawdown (biggest negative month)
$phd_total_stat = $productcustomer->getPhDTotalStat($id_fx);
  $total_stat_isset
**/

</script>




</div>