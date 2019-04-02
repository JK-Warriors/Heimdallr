<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit" />
    <meta
      name="viewport"
      content="width=device-width,initial-scale=1.0,user-scalable=no"
    />

    <base href="<?php echo base_url().'application/views/static/'; ?>" />
		<script src="lib/bootstrap/js/rem.js"></script>
		<link href="lib/bootstrap/css/large_screen.css" rel="stylesheet"/>
  
  	<script type="text/javascript" src="lib/bootstrap/js/jquery-3.3.1.min.js"></script>
  	<script type="text/javascript" src="lib/bootstrap/js/echarts.min.js"></script>
  	<script type="text/javascript" src="lib/bootstrap/js/chalk.js"></script>
  
    <title></title>
  </head>

  <body style="visibility: visible;">
    <div class="container-flex">
      <div class="boxtit">
        <div class="b1" id="Timer">
        </div>
        <div class="b2"><?php echo $dg_info[0]['group_name'];?>容灾监控中心</div>
        <div class="b3">最新检测时间:<span><?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
        	
        <a href="<?php echo site_url('wl_oracle/dglist'); ?>" class="qbtn"><img src="lib/bootstrap/img/quit.png" title="退出"></a>
        	</div>
        <div class="b4">
          <h3>灾备状态</h3>
          <div class="m">
            <div class="m2">
              <div class="container <?php 
        				$days_dif=floor((strtotime($primary_db[0]['p_db_time'])-strtotime($standby_db[0]['s_db_time']))/86400);
        				$hours_dif=floor((strtotime($primary_db[0]['p_db_time'])-strtotime($standby_db[0]['s_db_time']) - ($days_dif*24*3600))/3600);
        				#echo $days_dif . "天" . $hours_dif . "小时" . $min_dif . "分" . $sec_dif . "秒";
              	if($days_dif > 1 ){echo "item-red";}   #时间差超过1天，红色告警
        				elseif($hours_dif > 1){echo "item-yellow";} #时间差超过1小时，黄色告警
        				else{echo "";}
              	?>">
                <div class="item-1"></div>
                <div class="item-2"></div>
                <div class="item-3"></div>
                <div class="item-4"></div>
                <div class="item-5"></div>
              </div>
              <p><span class="spancolor">日志应用:</span> SEQ: <?php echo $standby_db[0]['s_sequence'] ?> BLOCK# <?php echo $standby_db[0]['s_block'] ?></p>
              <p><span class="spancolor">传输模式:</span> <?php if($primary_db[0]['transmit_mode']='ASYNCHRONOUS'){echo "异步模式";} else{echo "同步模式";} ?></p>
              
            </div>
            <div class="m1 ml">
              <p>生产系统</p>
              <img src="<?php if($primary_db[0]['open_mode']==-1){echo "lib/bootstrap/img/database_error.png";} else{echo "lib/bootstrap/img/database.png";} ?>" alt="" />
              <div class="mtext">
                <li><span>SCN时间:</span> <?php echo $primary_db[0]['p_db_time'] ?></li>
                <li><span>实例名:</span> <?php echo $primary_db[0]['db_name'] ?></li>
                <li><span>IP地址:</span> <?php  echo $primary_db[0]['p_host'] ?></li>
                <li><span>数据库版本:</span> <?php  echo $primary_db[0]['db_version'] ?></li>
              </div>
            </div>
            <div class="m1 mr">
              <p>灾备系统</p>
              <img src="<?php if($standby_db[0]['open_mode']==-1){echo "lib/bootstrap/img/database_error.png";} else{echo "lib/bootstrap/img/database.png";} ?>" alt="" />
              <div class="mtext">
                <li><span>SCN时间:</span> <?php echo $standby_db[0]['s_db_time'] ?></li>
                <li><span>实例名:</span> <?php echo $standby_db[0]['db_name'] ?></li>
                <li><span>IP地址:</span> <?php  echo $standby_db[0]['s_host'] ?></li>
                <li><span>数据库版本:</span> <?php  echo $standby_db[0]['db_version'] ?></li>
              </div>
            </div>
            <div class="m3 ml">
              <div class="mtext">
                <li><span>当前SCN:</span><?php echo $primary_db[0]['p_scn'] ?></li>
            		<?php foreach ($primary_db as $item):?>
                    <li><span>Thread:</span><?php echo $item['p_thread'] ?>
                    <p class="sp2"><span>Sequence:</span> <?php echo $item['p_sequence'] ?></p>
                    </li>
            		<?php endforeach;?>
            		
                <li><span>状态:</span><?php echo $primary_db[0]['open_mode'] ?></li>
                <li style="<?php if($primary_db[0]['flashback_on']=='YES'){echo "display: none;";} ?>"><span>生产库闪回状态:</span>未启动</li>
                <li style="<?php if($primary_db[0]['flashback_on']=='NO'){echo "display: none;";} ?>"><span>最早闪回时间:</span>
                <li><span>闪回空间使用率:</span><?php echo $primary_db[0]['flashback_space_used'] ?>%</li>
              </div>
            </div>
            <div class="m3 mr">
              <div class="mtext">
                <li><span>当前SCN:</span><?php echo $standby_db[0]['s_scn'] ?></li>
                
                <li><span>恢复速度:</span><?php echo $standby_db[0]['avg_apply_rate'] ?> KB/sec</p>
                </li>
      					<li><span>当前恢复:</span>thread#: <?php echo $standby_db[0]['s_thread'] ?>
      						<p class="sp2">sequence: <?php echo $standby_db[0]['s_sequence']?></p>
      					</li>
      
                <li><span>状态:</span><?php echo $standby_db[0]['open_mode'] ?></li>
                <li style="<?php if($standby_db[0]['flashback_on']=='YES'){echo "display: none;";} ?>"><span>容灾库闪回状态:</span>未启动</li>
                <li style="<?php if($standby_db[0]['flashback_on']=='NO'){echo "display: none;";} ?>"><span>最早闪回时间:</span><?php echo $standby_db[0]['flashback_e_time'] ?></li>
                <li><span>闪回空间使用率:</span><?php echo $standby_db[0]['flashback_space_used'] ?>%</li>
              </div>
            </div>
          </div>
        </div>
        <div class="b5">
          <h3>备库信息</h3>
          <div class="m">
            <div class="e1">
              <h4>日志量 (单位:M)</h4>
              <div id="main" style="width: 100%;height:100%;"></div>
            </div>
            <div class="e2">
              <h4>指标雷达</h4>
              <div id="main2" style="width: 100%;height:100%;"></div>
            </div>
            <div class="e3">
              <table border="0">
                <tr>
                  <td>CPU空闲率</td>
                  <td><?php echo $standby_os['cpu_idle_time'] ?>%</td>
                  <td>内存空闲率</td>
                  <td><?php echo 100-$standby_os['mem_usage_rate'] ?>%</td>
                </tr>
                <tr>
                  <td>Swap空闲率</td>
                  <td><?php echo floor(($standby_os['swap_avail']/$standby_os['swap_total'])*100) ?>%</td>
                  <td>磁盘空闲率</td>
                  <td><?php echo 100-$standby_os_disk['max_used'] ?>%</td>
                </tr>
                <tr>
                  <td>Inode空闲率</td>
                  <td>80%</td>
                  <td>进程数</td>
                  <td><?php echo $standby_os['process'] ?></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="b6">
          <h3>备份差异</h3>
          <div class="m">
            <li>
              <span>数据文件写入延时:</span><b class="orange"><?php
        				#$second_dif=floor((strtotime($primary_db[0]['p_db_time'])-strtotime($standby_db[0]['s_db_time']))%86400%60);
        				#$date1 = "2019-04-01 19:47:11";
        				#$date2 = "2019-03-06 15:43:05";
        				$date1 = $primary_db[0]['p_db_time'];
        				$date2 = $standby_db[0]['s_db_time'];
        				
        				$days_dif=floor((strtotime($date1)-strtotime($date2))/86400);
        				$hours_dif=floor((strtotime($date1)-strtotime($date2) - ($days_dif*24*3600))/3600);
        				$min_dif=floor((strtotime($date1)-strtotime($date2) - ($days_dif*24*3600) -($hours_dif * 3600))/60);
        				$sec_dif=floor(strtotime($date1)-strtotime($date2) - ($days_dif*24*3600) -($hours_dif * 3600) - ($min_dif * 60));
        				#echo strtotime($testdate1)-strtotime($testdate2);
        				echo $days_dif . "天" . $hours_dif . "小时" . $min_dif . "分" . $sec_dif . "秒";
        				?>
              	</b>
            </li>
            <li><span>日志传输延时thread:</span><b class="orange"><?php echo $primary_db[0]['p_thread'] ?><span style="display:inline-block;width:3.5em;"></span><?php echo $primary_db[1]['p_thread'] ?></b></li>
            <li>
              <span>日志传输延时sequence差异:</span><b class="orange"><?php echo $primary_db[0]['archived_delay'] ?><span style="display:inline-block;width: 3.5em;"></span><?php echo $primary_db[1]['archived_delay'] ?></b>
            </li>
            <li><span>日志应用延时thread:</span><b class="orange"><?php echo $primary_db[0]['p_thread'] ?><span style="display:inline-block;width: 3.5em;"></span><?php echo $primary_db[1]['p_thread'] ?></b></li>
            <li><span>日志应用延时sequence差异:</span><b class="orange"><?php echo $primary_db[0]['applied_delay'] ?><span style="display:inline-block;width: 3.5em;"></span><?php echo $primary_db[1]['applied_delay'] ?></b></li>
          </div>
        </div>
      </div>
    </div>
  </body>
  
  <script type="text/javascript">
    var myChart = echarts.init(document.getElementById("main"), "chalk");
    var option = {
	    tooltip: {
  				trigger: 'axis'
	    },
	    grid:{
                    x:25,
                    y:45,
                    x2:2,
                    y2:17,
                    borderWidth:1
                },
      xAxis: {
        type: "category",
        boundaryGap: false,
        axisTick: {
            alignWithLabel: true
        },
        axisLine: {
            onZero: false,
            lineStyle: {
            }
        },
        axisPointer: {
            label: {
                formatter: function(params) {
                    return params.value + ' 日志量';
                }
            }
        },
        data: [
        		<?php if(!empty($standby_redo)) {?>
						<?php foreach ($standby_redo  as $item):?>
										"<?php echo $item['redo_time'] ?>",
						<?php endforeach;?>
						<?php } ?>
        ]
      },
      yAxis: {
        type: "value"
      },
      series: [
        {
          data: [
        		<?php if(!empty($standby_redo)) {?>
						<?php foreach ($standby_redo  as $item):?>
										"<?php echo $item['redo_log'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
      ]
    };
    myChart.setOption(option);

    var myChart2 = echarts.init(document.getElementById("main2"), "chalk");
    var option = {
      tooltip: {},

      radar: {
        name: {
          textStyle: {
            color: "#fff",
            fontSize: 10
          }
        },

        center: ["45%", "50%"],
        radius: 40,
        nameGap : 0,
        indicator: [
          { name: "CPU", max: 100 },
          { name: "Swap", max: 100 },
          { name: "内存", max: 100 },
          { name: "磁盘", max: 100 },
          { name: "Inode", max: 100 },
          { name: "进程", max: 100 }
        ]
      },
      series: [
        {
          name: "容灾库主机性能指标",
          type: "radar",
          itemStyle: { normal: { areaStyle: { type: "default" } } },
          data: [
            {
              value: [<?php echo $standby_os['cpu_idle_time'] ?>, 
              <?php echo floor(($standby_os['swap_avail']/$standby_os['swap_total'])*100) ?>, 
              <?php echo 100-$standby_os['mem_usage_rate'] ?>, 
              <?php echo 100-$standby_os_disk['max_used'] ?>, 
              80, 
              <?php echo floor(($standby_os['process']/$standby_os_cfg['threshold_critical_os_process'])*100) ?>]
            }
          ]
        }
      ]
    };
    myChart2.setOption(option);
     myChart2.setOption(option);
  window.addEventListener("resize", function () {
    myChart.resize();
    myChart2.resize();
  });
  </script>
  <script type="text/javascript">
    $(function () {
        setInterval("GetTime()", 1000);
    });
    //获取时间并设置格式
    function GetTime() {
        var mon, day, now, hour, min, ampm, time, str, tz, end, beg, sec;
        /*
        mon = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
                "Sep", "Oct", "Nov", "Dec");
        */
        mon = new Array("一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月",
                "九月", "十月", "十一月", "十二月");
        /*
        day = new Array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
        */
        day = new Array("周日", "周一", "周二", "周三", "周四", "周五", "周六");
        now = new Date();
        hour = now.getHours();
        min = now.getMinutes();
        sec = now.getSeconds();
        if (hour < 10) {
            hour = "0" + hour;
        }
        if (min < 10) {
            min = "0" + min;
        }
        if (sec < 10) {
            sec = "0" + sec;
        }
        $("#Timer").html(
            now.getFullYear() + "年" + (now.getMonth() + 1) + "月" + now.getDate() + "日" + "  " + hour + ":" + min + ":" + sec
            );
        //$("#Timer").html(
        //        day[now.getDay()] + ", " + mon[now.getMonth()] + " "
        //                + now.getDate() + ", " + now.getFullYear() + " " + hour
        //                + ":" + min + ":" + sec);
    }
</script>



<script type="text/javascript">
function refresh()
{
       window.location.reload();
}
setTimeout('refresh()',60000); //指定60秒刷新一次
</script>

</html>


