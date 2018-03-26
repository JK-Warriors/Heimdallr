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
    background: #2A2A2A
}

footer hr {
    border: 0px;
    border-top: 1px solid #3c3b3b;
}
</style>
<div class="indexpage">
    <ul class="breadcrumb">
        <li><a href="http://localhost/index.php">主页</a> <span class="divider">/</span></li>
        <li class="active">工作台</li>
    </ul>
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"><i class="iconfont icon-ai222"></i>oracle</div>
                            <div class="pull-right"><a href="#">查看详细<i class="iconfont icon-gengduo"></i></a>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="row">
                                <div class="col-md-6 tbox1">11
                                </div>
                                <div class="col-md-6 tbox2">
                                    <p class="box_check"><i class="iconfont icon-ziyuan"></i>11</p>
                                    <p class="box_times"><i class="iconfont icon-icon2"></i>222</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"><i class="iconfont icon-ai222"></i>MySQL</div>
                            <div class="pull-right"><a href="#">查看详细<i class="iconfont icon-gengduo"></i></a>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="row">
                                <div class="col-md-6 tbox1">99
                                </div>
                                <div class="col-md-6 tbox2">
                                    <p class="box_check"><i class="iconfont icon-ziyuan"></i>11</p>
                                    <p class="box_times"><i class="iconfont icon-icon2"></i>222</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"><i class="iconfont icon-ai222"></i>SQLServer</div>
                            <div class="pull-right"><a href="#">查看详细<i class="iconfont icon-gengduo"></i></a>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="row">
                                <div class="col-md-6 tbox1">1
                                </div>
                                <div class="col-md-6 tbox2">
                                    <p class="box_check"><i class="iconfont icon-ziyuan"></i>11</p>
                                    <p class="box_times"><i class="iconfont icon-icon2"></i>222</p>
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
                                <div class="pull-right"><a href="#">查看详细<i class="iconfont icon-gengduo"></i></a>
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
                                <div class="pull-right"><a href="#">查看详细<i class="iconfont icon-gengduo"></i></a>
                                </div>
                            </div>
                            <div class="block-content">
                                <table class="table">
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
                                    <tbody>
                                        <!-- √×!分别对应不同的<i>标签,参考demo -->
                                        <tr>
                                            <td><i class="iconfont icon-ziyuan"></i>demo</td>
                                            <td><i class="iconfont icon-icon2"></i>demo</td>
                                            <td><i class="iconfont icon-jinggao1"></i>demo</td>
                                            <td>demo</td>
                                            <td>demo</td>
                                            <td>demo</td>
                                        </tr>
                                        <tr>
                                            <td>demo</td>
                                            <td>demo</td>
                                            <td>demo</td>
                                            <td><i class="iconfont icon-ziyuan"></i></td>
                                            <td><i class="iconfont icon-icon2"></i></td>
                                            <td><i class="iconfont icon-jinggao1"></i></td>
                                        </tr>
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
                                        <div class="in4_1">Demo</div>
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
                                                        <p class="c3">99</p>
                                                        <p class="c4">High</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co2">
                                                    <div>
                                                        <p class="c3">99</p>
                                                        <p class="c4">Medium</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co3">
                                                    <div>
                                                        <p class="c3">99</p>
                                                        <p class="c4">Low</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                     <li>
                                        <div class="in4_1">Demo</div>
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
                                                        <p class="c3">99</p>
                                                        <p class="c4">High</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co2">
                                                    <div>
                                                        <p class="c3">99</p>
                                                        <p class="c4">Medium</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co3">
                                                    <div>
                                                        <p class="c3">99</p>
                                                        <p class="c4">Low</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                     <li>
                                        <div class="in4_1">Demo</div>
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
                                                        <p class="c3">99</p>
                                                        <p class="c4">High</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co2">
                                                    <div>
                                                        <p class="c3">99</p>
                                                        <p class="c4">Medium</p>
                                                    </div>
                                                </div>
                                                <div class="c2 co3">
                                                    <div>
                                                        <p class="c3">99</p>
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
                            <div class="pull-right"><a href="#">查看详细<i class="iconfont icon-gengduo"></i></a>
                            </div>
                        </div>
                        <div class="block-content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <div>
                                            <th>数据库名</th>
                                            <th style="width:120px;">类型</th>
                                            <th>告警内容</th>
                                        </div>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>demo</td>
                                        <td>demo</td>
                                        <td>demo</td>
                                    </tr>
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
option = null;
app.title = '多 X 轴示例';

var colors = ['#5793f3', '#d14a61', '#675bba'];


option = {
    color: colors,

    tooltip: {
        trigger: 'none',
        axisPointer: {
            type: 'cross'
        }
    },
    legend: {
        data: ['2015 降水量', '2016 降水量']
    },
    grid: {
        top: 70,
        bottom: 50
    },
    xAxis: [{
        type: 'category',
        axisTick: {
            alignWithLabel: true
        },
        axisLine: {
            onZero: false,
            lineStyle: {
                color: colors[1]
            }
        },
        axisPointer: {
            label: {
                formatter: function(params) {
                    return '降水量  ' + params.value + (params.seriesData.length ? '：' + params.seriesData[0].data : '');
                }
            }
        },
        data: ["2016-1", "2016-2", "2016-3", "2016-4", "2016-5", "2016-6", "2016-7", "2016-8", "2016-9", "2016-10", "2016-11", "2016-12"]
    }, {
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
                    return '降水量  ' + params.value + (params.seriesData.length ? '：' + params.seriesData[0].data : '');
                }
            }
        },
        data: ["2015-1", "2015-2", "2015-3", "2015-4", "2015-5", "2015-6", "2015-7", "2015-8", "2015-9", "2015-10", "2015-11", "2015-12"]
    }],
    yAxis: [{
        type: 'value'
    }],
    series: [{
        name: '2015 降水量',
        type: 'line',
        xAxisIndex: 1,
        smooth: true,
        data: [2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3]
    }, {
        name: '2016 降水量',
        type: 'line',
        smooth: true,
        data: [3.9, 5.9, 11.1, 18.7, 48.3, 69.2, 231.6, 46.6, 55.4, 18.4, 10.3, 0.7]
    }]
};;
if (option && typeof option === "object") {
    myChart.setOption(option, true);
}
</script>
<!-- pho页面代码-结束 -->




