<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo $this->lang->line('wlblazers'); ?> <?php echo $this->lang->line('database_monitor_system'); ?></title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <base href="<?php echo base_url().'application/views/static/'; ?>" />
    <link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.css">
    <link href="lib/bootstrap/css/jquery-ui-1.10.0.custom.css" rel="stylesheet" />
    <link href="lib/bootstrap/css/prettify.css"  rel="stylesheet">
    
    <link href="lib/font-awesome/css/font-awesome.css"  rel="stylesheet">
    <link href="stylesheets/theme.css" rel="stylesheet" type="text/css">
    <link href="stylesheets/style.css" rel="stylesheet" type="text/css">
    <link href="stylesheets/wlblazers.css" rel="stylesheet" type="text/css">
    
    <script src="lib/jquery-2.2.4.min.js" type="text/javascript"></script>
    <script src="lib/bootstrap/js/jquery-ui-1.10.0.custom.min.js"></script>
	
	
    <link href="iconfont/iconfont.css" rel="stylesheet" type="text/css">
	
    <style type="text/css">
        #line-chart {
            height:300px;
            width:800px;
            margin: 0px auto;
            margin-top: 1em;
        }
        .brand { font-family: georgia, serif; }
        .brand .first {
            color: #ccc;
            font-style: italic;
        }
        .brand .second {
            color: #fff;
            font-weight: bold;
        }
		/*
		by.zx
		*/
		
		body{
			background:#3B3A3F
		}
		
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
  </head>

  <!--[if lt IE 7 ]> <body class="ie ie6"> <![endif]-->
  <!--[if IE 7 ]> <body class="ie ie7 "> <![endif]-->
  <!--[if IE 8 ]> <body class="ie ie8 "> <![endif]-->
  <!--[if IE 9 ]> <body class="ie ie9 "> <![endif]-->
  <!--[if (gt IE 9)|!(IE)]><!--> 
  <body class=""> 
  <!--<![endif]-->
    
    <div class="navbar" <?php if($this->input->cookie('lang_current')=='zh-hans') echo  "style='font-family: 微软雅黑;'" ?>>
        <div class="navbar-inner">
                <ul class="nav pull-right">
					<?php 
						$model = $this->uri->segment(1);
						$view = $this->uri->segment(2);
					?>
                    <li <?php if($model=='index' && ($view=='index' || $view=='')){ echo "class='active'";} ?> ><a href="<?php echo site_url('index/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button"><?php echo $this->lang->line('home'); ?></a></li>
                    <li <?php if($model=='index' && $view=='dashboard'){ echo "class='active'";} ?> ><a href="<?php echo site_url('index/dashboard'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button"><?php echo $this->lang->line('dashboard'); ?></a></li>
                    <li id="fat-menu" class="dropdown">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-th-large"></i><?php echo $this->lang->line('database'); ?>
                            <i class="icon-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li <?php if($model=='wl_oracle'){ echo "class='active'";} ?>><a href="<?php echo site_url('wl_oracle/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button">Oracle</a></li>
                            <li class="divider"></li>
                            <li <?php if($model=='wl_mysql'){ echo "class='active'";} ?>><a href="<?php echo site_url('wl_mysql/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button">MySQL</a></li>
                            <li class="divider"></li>
                            <li <?php if($model=='wl_sqlserver'){ echo "class='active'";} ?>><a href="<?php echo site_url('wl_sqlserver/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button">SQLServer</a></li>
                        </ul>
                    </li>                    
                    
                    
                    <!-- <li <?php if($model=='screen'){ echo "class='action'";} ?>><a href="<?php echo site_url('screen/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button"><?php echo $this->lang->line('screen'); ?></a></li> -->
                    <!-- <li <?php if($model=='wl_mysql'){ echo "class='action'";} ?>><a href="<?php echo site_url('wl_mysql/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button">MySQL</a></li> -->
                    <!-- <li <?php if($model=='wl_oracle'){ echo "class='action'";} ?>><a href="<?php echo site_url('wl_oracle/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button">Oracle</a></li> -->
                    <!-- <li <?php if($model=='wl_mongodb'){ echo "class='action'";} ?>><a href="<?php echo site_url('wl_mongodb/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button">MongoDB</a></li> -->
                    <!-- <li <?php if($model=='wl_sqlserver'){ echo "class='action'";} ?>><a href="<?php echo site_url('wl_sqlserver/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button">SQLServer</a></li> -->
                    <!-- <li <?php if($model=='wl_redis'){ echo "class='action'";} ?>><a href="<?php echo site_url('wl_redis/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button">Redis</a></li> -->
                    <li <?php if($model=='wl_os'){ echo "class='active'";} ?>><a href="<?php echo site_url('wl_os/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button"><?php echo $this->lang->line('_OS'); ?></a></li>
                    <li <?php if($model=='alarm'){ echo "class='active'";} ?>><a href="<?php echo site_url('alarm/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button"><?php echo $this->lang->line('alarm'); ?></a></li>
                    <li <?php if($model=='settings'){ echo "class='active'";} ?>><a href="<?php echo site_url('settings/index'); ?>" class="hidden-phone visible-tablet visible-desktop" role="button"><?php echo $this->lang->line('settings'); ?></a></li>
                    

                    
                    <!-- <li id="fat-menu" class="dropdown">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-th-large"></i><?php echo $this->lang->line('language'); ?>
                            <i class="icon-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a tabindex="-1" href="<?php echo site_url('language/switchover/english').'?return_url='.current_url(); ?>">English</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="<?php echo site_url('language/switchover/zh-hans').'?return_url='.current_url(); ?>">简体中文</a></li>
                            <li class="divider"></li>
                        </ul>
                    </li> -->
                    <li id="fat-menu" class="dropdown">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-user"></i> <?php echo $this->session->userdata('username') ?>
                            <i class="icon-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a tabindex="-1" href="<?php echo site_url('profile/index'); ?>"><?php echo $this->lang->line('profile'); ?></a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="<?php echo site_url('login/logout')?>"><?php echo $this->lang->line('logout'); ?></a></li>
                        </ul>
                    </li>
                    
                </ul>
                
				<div class="nav_logo">
				<span class="nav_logo_img">
				<img src="./images/logo.png"/></span>
				
				<a class="brand" href="<?php echo site_url('index')?>">
				<!--<span class="first"></span>-->
				<span class="second" style="display:none;"><?php echo $this->lang->line('wlblazers'); ?></span>
				</a>
				</div>
        </div>
    </div>
    

    <?php $this->load->model("user_model");?>
    <?php $menus = $this->user_model->get_user_menus(); //print_r($menus);?>
    <div class="sidebar-nav cf" <?php if($this->input->cookie('lang_current')=='zh-hans') echo  "style='font-family: 微软雅黑;font-size:12px'" ?> >
        <li <?php if(is_active_menu($this->user_model->get_user_current_action(),"index/") || is_active_menu($this->user_model->get_user_current_action(),"index/index")) echo "class=action"; ?> ><a href="<?php echo site_url('index')?>" class="nav-header" ><i class="icon-home"></i><?php echo $this->lang->line('home'); ?></a></li>
        <li <?php if(is_active_menu($this->user_model->get_user_current_action(),"index/dashboard")) echo "class=action"; ?> ><a href="<?php echo site_url('index/dashboard')?>" class="nav-header" ><i class="icon-home"></i><?php echo $this->lang->line('dashboard'); ?></a></li>
        <?php if (isset($menus)):?>
          	<?php foreach($menus as $menu):?>
            <li><a href="#<?php echo $menu['parent_url'];?>" class="nav-header collapsed" data-toggle="collapse"><i class="<?php echo $menu['parent_icon'];?>"></i><?php echo $this->lang->line('_'.$menu['parent_title']); ?><i class="icon-chevron-up" ></i></a></li>
            <ul id="<?php echo $menu['parent_url'];?>" class="nav nav-list collapse <?php if(is_active_menu($this->user_model->get_user_current_action(),$menu['parent_action'])) echo "in"; ?> ">
              <?php foreach($menu as $sub_menu):?>
              <?php if(is_array($sub_menu)):?>
              <li <?php if(is_active_menu($this->user_model->get_user_current_action(),$sub_menu['action'])) echo "class=action"; ?> ><a href="<?php echo site_url($sub_menu['url']) ?>"><i class="<?php echo $sub_menu['icon'];?>"></i><?php echo $this->lang->line('_'.$sub_menu['title']); ?></a></li>
              <?php endif;?>
     	      <?php endforeach;?>
            </ul>
            <?php endforeach;?>
       	<?php endif;?>
        
        
    </div>
    

    
<div id="inner_frame" class="content" >
       <?php echo $content_for_layout ; ?>



                    
                    <footer>
                        <hr>

                        <!-- Purchase a site license to remove this link from the footer: http://www.portnine.com/bootstrap-themes -->
                        <p class="pull-right">Power by <a href="#" target="_blank">DR&M</a></p>

                        <p>&copy; 2018 <a href="#" target="_blank">DR&M Cloud</a>&nbsp;(Disaster Recovery & Monitor Cloud)</p>
                    </footer>
                    
            </div>
        </div>
    </div>
    


    <script src="lib/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript">
        $("[rel=tooltip]").tooltip();
        $(function() {
            $('.demo-cancel-click').click(function(){return false;});
        });
    </script>
    
  </body>
</html>


