<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php

$caimgs=av::get('cpath_modules').'client/admin/img/';
$cpadm=av::get('cpath_admin');
$tr='i18n_r';
$ak='find_accesskey';

if (cookie_check())
{
	global $USR;
?>
			<ul id="pill">
				<li class="leftnav">
					<a href="<?=$cpadm;?>logout.php" accesskey="<?=$ak($tr('TAB_LOGOUT'));?>"><?=$tr('TAB_LOGOUT');?></a>
				</li>
				<li class="rightnav" >
					<a href="<?=$cpadm;?>settings.php#profile"><?=$tr('WELCOME');?>, <strong><?=$USR;?></strong>!</a>
				</li>
			</ul>
<?php
}
//determine page type if plugin is being shown
if (get_filename_id() == 'load')
{
	$plugin_class = $plugin_info[$plugin_id]['page_type'];
}
else
{
	$plugin_class = '';
}
?>
			<h1 id="sitename"><a href="<?=av::get('cpath')?>" target="_blank" ><?=av::get('site_name'); ?></a></h1>
			<ul class="nav <?=$plugin_class; ?>">
				<li id="nav_pages" ><a class="pages" href="<?=$cpadm;?>pages.php" accesskey="<?=$ak($tr('TAB_PAGES'));?>" ><?=$tr('TAB_PAGES');?></a></li>
				<li id="nav_upload" ><a class="files" href="<?=$cpadm;?>upload.php" accesskey="<?=$ak($tr('TAB_FILES'));?>" ><?=$tr('TAB_FILES');?></a></li>
				<li id="nav_theme" ><a class="theme" href="<?=$cpadm;?>theme.php" accesskey="<?=$ak($tr('TAB_THEME'));?>" ><?=$tr('TAB_THEME');?></a></li>
				<li id="nav_backups" ><a class="backups" href="<?=$cpadm;?>backups.php" accesskey="<?=$ak($tr('TAB_BACKUPS'));?>" ><?=$tr('TAB_BACKUPS');?></a></li>
				<li id="nav_plugins" ><a class="plugins" href="<?=$cpadm;?>plugins.php" accesskey="<?=$ak($tr('PLUGINS_NAV'));?>" ><?=$tr('PLUGINS_NAV');?></a></li>
				<?php event::create('nav-tab');	?>
				<li id="nav_loaderimg" ><img class="toggle" id="loader" src="<?=$caimgs;?>ajax.gif" alt="" /></li>
				<li class="rightnav" ><a class="support last" href="<?=$cpadm;?>support.php" accesskey="<?=$ak($tr('TAB_SUPPORT'));?>" ><?=$tr('TAB_SUPPORT');?></a></li>
				<li class="rightnav" ><a class="health last" href="<?=$cpadm;?>health.php" accesskey="<?=$ak($tr('tab_health'));?>" ><?=$tr('tab_health');?></a></li>
				<li class="rightnav" ><a class="settings first" href="<?=$cpadm;?>settings.php" accesskey="<?=$ak($tr('TAB_SETTINGS'));?>" ><?=$tr('TAB_SETTINGS');?></a></li>
			</ul>
		</div> <?=dev::rhtmlcom('wrapper clearfix');?>
	</div> <?=dev::rhtmlcom('header');?>
	<div class="wrapper">
<?php 
$fname=av::get('spath_admintemplate').'error_checking.php';
?>
<?php require_once(av::get('spath_admintemplate').'error_checking.php');  ?>
