<script src="lib/bootstrap/js/jquery.pin.js"></script>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<!-- pho页面代码-开始 -->
<script src="lib/echarts4/echarts.min.js"></script>
<script src="lib/echarts4/dark.js"></script>
<style>
body {
    background: url(images/bg.png) left top repeat-y #2A2A2A !important;
}

.content {
    background: #2A2A2A;
}

footer hr {
    border: 0px;
    border-top: 1px solid #3c3b3b;
}
</style>
<div class="indexpage">
    <ul class="breadcrumb">
        <li><a href="<?php echo site_url('index/index'); ?>">主页</a> <span class="divider">/</span></li>
        <li class="active">工作台</li>
    </ul>
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"><i class="iconfont icon-ai222"></i>Oracle</div>
                            <div class="pull-right"><a href="<?php echo site_url('wl_oracle/index'); ?>">查看详细<i class="iconfont icon-gengduo"></i></a>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="row">
                                <div class="col-md-6 tbox1"> <?php echo $oracle_cfg_total ?> 
                                </div>
                                <div class="col-md-6 tbox2">
                                    <p class="box_check"><i class="iconfont icon-ziyuan"></i> <?php echo $oracle_active_count ?> </p>
                                    <p class="box_times"><i class="iconfont icon-icon2"></i> <?php echo $oracle_inactive_count ?> </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"><i class="iconfont icon-ai222"></i>MySQL</div>
                            <div class="pull-right"><a href="<?php echo site_url('wl_mysql/index'); ?>">查看详细<i class="iconfont icon-gengduo"></i></a>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="row">
                                <div class="col-md-6 tbox1"> <?php echo $mysql_cfg_total ?> 
                                </div>
                                <div class="col-md-6 tbox2">
                                    <p class="box_check"><i class="iconfont icon-ziyuan"></i> <?php echo $mysql_active_count ?> </p>
                                    <p class="box_times"><i class="iconfont icon-icon2"></i> <?php echo $mysql_inactive_count ?> </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"><i class="iconfont icon-ai222"></i>SQLServer</div>
                            <div class="pull-right"><a href="<?php echo site_url('wl_sqlserver/index'); ?>">查看详细<i class="iconfont icon-gengduo"></i></a>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="row">
                                <div class="col-md-6 tbox1"> <?php echo $sqlserver_cfg_total ?> 
                                </div>
                                <div class="col-md-6 tbox2">
                                    <p class="box_check"><i class="iconfont icon-ziyuan"></i> <?php echo $sqlserver_active_count ?> </p>
                                    <p class="box_times"><i class="iconfont icon-icon2"></i> <?php echo $sqlserver_inactive_count ?> </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="col-md-12">
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left"><i class="iconfont icon-ai222"></i>数据库同步延迟情况</div>
                                <div class="pull-right"><a href="<?php echo site_url('wl_oracle/dglist'); ?>">查看详细<i class="iconfont icon-gengduo"></i></a>
                                </div>
                            </div>
                            <div class="block-content">
                                <div id="container" style="height: 300px"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="col-md-12">
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left"><i class="iconfont icon-ai222"></i>数据库主机资源情况</div>
                                <div class="pull-right"><a href="<?php echo site_url('wl_os/index'); ?>">查看详细<i class="iconfont icon-gengduo"></i></a>
                                </div>
                            </div>
                            <div class="block-content">
                                <table class="table tooltip-wlblazers">
                                    <thead>
                                        <tr>
                                            <th style="width:120px;">主机IP</th>
                                            <th style="width:120px;">cpu</th>
                                            <th style="width:120px;">内存</th>
                                            <th style="width:120px;">I/O</th>
                                            <th style="width:120px;">网络</th>
                                            <th style="width:120px;">数据库类型</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- √×!分别对应不同的<i>标签,参考demo
                                        <tr>
                                            <td><i class="iconfont icon-ziyuan"></i>demo</td>
                                            <td><i class="iconfont icon-icon2"></i>demo</td>
                                            <td><i class="iconfont icon-jinggao1"></i>demo</td>
                                            <td>demo</td>
                                            <td>demo</td>
                                            <td>demo</td>
                                        </tr> -->
                                         <?php if(!empty($db_status)) {?>
                                         <?php foreach ($db_status  as $item):?>
                                         	<tr style="font-size: 12px;">
                                         		<td><?php echo $item['host'] ?></td>
                                         		<td><?php echo check_db_status_level_new($item['cpu'],$item['cpu_tips']) ?></td>
                                         		<td><?php echo check_db_status_level_new($item['memory'],$item['memory_tips']) ?></td>
                                         		<td><?php echo check_db_status_level_new($item['disk'],$item['disk_tips']) ?></td>
                                         		<td><?php echo check_db_status_level_new($item['network'],$item['network_tips']) ?></td>
                                         		<td><?php echo check_dbtype($item['db_type']) ?></td>
                                           </tr>
                                         <?php endforeach;?>
                                         <?php }else{  ?>
                                         		<tr>
                                         		<td colspan="16">
                                         		<font color="red"><?php echo $this->lang->line('no_record'); ?></font>
                                         		</td>
                                         </tr>
                                         <?php } ?>  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"><i class="iconfont icon-ai222"></i>数据库容灾同步情况</div>
                            <div class="pull-right"><a href="#">查看详细<i class="iconfont icon-gengduo"></i></a>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="in4">
                                <ul>
                                    <li>
                                        <div class="in4_1">Oracle</div>
                                        <div class="cf">
                                            <div class="c1">
                                                <div class="c1box"><span></span><span></span><span></span><span></span><span></span>
                                                    <div class="cschedule cschedule_green" style="height:30%;"></div>
                                                    <!-- 绿色cschedule_green,黄色cschedule_yellow,红色cschedule_red style="height:xx%;"里面的百分数通过实际情况计算显示不同高度 -->
                                                </div>
                                            </div>
                                            <div class="c2right">
                                                <div class="c2 co1">
                                                    <div>
                                                        <p class="c3"><?php echo $oracle_high ?></p>
                                                        <p class="c4">High</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co2">
                                                    <div>
                                                        <p class="c3"><?php echo $oracle_medium ?></p>
                                                        <p class="c4">Medium</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co3">
                                                    <div>
                                                        <p class="c3"><?php echo $oracle_low ?></p>
                                                        <p class="c4">Low</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                     <li>
                                        <div class="in4_1">MySQL</div>
                                        <div class="cf">
                                            <div class="c1">
                                                <div class="c1box"><span></span><span></span><span></span><span></span><span></span>
                                                    <div class="cschedule cschedule_yellow" style="height:60%;"></div>
                                                    <!-- 绿色cschedule_green,黄色cschedule_yellow,红色cschedule_red style="height:xx%;"里面的百分数通过实际情况计算显示不同高度 -->
                                                </div>
                                            </div>
                                            <div class="c2right">
                                                <div class="c2 co1">
                                                    <div>
                                                        <p class="c3"><?php echo $mysql_high ?></p>
                                                        <p class="c4">High</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co2">
                                                    <div>
                                                        <p class="c3"><?php echo $mysql_medium ?></p>
                                                        <p class="c4">Medium</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co3">
                                                    <div>
                                                        <p class="c3"><?php echo $mysql_low ?></p>
                                                        <p class="c4">Low</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                     <li>
                                        <div class="in4_1">SQLServer</div>
                                        <div class="cf">
                                            <div class="c1">
                                                <div class="c1box"><span></span><span></span><span></span><span></span><span></span>
                                                    <div class="cschedule cschedule_red" style="height:100%;"></div>
                                                    <!-- 绿色cschedule_green,黄色cschedule_yellow,红色cschedule_red style="height:xx%;"里面的百分数通过实际情况计算显示不同高度 -->
                                                </div>
                                            </div>
                                            <div class="c2right">
                                                <div class="c2 co1">
                                                    <div>
                                                        <p class="c3"><?php echo $sqlserver_high ?></p>
                                                        <p class="c4">High</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co2">
                                                    <div>
                                                        <p class="c3"><?php echo $sqlserver_medium ?></p>
                                                        <p class="c4">Medium</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co3">
                                                    <div>
                                                        <p class="c3"><?php echo $sqlserver_low ?></p>
                                                        <p class="c4">Low</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"><i class="iconfont icon-ai222"></i>告警显示</div>
                            <div class="pull-right"><a href="<?php echo site_url('alarm/index'); ?>">查看详细<i class="iconfont icon-gengduo"></i></a>
                            </div>
                        </div>
                        <div class="block-content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <div>
                                            <th style="width:60px;">标签</th>
                                            <th style="width:60px;">类型</th>
                                            <th>告警内容</th>
                                        </div>
                                    </tr>
                                </thead>
                                <tbody>
                                	<?php if(!empty($alarm)) {?>
                                 	<?php foreach ($alarm  as $item):?>
                                    <tr>
                                        <td><?php echo $item['tags'] ?></td>
                                        <td><?php echo $item['db_type'] ?></td>
                                        <td><?php echo $item['message'] ?></td>
                                    </tr>
                                	<?php endforeach;?>
                                	<?php }else{  ?>
                                			<tr>
                                			<td colspan="16">
                                			<font color="red"><?php echo $this->lang->line('no_record'); ?></font>
                                			</td>
                                			</tr>
                                	<?php } ?>    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var dom = document.getElementById("container");
var myChart = echarts.init(dom, 'dark');
var app = {};
var option = null;
app.title = '多 X 轴示例';

var colors = ['#5793f3', '#d14a61', '#675bba'];

var url = "<?php echo site_url('index/series'); ?>";

jQuery(document).ready(function(){
		getSeriesData(url);
		
		oTimer = setInterval("getSeriesData(url)",60000);
		
});  

function getSeriesData(url){
    $.get(url, function(json){
    		//alert(json.server_id);
    		//alert(json.time);
    		//alert(json.delay);

        //var status = 1;
				option = {
				    color: colors,
				
				    tooltip: {
				        trigger: 'none',
				        axisPointer: {
				            type: 'cross'
				        }
				    },
				    legend: {
				        data: json.server_id
				    },
				    grid: {
				        top: 70,
				        bottom: 50
				    },
				    xAxis: {
				        type: 'category',
				        axisTick: {
				            alignWithLabel: true
				        },
				        axisLine: {
				            onZero: false,
				            lineStyle: {
				                color: colors[0]
				            }
				        },
				        axisPointer: {
				            label: {
				                formatter: function(params) {
				                    return params.value + ' 延时' + (params.seriesData.length ? '：' + params.seriesData[0].data[1] : '');
				                }
				            }
				        },
				        data: json.time
				    },
				    yAxis: [{
				        type: 'value'
				    }],
				    series: [{
				        name: json.server_id,
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.delay
				    }]
				};;
				if (option && typeof option === "object") {
				    myChart.setOption(option, true);
				}
    },'json');  
}  
</script>
<!-- pho页面代码-结束 -->




