<div class="customer-reviews">
  <h4>Statistics</h4>

  <?php 

  $productId = $this->product->virtuemart_product_id;

  // $productid = (isset($_GET['product']) && $_GET['product'] != '' ? $_GET['product'] : false);

  // Get a db connection.
  $db = JFactory::getDbo();
  // Create a new query object.
  $query = $db->getQuery(true);

  // $query->select(array('files_products.id', 'duplicum.account_id', 'virtuemart_products.published'));
  // $query->from('#__fxbotmarketx_files_products as files_products');
  // $query->join('LEFT', '#__fxbotmarketx_duplicum as duplicum ON duplicum.id_product = files_products.id');
  // $query->join('LEFT', '#__virtuemart_products as virtuemart_products ON virtuemart_products.virtuemart_product_id = files_products.product_id');
  // $query->where('files_products.id = '. $productid);
  // $db->setQuery($query);

  $query->select(array('fxbotmarketx.id', 'fxbotmarketx_duplicum.account_id', 'virtuemart.published', 'virtuemart.virtuemart_product_id'));
  $query->from('#__virtuemart_products as virtuemart');
  $query->join('LEFT', '#__fxbotmarketx_files_products as fxbotmarketx on fxbotmarketx.product_id = virtuemart.virtuemart_product_id');
  $query->join('LEFT', '#__fxbotmarketx_duplicum as fxbotmarketx_duplicum on fxbotmarketx_duplicum.id_product = fxbotmarketx.id');
  $query->where('virtuemart.virtuemart_product_id = '. $productId);
  $db->setQuery($query);

  $results = $db->loadAssoc();

  
$tapStatus = array('status' => 'error', 'msg' => 'record missing');
if(empty($results))
{
    $tapStatus = array('status' => 'error', 'msg' => 'No record found');
}
else if($results['published'] == 0)
{
    $tapStatus = array('status' => 'error', 'msg' => 'Account not published yet, Please contact to right person!.');
}
else if($results['account_id'] > 0)
{
    $userAccountId = $results['account_id'];
    // $userAccountId = 48776;
    // checking account exist in RDS database
    $url = "https://fxbot.market/taptrading/accountExist";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $account = curl_exec($ch);
    curl_close ($ch);

    if($account['response'])
    {
        $tapStatus = array('status' => 'success');
    }
    else
    {
        $tapStatus = array('status' => 'error', 'msg' => 'You TAP Account not exist, Please contact to right person!.');
    }
}


if($tapStatus['status'] == 'success')
{
  // get balance
  $url = "https://dev.fxbot.market/taptrading/getbalance";
  $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $balance = curl_exec($ch);
  curl_close ($ch);

  // get growth
  $url = "https://dev.fxbot.market/taptrading/getgrowth";
  $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $growth = curl_exec($ch);
  curl_close ($ch);

  // get profit
  $url = "https://dev.fxbot.market/taptrading/getprofit";
  $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $profit = curl_exec($ch);
  curl_close ($ch);

  // get drawdown
  $url = "https://dev.fxbot.market/taptrading/getdrawdown";
  $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $drawdown = curl_exec($ch);
  curl_close ($ch);

    // get multistats
    $url = "https://dev.fxbot.market/taptrading/multistats";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $multistats = curl_exec($ch);
    curl_close ($ch);

    // get averageholdtime
    $url = "https://dev.fxbot.market/taptrading/averageholdtime";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $averageholdtime = curl_exec($ch);
    curl_close ($ch);

    // get currencypopularity
    $url = "https://dev.fxbot.market/taptrading/currencypopularity";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $currencypopularity = curl_exec($ch);
    curl_close ($ch);

    // get monthReport
    $url = "https://dev.fxbot.market/taptrading/getyearlydata";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $monthReport = curl_exec($ch);
    curl_close ($ch);

    // get capsulestats
    $url = "https://dev.fxbot.market/taptrading/capsulestats";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,  "account_id=$userAccountId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $capsulestats = curl_exec($ch);
    curl_close ($ch);

 //    var_dump($userAccountId);
  // echo '<pre>';
  // print_r($capsulestats);die;
}
?>

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
            if($tapStatus['status'] == 'success')
            {
                ?>
            
                <div class="col-md-3 cus-p">
                    <div class="tab-sidebar">
                        <h3>Information</h3>
                        <div id="stats" class="cus-design">
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="ull-left tab-tabbar">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-balance-scale"></i> Balance</a></li>
                            <li role="presentation"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                                <i class="fa fa-line-chart"></i> Growth</a></li>
                            <li role="presentation">
                                <a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 480 480" style="enable-background:new 0 0 480 480;" xml:space="preserve">
                                        <g>
                                            <g>
                                                <path d="M408,127.992l-72,112h47.488c0,79.12-64.368,143.488-143.488,143.488c-46.4,0-87.616-22.24-113.856-56.512l-27.168,42.256    C133.984,407.4,184.112,431.496,240,431.496c105.76,0,191.488-85.728,191.488-191.488H480L408,127.992z" fill=""/>
                                            </g>
                                        </g>
                                        <g>
                                            <g>
                                                <path d="M240,48.504c-105.76,0-191.488,85.728-191.488,191.488H0l72,112l72-112H96.512c0-79.12,64.368-143.488,143.488-143.488    c46.4,0,87.616,22.24,113.856,56.512l27.168-42.256C346.016,72.584,295.888,48.504,240,48.504z" fill=""/>
                                            </g>
                                        </g>
                                        <g>
                                            <g>
                                                <rect x="160.001" y="208.072" width="32" height="112" fill=""/>
                                            </g>
                                        </g>
                                        <g>
                                            <g>
                                                <rect x="224.001" y="144.072" width="32" height="176" fill=""/>
                                            </g>
                                        </g>
                                        <g>
                                            <g>
                                                <rect x="288.001" y="176.072" width="32" height="144" fill=""/>
                                            </g>
                                        </g>
                                        </svg> Profit
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px"  viewBox="0 0 114.595 114.594" style="enable-background:new 0 0 114.595 114.594;" xml:space="preserve">
                                        <g>
                                            <g>
                                                <path d="M99.287,101.797h-7.121v-4.834c0-0.275-0.225-0.5-0.5-0.5c-0.276,0-0.5,0.225-0.5,0.5v4.834h-9v-4.834    c0-0.275-0.225-0.5-0.5-0.5c-0.276,0-0.5,0.225-0.5,0.5v4.834h-9v-4.834c0-0.275-0.225-0.5-0.5-0.5c-0.276,0-0.5,0.225-0.5,0.5    v4.834h-9v-4.834c0-0.275-0.225-0.5-0.5-0.5c-0.276,0-0.5,0.225-0.5,0.5v4.834h-9v-4.834c0-0.275-0.224-0.5-0.5-0.5    c-0.276,0-0.5,0.225-0.5,0.5v4.834h-9v-4.834c0-0.275-0.224-0.5-0.5-0.5c-0.276,0-0.5,0.225-0.5,0.5v4.834h-9v-4.834    c0-0.275-0.224-0.5-0.5-0.5c-0.276,0-0.5,0.225-0.5,0.5v4.834h-9v-4.834c0-0.275-0.224-0.5-0.5-0.5c-0.276,0-0.5,0.225-0.5,0.5    v4.834H3V90.464h4.016c0.276,0,0.5-0.224,0.5-0.5s-0.224-0.5-0.5-0.5H3v-9h4.016c0.276,0,0.5-0.224,0.5-0.5s-0.224-0.5-0.5-0.5H3    v-9h4.016c0.276,0,0.5-0.224,0.5-0.5s-0.224-0.5-0.5-0.5H3v-9h4.016c0.276,0,0.5-0.224,0.5-0.5s-0.224-0.5-0.5-0.5H3v-9h4.016    c0.276,0,0.5-0.224,0.5-0.5s-0.224-0.5-0.5-0.5H3v-9h4.016c0.276,0,0.5-0.224,0.5-0.5s-0.224-0.5-0.5-0.5H3v-9h4.016    c0.276,0,0.5-0.224,0.5-0.5s-0.224-0.5-0.5-0.5H3v-9h4.016c0.276,0,0.5-0.224,0.5-0.5s-0.224-0.5-0.5-0.5H3v-8.167    c0-0.828-0.672-1.5-1.5-1.5c-0.828,0-1.5,0.672-1.5,1.5v92c0,0.828,0.672,1.5,1.5,1.5h97.787c0.828,0,1.5-0.672,1.5-1.5    S100.115,101.797,99.287,101.797z"/>
                                                <rect x="15.644" y="23.4" width="10.25" height="67.795"/>
                                                <path d="M47.644,55.306c-1.342-1.754-2.43-3.641-3.262-5.611h-6.988v41.5h10.25V55.306z"/>
                                                <path d="M69.145,65.403c-0.332,0.014-0.664,0.035-0.996,0.035c-3.218,0-6.34-0.59-9.254-1.705v27.461h10.25V65.403z" />
                                                <path d="M91.645,71.571l-9.863-10.008c-0.127,0.078-0.26,0.146-0.387,0.221v29.41h10.25V71.571z" />
                                                <path d="M113.751,81.944L86.074,53.861c6.94-8.952,6.322-21.913-1.895-30.129c-8.906-8.907-23.4-8.907-32.309,0    c-8.906,8.908-8.906,23.4,0,32.309c8.217,8.217,21.176,8.834,30.13,1.895l27.677,28.081c1.125,1.125,2.947,1.125,4.072,0.001    C114.876,84.893,114.876,83.069,113.751,81.944z M80.109,51.968c-6.662,6.662-17.502,6.663-24.164,0    c-6.662-6.661-6.661-17.501,0-24.162c6.662-6.661,17.501-6.663,24.164,0C86.771,34.467,86.771,45.307,80.109,51.968z" fill=""/>
                                                <path d="M68.859,37.443c-2.613-1.081-3.369-1.735-3.369-2.919c0-0.946,0.693-2.052,2.645-2.052c1.837,0,2.993,0.661,3.487,0.943    c0.126,0.073,0.278,0.085,0.417,0.037c0.138-0.05,0.246-0.156,0.299-0.293l0.742-1.96c0.087-0.228-0.004-0.485-0.215-0.608    c-1.063-0.619-2.248-0.974-3.608-1.077v-2.405c0-0.276-0.224-0.5-0.5-0.5h-1.838c-0.276,0-0.5,0.224-0.5,0.5v2.583    c-2.735,0.604-4.478,2.603-4.478,5.188c0,3.119,2.504,4.533,5.203,5.595c2.159,0.873,3.039,1.762,3.039,3.064    c0,1.439-1.218,2.406-3.027,2.406c-1.4,0-2.856-0.434-3.994-1.19c-0.127-0.086-0.287-0.106-0.434-0.059    c-0.147,0.047-0.263,0.161-0.313,0.306l-0.712,1.985c-0.075,0.211-0.002,0.446,0.181,0.576c1.04,0.742,2.696,1.264,4.327,1.377    v2.516c0,0.276,0.225,0.5,0.5,0.5h1.868c0.276,0,0.5-0.224,0.5-0.5v-2.671c2.819-0.631,4.686-2.778,4.686-5.454    C73.764,40.604,72.296,38.843,68.859,37.443z" fill=""/>
                                            </g>
                                        </svg>
                                         Drawdown
                                </a>
                            </li>
                            <li class="pull-right">
                                <div class="btn-group pull-righ">
                                    <div class="btn-group">
                                        <a href="#" class="btn  dropdown-toggle" data-toggle="dropdown">
                                            <span class="glyphicon glyphicon-cog"></span>
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="javascript:void(0)">Data PDF</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="clearfix"></div>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="profile">
                            <div id="balance" style="width: 100%; height: 400px; margin: 0 auto"></div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="home">
                            <div id="graphLine" style="width: 100%; height: 400px; margin: 0 auto"></div>
                            
                        </div>
                        <div role="tabpanel" class="tab-pane" id="messages">
                            <div id="profit" style="width: 100%; height: 400px; margin: 0 auto"></div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="settings">
                            <div id="drawDown" style="width: 100%; height: 400px; margin: 0 auto"></div>
                        </div>
                    </div>

                    <div class="custom-charttab">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Jan</th>
                                    <th>Feb</th>
                                    <th>Mar</th>
                                    <th>Apr</th>
                                    <th>May</th>
                                    <th>Jun</th>
                                    <th>Jul</th>
                                    <th>Aug</th>
                                    <th>Sep</th>
                                    <th>Oct</th>
                                    <th>Nov</th>
                                    <th>Dec</th>
                                    <th>YTD</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-tabbar">
                        <ul class="nav nav-tabs " role="tablist" id="yearlyLi">
                            
                        </ul>
                        <div class="tab-content" id="yearlyDiv">
                            
                        </div>
                    </div>
                    <div id="currencyPopularity">>
                        <div id="container" style="width: 100%;"></div>
                    </div>
                    <div id="averageHoldTime">
                        <div id="averageHoldTimeBar" style="width: 100%;"></div>
                    </div>

                </div>
            <?php
            }
            else
            {
                echo '<p>'.$tapStatus['msg'].'</p>';
            }
            ?>
            
            
        </div>

       
    </div>
</form>

<script type="text/javascript">
jQuery.noConflict();
    var balance = <?= $balance ?>;
    var growth = <?= $growth ?>;
    var profit = <?= $profit ?>;
    var drawdown = <?= $drawdown ?>;
    var multistats = <?= $multistats ?>;
    var averageholdtime = <?= $averageholdtime ?>;
    var currencypopularity = <?= $currencypopularity ?>;
    var monthReport = '<?= $monthReport ?>';
    var capsulestats = <?= $capsulestats ?>;

    jQuery(document).ready(function(){
        createBalanceGraph();
        createGrowthGraph();
        createProfitGraph();
        createDropdownGraph();
        createMultistats();
        createAverageholdtime();
        createCurrencypopularity();
        creatMonthReport();
        creatCapsulestats();
    });

    function ChartParent(divId) 
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
            if (Math.abs(value) >= 1000 && Math.abs(value) < 1000000) {
                return this.toFixed((value / 1000), 1000) + 'K';
            } else if (Math.abs(value) >= 1000000) {
                return this.toFixed((value / 1000000), 1000000) + 'M';
            }
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

    function chatColumnParent(divId)
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
    }

    function chatColumnParent(divId)
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
    }

    function graph(parent)
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

    function createBalanceGraph()
    {
        if(balance.status == 'ok')
        {
            var data = balance.response;
            var parent = new ChartParent('balance');
            parent.categories = data.category;
            parent.showMarker= data.showMarker;
            parent.numberSuffix= data.sNumberSuffix;
            parent.series= data.series;
            parent.title= 'Balance';                    
            graph(parent);
        }
    }

    function createGrowthGraph()
    {
        if(growth.status == 'ok')
        {
            var data = growth.response;
            var parent = new ChartParent('graphLine');
            parent.categories = data.category;
            parent.showMarker= data.showMarker;
            parent.numberSuffix= data.sNumberSuffix;
            parent.series= data.series;
            parent.title = 'Growth';
            graph(parent);
        }
    }

    function createProfitGraph()
    {
        if(profit.status == 'ok')
        {
            var data = profit.response;
            var parent = new ChartParent('profit');
            parent.categories = data.category;
            parent.showMarker= data.showMarker;
            parent.numberSuffix= data.sNumberSuffix;
            parent.series= data.series;
            parent.title= 'Profit';                    
            graph(parent);
        }
    }

    function createDropdownGraph()
    {
        if(drawdown.status == 'ok')
        {
            var data = drawdown.response;
            var parent = new ChartParent('drawDown');
            parent.categories = data.category;
            parent.showMarker= data.showMarker;
            parent.numberSuffix= data.sNumberSuffix;
            parent.series= data.series;
            parent.title= 'Drawdown';                    
            graph(parent);
        }
    }

    function createMultistats()
    {
        if(multistats.status == 'ok')
        {
            var data = multistats.response;
            var st = '<ul class="nav">'+
                        '<li><label>Total Gain :</label><span class="'+ (data.totalGain > 0 ? 'text-success' : '') +'"><strong>'+ data.totalGain +'%</strong></span></li>'+
                        '<li><label>Monthly Average</label><span>'+ data.monthlyAverage +'%</span></li>'+
                        '<li><label>Inception Date </label><span>'+ data.inceptionDate +'</span></li>'+
                        '<li><label>Total months </label><span>'+ data.totalMonth +'</span></li>'+
                        '<li><label>Best month </label><span class="'+ (data.bestMonth > 0 ? 'text-success' : '') +'">'+ data.bestMonth +'%</span></li>'+
                        '<li><label>Worst month </label><span class="'+ (data.worstMonth > 0 ? 'text-danger' : '') +'">'+ data.worstMonth +'%</span></li>'+
                        '<li class="margin"></li>'+
                        '<li><label>Total Trades </label><span>'+ data.totalTade +'</span></li>'+
                        '<li><label>Trades per week </label><span>'+ data.weekTrade +'</span></li>'+
                        '<li><label>Average Hold Time </label><span>'+ data.averageHoldTime +' Sec</span></li>'+
                        '<li><label>Winner Percentage </label><span>'+ data.winnerPercentage +'%</span></li>'+
                        '<li><label>Total Volume </label><span>'+ data.totalVolume +'</span></li>'+
                        '<li><label>Monthly Volume </label><span>'+ data.monthlyVolume +'</span></li>'+
                        '<li><label>Turnover </label><span>'+ data.turnover +'</span></li>'+
                        '<li><label>Monthly Turnover </label><span>'+ data.monthlyTurnover +'</span></li>'+
                    '</ul>';
            jQuery('div#stats').html(st);
        }
    }

    function createAverageholdtime()
    {
        if(averageholdtime.status == 'ok')
        {
            var data = averageholdtime.response;
            Highcharts.chart('averageHoldTimeBar', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: 'Average Holding Time'
                },
                xAxis: {
                    categories: data.category,
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Time (Hours)',
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },
                tooltip: {
                    valueSuffix: ' Hours'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                credits: {
                    enabled: true
                },
                    series: [{
                        data: data.data
                    }]
            });
        }
    }

    function createCurrencypopularity()
    {
        if(currencypopularity.status == 'ok')
        {
            var dataStr = currencypopularity.response;
            Highcharts.chart('currencyPopularity', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Currency Popularity'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Currency Popularity',
                    colorByPoint: true,
                    data: dataStr
                }]
            });
        }
    }

    function creatMonthReport()
    {
        if(monthReport.status == 'ok')
        {
            var dataStrTab = monthReport.response.key;
            var li = div = '';
            for (var o = 0; o < dataStrTab.length; o++) 
            {
                var classs = o == 0 ? 'active' : ''; 

                li += '<li role="presentation" class="'+ classs +'"><a href="#y'+ dataStrTab[o] +'" aria-controls="home" role="tab" data-toggle="tab">'+ dataStrTab[o] +'</a></li>';
                
                div += '<div role="tabpanel" class="tab-pane '+ classs +'" id="y'+ dataStrTab[o] +'">'+
                            '<div id="monthGain'+ dataStrTab[o] +'" style="width: 100%; height: 400px; margin: 0 auto"></div>'+
                        '</div>';
            }

            jQuery('ul#yearlyLi').html(li);
            jQuery('div#yearlyDiv').html(div);

            var dataStrTab = monthReport.response.finalData;
            for (var i = 0; i < dataStrTab.length; i++) 
            {
                var parent = new chatColumnParent('monthGain'+dataStrTab[i].tab);
                parent.categories = dataStrTab[i].category;
                parent.showMarker= monthReport.response.showMarker;
                parent.numberSuffix= monthReport.response.sNumberSuffix;
                parent.series= dataStrTab[i].data;
                parent.title= 'Monthly Gain(change)';                    
                monthReportGraph(parent);
            }
        }
    }

    function monthReportGraph(parent)
    {
        Highcharts.chart(parent.divId, {
            chart: {
                zoomType: 'x'
            },
            title: {
                text: parent.title
            },
            subtitle: {
                text:' '
            },
            xAxis: {
                categories: parent.categories,
            },
            yAxis: [{ // left y axis
                    title: {
                        text: ''
                    },
                    showFirstLabel: false,
                    labels: {
                        enabled: true,
                         useHTML:true,
                        formatter: function () {
                            return this.value+parent.numberSuffix;
                        }
                    },
                }],
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: false
                    },
                    enableMouseTracking: true,
                },
            },
             tooltip: {
                shared: true,
                crosshairs: true
            },

            series: [ {
                name: '',
                type: 'column',
                data: parent.series
            }]
        });
    }

    function creatCapsulestats()
    {
        if(capsulestats.status == 'ok')
        {
            var dataRep = capsulestats.response;
            jQuery('table tbody').html(dataRep.data);
            jQuery('[data-toggle="tooltip"]').tooltip();
        }
    }
</script>

</div>