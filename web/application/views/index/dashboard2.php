<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="renderer" content="webkit" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no" />
  
  <base href="<?php echo base_url().'application/views/static/'; ?>" />
	<script src="lib/bootstrap/js/rem.js"></script>
	<link href="lib/bootstrap/css/dashboard.css" rel="stylesheet"/>
		
  <script type="text/javascript" src="lib/bootstrap/js/jquery-3.3.1.min.js"></script>
  <script type="text/javascript" src="lib/bootstrap/js/echarts.min.js"></script>
  <script type="text/javascript" src="lib/bootstrap/js/chalk.js"></script>
  <!---<script type="text/javascript" src="lib/bootstrap/js/charts_demo.js"></script>--->
		
  <title></title>
</head>

<body style="visibility: visible;">
  <div class="container-flex">
    <div class="box">
      <div class="pagetit">
        <h1><a href="<?php echo site_url('index/index'); ?>">DRM监控平台</a></h1>
      </div>
      <div class="datanum">
        <!-- <div class="dtit">数据库的连接状态</div> -->
        <img src="lib/bootstrap/img/bj-1.png" alt="" class="bj-1">
        <img src="lib/bootstrap/img/bj-2.png" alt="" class="bj-2">
        <img src="lib/bootstrap/img/bj-3.png" alt="" class="bj-3">
        <img src="lib/bootstrap/img/bj-4.png" alt="" class="bj-4">
        <ul class="cf">
        	<!-- Oracle实例状态 -->
        	<?php if(!empty($oracle_active_instance)) {?>
        	<?php foreach ($oracle_active_instance as $item): ?>
          <li>
            <span>
              <p><?php echo $item['tags'] ?></p>
              <img src="lib/bootstrap/img/db1.png" alt="">
            </span>
          </li>
          <?php endforeach;?>
          <?php } ?>  
          
          
        	<?php if(!empty($oracle_inactive_instance)) {?>
        	<?php foreach ($oracle_inactive_instance as $item): ?>
          <li>
            <span>
              <p><?php echo $item['tags'] ?></p>
              <img src="lib/bootstrap/img/db2.png" alt="">
            </span>
          </li>
          <?php endforeach;?>
          <?php } ?>  
          
        	<!-- MySQL实例状态 -->
        	<?php if(!empty($mysql_active_instance)) {?>
        	<?php foreach ($mysql_active_instance as $item): ?>
          <li>
            <span>
              <p><?php echo $item['tags'] ?></p>
              <img src="lib/bootstrap/img/db1.png" alt="">
            </span>
          </li>
          <?php endforeach;?>
          <?php } ?>  
          
          
        	<?php if(!empty($mysql_inactive_instance)) {?>
        	<?php foreach ($mysql_inactive_instance as $item): ?>
          <li>
            <span>
              <p><?php echo $item['tags'] ?></p>
              <img src="lib/bootstrap/img/db2.png" alt="">
            </span>
          </li>
          <?php endforeach;?>
          <?php } ?>  
        	
          
        	<!-- SQLServer实例状态 -->
        	<?php if(!empty($sqlserver_active_instance)) {?>
        	<?php foreach ($sqlserver_active_instance as $item): ?>
          <li>
            <span>
              <p><?php echo $item['tags'] ?></p>
              <img src="lib/bootstrap/img/db1.png" alt="">
            </span>
          </li>
          <?php endforeach;?>
          <?php } ?>  
          
          
        	<?php if(!empty($sqlserver_inactive_instance)) {?>
        	<?php foreach ($sqlserver_inactive_instance as $item): ?>
          <li>
            <span>
              <p><?php echo $item['tags'] ?></p>
              <img src="lib/bootstrap/img/db2.png" alt="">
            </span>
          </li>
          <?php endforeach;?>
          <?php } ?>  
        	
          
        </ul>
      </div>
      <div class="left1">

        <img src="lib/bootstrap/img/bj-1.png" alt="" class="bj-1">
        <img src="lib/bootstrap/img/bj-2.png" alt="" class="bj-2">
        <img src="lib/bootstrap/img/bj-3.png" alt="" class="bj-3">
        <img src="lib/bootstrap/img/bj-4.png" alt="" class="bj-4">
        <div class="datarow cf">
          <div class="d1">
            <h1><?php echo $db_tag_1[tags] ?></h1>
          </div>
          <div class="d2">
            <h2>负载（DB Time/ Elapsed time）</h2>
            <div id="left2" style="width: 100%;height:100%;"></div>
          </div>
          <div class="d3">
            <h2>total session和active session</h2>
            <div id="left3" style="width: 100%;height:100%;"></div>
          </div>
          <div class="d4">
            <h2>表空间使用率</h2>
            <ul>
            	<?php if(!empty($space_1)) {?>
		        	<?php foreach ($space_1 as $item): ?>
		          <li>
                <div class="progress">
                  <div class="progress-value"><?php echo $item['tablespace_name'] ?>:<span class="pdata"><?php echo $item['max_rate'] ?>%</span></div>
                  <div class="progress-bar">
                    <div class="progress-data"></div>
                  </div>
                </div>
              </li>
		          <?php endforeach;?>
		          <?php } ?>  
          
            </ul>
          </div>
          <div class="d5">
            <h2>每小时归档量</h2>
            <div id="left5" style="width: 100%;height:100%;"></div>
          </div>
        </div>
        
        <!--- 第二个 ---> 
        <div class="datarow cf">
          <div class="d1">
            <h1><?php echo $db_tag_2[tags] ?></h1>
          </div>
          <div class="d2">
            <div id="left22" style="width: 100%;height:100%;"></div>
          </div>
          <div class="d3">
            <div id="left23" style="width: 100%;height:100%;"></div>
          </div>
          <div class="d4">
            <ul>
            	<?php if(!empty($space_2)) {?>
		        	<?php foreach ($space_2 as $item): ?>
		          <li>
                <div class="progress">
                  <div class="progress-value"><?php echo $item['tablespace_name'] ?>:<span class="pdata"><?php echo $item['max_rate'] ?>%</span></div>
                  <div class="progress-bar">
                    <div class="progress-data"></div>
                  </div>
                </div>
              </li>
		          <?php endforeach;?>
		          <?php } ?>  
            </ul>
          </div>
          <div class="d5">
            <div id="left25" style="width: 100%;height:100%;"></div>
          </div>
        </div>
        
        <!--- 第三个 ---> 
        <div class="datarow cf">
          <div class="d1">
            <h1><?php echo $db_tag_3[tags] ?></h1>
          </div>
          <div class="d2">
            <div id="left32" style="width: 100%;height:100%;"></div>
          </div>
          <div class="d3">
            <div id="left33" style="width: 100%;height:100%;"></div>
          </div>
          <div class="d4">
            <ul>
            	<?php if(!empty($space_3)) {?>
		        	<?php foreach ($space_3 as $item): ?>
		          <li>
                <div class="progress">
                  <div class="progress-value"><?php echo $item['tablespace_name'] ?>:<span class="pdata"><?php echo $item['max_rate'] ?>%</span></div>
                  <div class="progress-bar">
                    <div class="progress-data"></div>
                  </div>
                </div>
              </li>
		          <?php endforeach;?>
		          <?php } ?>  
            </ul>
          </div>
          <div class="d5">
            <div id="left35" style="width: 100%;height:100%;"></div>
          </div>
        </div>
        
      </div>
      <div class="right1">
        <div class="dtit">dbtime指标</div>
        <img src="lib/bootstrap/img/bj-1.png" alt="" class="bj-1">
        <img src="lib/bootstrap/img/bj-2.png" alt="" class="bj-2">
        <img src="lib/bootstrap/img/bj-3.png" alt="" class="bj-3">
        <img src="lib/bootstrap/img/bj-4.png" alt="" class="bj-4">
        <div class="right11" id="right11"></div>
        <div class="right12" id="right12"></div>
      </div>
      <div class="right2">
        <div class="dtit">容灾状态</div>
        <img src="lib/bootstrap/img/bj-1.png" alt="" class="bj-1">
        <img src="lib/bootstrap/img/bj-2.png" alt="" class="bj-2">
        <img src="lib/bootstrap/img/bj-3.png" alt="" class="bj-3">
        <img src="lib/bootstrap/img/bj-4.png" alt="" class="bj-4">
        <ul>
          <li>
            <div class="progress">
              <div class="progress-name">Oracle</div>
              <div class="progress-bar">
                <hr>
                <hr>
                <hr>
                <hr>
                <!-- color1,color2,color3分别对应正常,告警,异常的颜色 -->
                <div class="progress-data <?php echo check_repl_color($oracle_normal, $oracle_waring, $oracle_critical) ?>" style="height:<?php echo check_repl_rate($oracle_normal, $oracle_waring, $oracle_critical) ?>%"></div>
              </div>
            </div>
            <div class="proright">
              <ul class="cf">
                <li class="co1">
                  <div>
                    <p><?php echo $oracle_normal ?></p>
                    <p>正常</p>
                  </div>
                </li>
                <li class="co2">
                  <div>
                    <p><?php echo $oracle_waring ?></p>
                    <p>告警</p>
                  </div>
                </li>

                <li class="co3">
                  <div>
                    <p><?php echo $oracle_critical ?></p>
                    <p>异常</p>
                  </div>
                </li>
              </ul>

            </div>
          </li>
          <li>
              <div class="progress">
                <div class="progress-name">MySQL</div>
                <div class="progress-bar">
                  <hr>
                  <hr>
                  <hr>
                  <hr>
                  <!-- color1,color2,color3分别对应正常,告警,异常的颜色 -->
                  <div class="progress-data <?php echo check_repl_color($mysql_normal, $mysql_waring, $mysql_critical) ?>" style="height:<?php echo check_repl_rate($mysql_normal, $mysql_waring, $mysql_critical) ?>%"></div>
                </div>
              </div>
              <div class="proright">
                <ul class="cf">
                  <li class="co1">
                    <div>
                      <p><?php echo $mysql_normal ?></p>
                      <p>正常</p>
                    </div>
                  </li>
                  <li class="co2">
                    <div>
                      <p><?php echo $mysql_waring ?></p>
                      <p>告警</p>
                    </div>
                  </li>
  
                  <li class="co3">
                    <div>
                      <p><?php echo $mysql_critical ?></p>
                      <p>异常</p>
                    </div>
                  </li>
                </ul>
  
              </div>
            </li>
            <li>
                <div class="progress">
                  <div class="progress-name">SQLServer</div>
                  <div class="progress-bar">
                    <hr>
                    <hr>
                    <hr>
                    <hr>
                    <!-- color1,color2,color3分别对应正常,告警,异常的颜色 -->
                    <div class="progress-data <?php echo check_repl_color($sqlserver_normal, $sqlserver_waring, $sqlserver_critical) ?>" style="height:<?php echo check_repl_rate($sqlserver_normal, $sqlserver_waring, $sqlserver_critical) ?>%"></div>
                  </div>
                </div>
                <div class="proright">
                  <ul class="cf">
                    <li class="co1">
                      <div>
                        <p><?php echo $sqlserver_normal ?></p>
                        <p>正常</p>
                      </div>
                    </li>
                    <li class="co2">
                      <div>
                        <p><?php echo $sqlserver_waring ?></p>
                        <p>告警</p>
                      </div>
                    </li>
    
                    <li class="co3">
                      <div>
                        <p><?php echo $sqlserver_critical ?></p>
                        <p>异常</p>
                      </div>
                    </li>
                  </ul>
    
                </div>
              </li>
        </ul>
      </div>
      <div class="foot1">
        <div class="dtit">告警信息</div>

        <img src="lib/bootstrap/img/bj-1.png" alt="" class="bj-1">
        <img src="lib/bootstrap/img/bj-2.png" alt="" class="bj-2">
        <img src="lib/bootstrap/img/bj-3.png" alt="" class="bj-3">
        <img src="lib/bootstrap/img/bj-4.png" alt="" class="bj-4">
        <div class="finfo">
          <ul>
            <li>
              <span class="circlespan c1"></span>
                一级：红色
            </li>
            <li>
              <span class="circlespan c2"></span>
                二级：黄色
            </li>
            <li>
              <span class="circlespan c3"></span>
                三级（正常）：绿色
            </li>
          </ul>
        </div>
        <div class="fg-box" id="box">
          <ul>
          	<?php if(!empty($alarm)) {?>
           	<?php foreach ($alarm  as $item):?>
              <li>
                  <table class="table">
                    <tr>
                        <td style="width:100px;"><?php echo $item['tags'] ?></td>
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
</body>


<script type="text/javascript">
var left2 = echarts.init(document.getElementById("left2"), "chalk");
var option = {

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        orient: 'vertical',
        data: ['']
    },
    grid: {
        left: '3%',
        right: '3%',
        top: '30px',
        bottom: '0',
        containLabel: true
    },
    color: ['#a4d8cc', '#25f3e6'],
    toolbox: {
        show: false,
        feature: {
            mark: {
                show: true
            },
            dataView: {
                show: true,
                readOnly: false
            },
            magicType: {
                show: true,
                type: ['line', 'bar', 'stack', 'tiled']
            },
            restore: {
                show: true
            },
            saveAsImage: {
                show: true
            }
        }
    },

    calculable: true,
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
                    return params.value + ' 负载';
                }
            }
        },
        data: [
        		<?php if(!empty($db_time_1)) {?>
						<?php foreach ($db_time_1  as $item):?>
										"<?php echo substr($item['end_time'],11,5) ?>",
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
        		<?php if(!empty($db_time_1)) {?>
						<?php foreach ($db_time_1  as $item):?>
										"<?php echo $item['rate'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
    ]
};
left2.setOption(option);




var left3 = echarts.init(document.getElementById("left3"), "chalk");
var option = {

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        orient: 'vertical',
        data: ['']
    },
    grid: {
        left: '3%',
        right: '3%',
        top: '30px',
        bottom: '0',
        containLabel: true
    },
    color:  ['#5793f3', '#675bba'],
    toolbox: {
        show: false,
        feature: {
            mark: {
                show: true
            },
            dataView: {
                show: true,
                readOnly: false
            },
            magicType: {
                show: true,
                type: ['line', 'bar', 'stack', 'tiled']
            },
            restore: {
                show: true
            },
            saveAsImage: {
                show: true
            }
        }
    },

    calculable: true,
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
                    return params.value + ' 负载';
                }
            }
        },
        data: [
        		<?php if(!empty($db_session_1)) {?>
						<?php foreach ($db_session_1  as $item):?>
										"<?php echo substr($item['end_time'],11,5) ?>",
						<?php endforeach;?>
						<?php } ?>
        ]
    },
    yAxis: {
        type: "value"
    },
    series: [
        { name: "total",
          data: [
        		<?php if(!empty($db_session_1)) {?>
						<?php foreach ($db_session_1  as $item):?>
										"<?php echo $item['total_session'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        },
        { name: "active",
          data: [
        		<?php if(!empty($db_session_1)) {?>
						<?php foreach ($db_session_1  as $item):?>
										"<?php echo $item['active_session'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
    ]
};
left3.setOption(option);


var left5 = echarts.init(document.getElementById("left5"), "chalk");
var option = {

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        orient: 'vertical',
        data: ['']
    },
    grid: {
        left: '3%',
        right: '3%',
        top: '30px',
        bottom: '0',
        containLabel: true
    },
    color: ['#a4d8cc', '#25f3e6'],
    toolbox: {
        show: false,
        feature: {
            mark: {
                show: true
            },
            dataView: {
                show: true,
                readOnly: false
            },
            magicType: {
                show: true,
                type: ['line', 'bar', 'stack', 'tiled']
            },
            restore: {
                show: true
            },
            saveAsImage: {
                show: true
            }
        }
    },

    calculable: true,
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
        		<?php if(!empty($redo_1)) {?>
						<?php foreach ($redo_1  as $item):?>
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
        		<?php if(!empty($redo_1)) {?>
						<?php foreach ($redo_1  as $item):?>
										"<?php echo $item['redo_log'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
    ]
};
left5.setOption(option);


//中间区域第二行
var left22 = echarts.init(document.getElementById("left22"), "chalk");
var option = {

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        orient: 'vertical',
        data: ['']
    },
    grid: {
        left: '3%',
        right: '3%',
        top: '30px',
        bottom: '0',
        containLabel: true
    },
    color: ['#a4d8cc', '#25f3e6'],
    toolbox: {
        show: false,
        feature: {
            mark: {
                show: true
            },
            dataView: {
                show: true,
                readOnly: false
            },
            magicType: {
                show: true,
                type: ['line', 'bar', 'stack', 'tiled']
            },
            restore: {
                show: true
            },
            saveAsImage: {
                show: true
            }
        }
    },

    calculable: true,
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
                    return params.value + ' 负载';
                }
            }
        },
        data: [
        		<?php if(!empty($db_time_2)) {?>
						<?php foreach ($db_time_2  as $item):?>
										"<?php echo substr($item['end_time'],11,5) ?>",
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
        		<?php if(!empty($db_time_2)) {?>
						<?php foreach ($db_time_2  as $item):?>
										"<?php echo $item['rate'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
    ]
};
left22.setOption(option);




var left23 = echarts.init(document.getElementById("left23"), "chalk");
var option = {

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        orient: 'vertical',
        data: ['']
    },
    grid: {
        left: '3%',
        right: '3%',
        top: '30px',
        bottom: '0',
        containLabel: true
    },
    color:  ['#5793f3', '#675bba'],
    toolbox: {
        show: false,
        feature: {
            mark: {
                show: true
            },
            dataView: {
                show: true,
                readOnly: false
            },
            magicType: {
                show: true,
                type: ['line', 'bar', 'stack', 'tiled']
            },
            restore: {
                show: true
            },
            saveAsImage: {
                show: true
            }
        }
    },

    calculable: true,
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
                    return params.value + ' 负载';
                }
            }
        },
        data: [
        		<?php if(!empty($db_session_2)) {?>
						<?php foreach ($db_session_2  as $item):?>
										"<?php echo substr($item['end_time'],11,5) ?>",
						<?php endforeach;?>
						<?php } ?>
        ]
    },
    yAxis: {
        type: "value"
    },
    series: [
        { name: "total",
          data: [
        		<?php if(!empty($db_session_2)) {?>
						<?php foreach ($db_session_2  as $item):?>
										"<?php echo $item['total_session'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        },
        { name: "active",
          data: [
        		<?php if(!empty($db_session_2)) {?>
						<?php foreach ($db_session_2  as $item):?>
										"<?php echo $item['active_session'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
    ]
};
left23.setOption(option);


var left25 = echarts.init(document.getElementById("left25"), "chalk");
var option = {

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        orient: 'vertical',
        data: ['']
    },
    grid: {
        left: '3%',
        right: '3%',
        top: '30px',
        bottom: '0',
        containLabel: true
    },
    color: ['#a4d8cc', '#25f3e6'],
    toolbox: {
        show: false,
        feature: {
            mark: {
                show: true
            },
            dataView: {
                show: true,
                readOnly: false
            },
            magicType: {
                show: true,
                type: ['line', 'bar', 'stack', 'tiled']
            },
            restore: {
                show: true
            },
            saveAsImage: {
                show: true
            }
        }
    },

    calculable: true,
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
        		<?php if(!empty($redo_2)) {?>
						<?php foreach ($redo_2  as $item):?>
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
        		<?php if(!empty($redo_2)) {?>
						<?php foreach ($redo_2  as $item):?>
										"<?php echo $item['redo_log'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
    ]
};
left25.setOption(option);


//中间区域第三行
var left32 = echarts.init(document.getElementById("left32"), "chalk");
var option = {

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        orient: 'vertical',
        data: ['']
    },
    grid: {
        left: '3%',
        right: '3%',
        top: '30px',
        bottom: '0',
        containLabel: true
    },
    color: ['#a4d8cc', '#25f3e6'],
    toolbox: {
        show: false,
        feature: {
            mark: {
                show: true
            },
            dataView: {
                show: true,
                readOnly: false
            },
            magicType: {
                show: true,
                type: ['line', 'bar', 'stack', 'tiled']
            },
            restore: {
                show: true
            },
            saveAsImage: {
                show: true
            }
        }
    },

    calculable: true,
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
                    return params.value + ' 负载';
                }
            }
        },
        data: [
        		<?php if(!empty($db_time_3)) {?>
						<?php foreach ($db_time_3  as $item):?>
										"<?php echo substr($item['end_time'],11,5) ?>",
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
        		<?php if(!empty($db_time_3)) {?>
						<?php foreach ($db_time_3  as $item):?>
										"<?php echo $item['rate'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
    ]
};
left32.setOption(option);




var left33 = echarts.init(document.getElementById("left33"), "chalk");
var option = {

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        orient: 'vertical',
        data: ['']
    },
    grid: {
        left: '3%',
        right: '3%',
        top: '30px',
        bottom: '0',
        containLabel: true
    },
    color:  ['#5793f3', '#675bba'],
    toolbox: {
        show: false,
        feature: {
            mark: {
                show: true
            },
            dataView: {
                show: true,
                readOnly: false
            },
            magicType: {
                show: true,
                type: ['line', 'bar', 'stack', 'tiled']
            },
            restore: {
                show: true
            },
            saveAsImage: {
                show: true
            }
        }
    },

    calculable: true,
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
                    return params.value + ' 负载';
                }
            }
        },
        data: [
        		<?php if(!empty($db_session_3)) {?>
						<?php foreach ($db_session_3  as $item):?>
										"<?php echo substr($item['end_time'],11,5) ?>",
						<?php endforeach;?>
						<?php } ?>
        ]
    },
    yAxis: {
        type: "value"
    },
    series: [
        { name: "total",
          data: [
        		<?php if(!empty($db_session_3)) {?>
						<?php foreach ($db_session_3  as $item):?>
										"<?php echo $item['total_session'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        },
        { name: "active",
          data: [
        		<?php if(!empty($db_session_3)) {?>
						<?php foreach ($db_session_3  as $item):?>
										"<?php echo $item['active_session'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
    ]
};
left33.setOption(option);


var left35 = echarts.init(document.getElementById("left35"), "chalk");
var option = {

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        orient: 'vertical',
        data: ['']
    },
    grid: {
        left: '3%',
        right: '3%',
        top: '30px',
        bottom: '0',
        containLabel: true
    },
    color: ['#a4d8cc', '#25f3e6'],
    toolbox: {
        show: false,
        feature: {
            mark: {
                show: true
            },
            dataView: {
                show: true,
                readOnly: false
            },
            magicType: {
                show: true,
                type: ['line', 'bar', 'stack', 'tiled']
            },
            restore: {
                show: true
            },
            saveAsImage: {
                show: true
            }
        }
    },

    calculable: true,
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
        		<?php if(!empty($redo_3)) {?>
						<?php foreach ($redo_3  as $item):?>
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
        		<?php if(!empty($redo_3)) {?>
						<?php foreach ($redo_3  as $item):?>
										"<?php echo $item['redo_log'] ?>",
						<?php endforeach;?>
						<?php } ?>
          ],
          type: "line",
          areaStyle: {}
        }
    ]
};
left35.setOption(option);



var right11 = echarts.init(document.getElementById("right11"), "chalk");
var option = {
    backgroundColor: 'rgba(0,0,0,0)',
    tooltip: {
        trigger: 'item',
        formatter: "{b}: <br/>{c} ({d}%)"
    },
    
    color: ['#af89d6', '#4ac7f5', '#0089ff', '#f36f8a', '#f5c847'],
    legend: {
        orient: 'vertical',
        x: 'left',
        textStyle: {
            color: '#ccc'
        },
        data:['直接访问','邮件营销','联盟广告','视频广告','搜索引擎']
    },
    series: [{
        name: '行业占比',
        type: 'pie',
        clockwise: false, //饼图的扇区是否是顺时针排布
        minAngle: 20, //最小的扇区角度（0 ~ 360）
        center: ['55%', '60%'], //饼图的中心（圆心）坐标
        radius: [0, '80%'], //饼图的半径
        avoidLabelOverlap: true, ////是否启用防止标签重叠
        itemStyle: { //图形样式
            normal: {
                borderColor: '#1e2239',
                borderWidth: 2,
            },
        },
        label: { //标签的位置
            normal: {
                show: true,
                position: 'inside', //标签的位置
                formatter: "{d}%",
                textStyle: {
                    color: '#fff',
                }
            },
            emphasis: {
                show: true,
                textStyle: {
                    fontWeight: 'bold'
                }
            }
        },
        data: [{
                value: 335,
                name: '直接访问'
            },
            {
                value: 310,
                name: '邮件营销'
            },
            {
                value: 234,
                name: '联盟广告'
            },
            {
                value: 135,
                name: '视频广告'
            },
            {
                value: 1548,
                name: '搜索引擎'
            }
        ],
    }]
};

right11.setOption(option);


var right12 = echarts.init(document.getElementById("right12"), "chalk");
var option = {

    tooltip: {},
    legend: {
        data: ['namename']
    },
    radar: {
        // shape: 'circle',
        name: {
            textStyle: {
                color: '#ccc',
            }
        },
        indicator: [{
                name: 'a1',
                max: 100
            },
            {
                name: 'a2',
                max: 100
            },
            {
                name: 'a3',
                max: 100
            },
            {
                name: 'a3',
                max: 100
            }
        ]
    },
    series: [{
        name: 'aaaa',
        type: 'radar',
        areaStyle: {},
        data: [{
            value: [50, 12, 90, 66]
        }]
    }]
};

right12.setOption(option);


window.addEventListener("resize", function () {
     left2.resize();
     left3.resize();
     left5.resize();
     right11.resize();
     right12.resize();
});  	
  	
</script>
  
<script type="text/javascript">
  $(document).ready(function () {

    // d4进度条
    var getValue = $('.pdata');
    for (var i = 0; i < getValue.length; i++) {
      var get_w = $(getValue[i]).text();
      $(getValue[i]).parent().next().find('.progress-data').css('width', get_w);
    }

    var _box = $('#box');
    var _interval = 1000; //刷新间隔时间3秒
    function gdb() {
      $("<p><span class='circlespan c3'></span>2019年4月25日 XXXX</p>").appendTo('#box');
      $('#box').scrollTop($('#box')[0].scrollHeight);
      /* var _last=$('#box dl dd:last');
      _last.animate({height: '+53px'}, "slow"); */
      setTimeout(function () {
        gdb();
      }, _interval);
    };
    // gdb();
  });



  // 滚动文字
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
        $("div.fg-box").myScroll({
            speed: 160, //数值越大，速度越慢
            rowHeight: 37 //li的高度
        });
    });
</script>
    
    
<script type="text/javascript">
function refresh()
{
       window.location.reload();
}
setTimeout('refresh()',60000); //指定60秒刷新一次
</script>

</html>


