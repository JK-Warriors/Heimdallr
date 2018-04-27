<script src="lib/bootstrap/js/jquery.pin.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>

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
html{    background: #2A2A2A;}

</style>
<script type="text/javascript">
    // 滚动效果
    (function($) {
        $.fn.myScroll = function(options) {
            var defaults = {
                speed: 40, 
                rowHeight: 24
            };

            var opts = $.extend({}, defaults, options),
                intId = [];

            function marquee(obj, step) {

                obj.find("ul").animate({
                    marginTop: '-=1'
                }, 0, function() {
                    var s = Math.abs(parseInt($(this).css("margin-top")));
                    if (s >= step) {
                        $(this).find("li").slice(0, 1).appendTo($(this));
                        $(this).css("margin-top", 0);
                    }
                });
            }

            this.each(function(i) {
                var sh = opts["rowHeight"],
                    speed = opts["speed"],
                    _this = $(this);
                intId[i] = setInterval(function() {
                    if (_this.find("ul").height() <= _this.height()) {
                        clearInterval(intId[i]);
                    } else {
                        marquee(_this, sh);
                    }
                }, speed);

                _this.hover(function() {
                    clearInterval(intId[i]);
                }, function() {
                    intId[i] = setInterval(function() {
                        if (_this.find("ul").height() <= _this.height()) {
                            clearInterval(intId[i]);
                        } else {
                            marquee(_this, sh);
                        }
                    }, speed);
                });

            });

        }

    })(jQuery);
    $(function() {
        $("div.list_lh").myScroll({
            speed: 160, //数值越大，速度越慢
            rowHeight: 37 //li的高度
        });
        $("div.list_lh2").myScroll({
            speed: 160, //数值越大，速度越慢
            rowHeight: 37 //li的高度
        });
    });
    </script>
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
				
			
				
				
<!-- 滚动部分静态代码 开始-->
                        <div class="">
                            <div class="col-md-12">
                                <div class="block">
                                    <div class="navbar navbar-inner block-header">
                                        <div class="muted pull-left"><i class="iconfont icon-ai222"></i>数据库主机资源情况</div>
                                        <div class="pull-right"><a href="<?php echo site_url('wl_os/index'); ?>">查看详细<i class="iconfont icon-gengduo"></i></a>
                                        </div>
                                    </div>
                                    <div class="block-content" style="height: 253px;">
                                        <table class="table" style="margin:0;">
                                            <thead>
                                                <tr>
                                                    <th>数据库名</th>
                                                    <th style="width:120px;">cpu</th>
                                                    <th style="width:120px;">内存</th>
                                                    <th style="width:120px;">I/O</th>
                                                    <th style="width:120px;">网络</th>
                                                    <th style="width:120px;">数据库类型</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <div class="list_lh">
                                            <ul>
	                                         <?php if(!empty($db_status)) {?>
	                                         <?php foreach ($db_status  as $item):?>
                                             <li>
                                               <table class="table">
			                                         	<tr style="font-size: 12px;">
			                                         		<td><?php echo $item['host'] ?></td>
			                                         		<td style="width:120px;"><?php echo check_db_status_level_new($item['cpu'],$item['cpu_tips']) ?></td>
			                                         		<td style="width:120px;"><?php echo check_db_status_level_new($item['memory'],$item['memory_tips']) ?></td>
			                                         		<td style="width:120px;"><?php echo check_db_status_level_new($item['disk'],$item['disk_tips']) ?></td>
			                                         		<td style="width:120px;"><?php echo check_db_status_level_new($item['network'],$item['network_tips']) ?></td>
			                                         		<td style="width:120px;"><?php echo check_dbtype($item['db_type']) ?></td>
			                                           </tr>
                                               </table>
                                             </li>
	                                         <?php endforeach;?>
	                                         <?php }else{  ?>
                                                <li>
                                                    <table class="table">
					                                         		<tr>
						                                         		<td colspan="16">
						                                         		<font color="red"><?php echo $this->lang->line('no_record'); ?></font>
						                                         		</td>
					                                         		</tr>
                                                    </table>
                                                </li>
	                                         <?php } ?>  
                                                
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
				
<!-- 滚动部分静态代码 结束-->
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"><i class="iconfont icon-ai222"></i>容灾</div>
                            <div class="pull-right"> <!-- <a href="#">查看详细<i class="iconfont icon-gengduo"></i></a> -->
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
                                                    <div class="cschedule <?php echo check_repl_color($oracle_normal, $oracle_waring, $oracle_critical) ?> >" style="height:<?php echo check_repl_rate($oracle_normal, $oracle_waring, $oracle_critical) ?>%;"></div>
                                                    <!-- 绿色cschedule_green,黄色cschedule_yellow,红色cschedule_red style="height:xx%;"里面的百分数通过实际情况计算显示不同高度 -->
                                                </div>
                                            </div>
                                            <div class="c2right">
                                                <div class="c2 co1">
                                                    <div>
                                                        <p class="c3"><?php echo $oracle_normal ?></p>
                                                        <p class="c4">正常</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co2">
                                                    <div>
                                                        <p class="c3"><?php echo $oracle_waring ?></p>
                                                        <p class="c4">告警</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co3">
                                                    <div>
                                                        <p class="c3"><?php echo $oracle_critical ?></p>
                                                        <p class="c4">异常</p>
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
                                                    <div class="cschedule <?php echo check_repl_color($mysql_normal, $mysql_waring, $mysql_critical) ?>" style="height:<?php echo check_repl_rate($mysql_normal, $mysql_waring, $mysql_critical) ?>%;"></div>
                                                    <!-- 绿色cschedule_green,黄色cschedule_yellow,红色cschedule_red style="height:xx%;"里面的百分数通过实际情况计算显示不同高度 -->
                                                </div>
                                            </div>
                                            <div class="c2right">
                                                <div class="c2 co1">
                                                    <div>
                                                        <p class="c3"><?php echo $mysql_normal ?></p>
                                                        <p class="c4">正常</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co2">
                                                    <div>
                                                        <p class="c3"><?php echo $mysql_waring ?></p>
                                                        <p class="c4">告警</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co3">
                                                    <div>
                                                        <p class="c3"><?php echo $mysql_critical ?></p>
                                                        <p class="c4">异常</p>
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
                                                    <div class="cschedule <?php echo check_repl_color($sqlserver_normal, $sqlserver_waring, $sqlserver_critical) ?>" style="height:<?php echo check_repl_rate($sqlserver_normal, $sqlserver_waring, $sqlserver_critical) ?>%;"></div>
                                                    <!-- 绿色cschedule_green,黄色cschedule_yellow,红色cschedule_red style="height:xx%;"里面的百分数通过实际情况计算显示不同高度 -->
                                                </div>
                                            </div>
                                            <div class="c2right">
                                                <div class="c2 co1">
                                                    <div>
                                                        <p class="c3"><?php echo $sqlserver_normal ?></p>
                                                        <p class="c4">正常</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co2">
                                                    <div>
                                                        <p class="c3"><?php echo $sqlserver_waring ?></p>
                                                        <p class="c4">告警</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co3">
                                                    <div>
                                                        <p class="c3"><?php echo $sqlserver_critical ?></p>
                                                        <p class="c4">异常</p>
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
            
			
<!-- 滚动部分静态代码 开始-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left"><i class="iconfont icon-ai222"></i>告警显示</div>
                                    <div class="pull-right"><a href="<?php echo site_url('alarm/index'); ?>">查看详细<i class="iconfont icon-gengduo"></i></a>
                                    </div>
                                </div>
                                <div class="block-content" style="    height: 371px;">
                                   
                                    
                                    <table class="table" style="margin:0;">
                                        <thead>
                                            <tr>
                                                <th style="width:60px;">标签</th>
                                                <th style="width:60px;">类型</th>
                                                <th>告警内容</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div class="list_lh2">
                                        <ul>
				                                	<?php if(!empty($alarm)) {?>
				                                 	<?php foreach ($alarm  as $item):?>
                                            <li>
                                                <table class="table">
							                                    <tr>
							                                        <td style="width:60px;"><?php echo $item['tags'] ?></td>
							                                        <td style="width:60px;"><?php echo $item['db_type'] ?></td>
							                                        <td><?php echo $item['message'] ?></td>
							                                    </tr>
                                                </table>
                                            </li>
				                                	<?php endforeach;?>
				                                	<?php }else{  ?>
                                            <li>
                                                <table class="table">
				                                					<tr>
							                                			<td colspan="16">
							                                			<font color="red"><?php echo $this->lang->line('no_record'); ?></font>
							                                			</td>
						                                			</tr>
                                                </table>
                                            </li>
				                                	<?php } ?>    
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<!-- 滚动部分静态代码 结束-->
			
			
			<button id="view-fullscreen">全屏</button>
                    <button id="cancel-fullscreen">退出</button>
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


//全屏JS
(function () {
    var viewFullScreen = document.getElementById("view-fullscreen");
    if (viewFullScreen) {
        viewFullScreen.addEventListener("click", function () {
            var docElm = document.documentElement;
            if (docElm.requestFullscreen) {
                docElm.requestFullscreen();
            }
            else if (docElm.msRequestFullscreen) {
                docElm = document.body; //overwrite the element (for IE)
                docElm.msRequestFullscreen();
            }
            else if (docElm.mozRequestFullScreen) {
                docElm.mozRequestFullScreen();
            }
            else if (docElm.webkitRequestFullScreen) {
                docElm.webkitRequestFullScreen();
            }
        }, false);
    }

    var cancelFullScreen = document.getElementById("cancel-fullscreen");
    if (cancelFullScreen) {
        cancelFullScreen.addEventListener("click", function () {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
            else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            }
            else if (document.webkitCancelFullScreen) {
                document.webkitCancelFullScreen();
            }
        }, false);
    }


    var fullscreenState = document.getElementById("fullscreen-state");
    if (fullscreenState) {
        document.addEventListener("fullscreenchange", function () {
            fullscreenState.innerHTML = (document.fullscreenElement)? "" : "not ";
        }, false);
        
        document.addEventListener("msfullscreenchange", function () {
            fullscreenState.innerHTML = (document.msFullscreenElement)? "" : "not ";
        }, false);
        
        document.addEventListener("mozfullscreenchange", function () {
            fullscreenState.innerHTML = (document.mozFullScreen)? "" : "not ";
        }, false);
        
        document.addEventListener("webkitfullscreenchange", function () {
            fullscreenState.innerHTML = (document.webkitIsFullScreen)? "" : "not ";
        }, false);
    }

    var marioVideo = document.getElementById("mario-video")
        videoFullscreen = document.getElementById("video-fullscreen");

    if (marioVideo && videoFullscreen) {
        videoFullscreen.addEventListener("click", function (evt) {
            if (marioVideo.requestFullscreen) {
                marioVideo.requestFullscreen();
            }
            else if (marioVideo.msRequestFullscreen) {
                marioVideo.msRequestFullscreen();
            }
            else if (marioVideo.mozRequestFullScreen) {
                marioVideo.mozRequestFullScreen();
            }
            else if (marioVideo.webkitRequestFullScreen) {
                marioVideo.webkitRequestFullScreen();
                /*
                    *Kept here for reference: keyboard support in full screen
                    * marioVideo.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
                */
            }
        }, false);
    }
})();
//全屏后刷新表格大小
window.onresize = function(){
                 myChart.resize();
        }
        
        
        
//显示tooltip  
$(function () { $("[data-toggle='tooltip']").tooltip(); });     
</script>

<!-- pho页面代码-结束 -->




