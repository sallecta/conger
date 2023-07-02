<?php
/**
 * Navigation Include Template
 *
 * @package GetSimple
 */
 
$debugInfoUrl = 'http://example.org/docs/debugging';

$cpadm=av::get('cpath_admin');

if (cookie_check())
{
	global $USR;
	?>
	<ul id="pill">
		<li class="leftnav">
			<a href="<?=$cpadm;?>logout.php" accesskey="<?=find_accesskey(i18n_r('TAB_LOGOUT'));?>">
				<?php echo i18n_r('TAB_LOGOUT');?>
			</a>
		</li>
		<li class="rightnav" >
			<a href="<?=$cpadm;?>settings.php#profile"><?=i18n_r('WELCOME');?>, <strong><?=$USR;?></strong>!</a>
		</li>
	</ul>
	<?php
}

//determine page type if plugin is being shown
if (get_filename_id() == 'load') {
	$plugin_class = $plugin_info[$plugin_id]['page_type'];
} else {
	$plugin_class = '';
}
?>
<h1 id="sitename"><a href="<?=av::get('cpath')?>" target="_blank" ><?php echo av::get('site_name'); ?></a></h1>
<ul class="nav <?php echo $plugin_class; ?>">
	<li id="nav_pages" ><a class="pages" href="<?=$cpadm;?>pages.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_PAGES'));?>" ><?php i18n('TAB_PAGES');?></a></li>
	<li id="nav_upload" ><a class="files" href="<?=$cpadm;?>upload.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_FILES'));?>" ><?php i18n('TAB_FILES');?></a></li>
	<li id="nav_theme" ><a class="theme" href="<?=$cpadm;?>theme.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_THEME'));?>" ><?php i18n('TAB_THEME');?></a></li>
	<li id="nav_backups" ><a class="backups" href="<?=$cpadm;?>backups.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_BACKUPS'));?>" ><?php i18n('TAB_BACKUPS');?></a></li>
	<li id="nav_plugins" ><a class="plugins" href="<?=$cpadm;?>plugins.php" accesskey="<?php echo find_accesskey(i18n_r('PLUGINS_NAV'));?>" ><?php i18n('PLUGINS_NAV');?></a></li>
	
	<?php event::create('nav-tab');	?>
	
	<li id="nav_loaderimg" ><img class="toggle" id="loader" src="<?=$cpadm;?>template/images/ajax.gif" alt="" /></li>
	<li class="rightnav" >
		<a class="support last" href="<?=$cpadm;?>support.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_SUPPORT'));?>" >
			<?php i18n('TAB_SUPPORT');?>
		</a>
	</li>
	<li class="rightnav" >
		<a class="health last" href="<?=$cpadm;?>health.php" accesskey="<?php echo find_accesskey(i18n_r('tab_health'));?>" >
			<?php i18n('tab_health');?>
		</a>
	</li>
	<li class="rightnav" >
		<a class="settings first" href="<?=$cpadm;?>settings.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_SETTINGS'));?>" >
			<?php i18n('TAB_SETTINGS');?>
		</a>
	</li>
</ul>

</div>
</div>
	
<div class="wrapper">

	<?php include('template/error_checking.php'); ?>
