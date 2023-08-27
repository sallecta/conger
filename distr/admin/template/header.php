<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php

global $SITENAME, $SITEURL;

$GSSTYLE         = getDef('GSSTYLE') ? GSSTYLE : '';
$GSSTYLE_sbfixed = in_array('sbfixed',explode(',',$GSSTYLE));
$GSSTYLE_wide    = in_array('wide',explode(',',$GSSTYLE));

if(get_filename_id()!='index') event::create('admin-pre-header');
$cpadm=av::get('cpath_admin');
$cpmodules=av::get('cpath_modules');
$cmc=av::get('cpath_modules_client');
?>
<!DOCTYPE html>
<html lang="<?=get_site_lang(true);?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?=$title;?></title>
<?php if(!isAuthPage()) { ?>
	<meta name="generator" content="<?=av::get('name') .' '. av::get('version');?>" /> 
	<link rel="shortcut icon" href="<?=$cmc;?>img/favicon/favicon.png" type="image/x-icon" />
	<link rel="author" href="<?=$cpadm;?>humans.txt" />
	<link rel="apple-touch-icon" href="<?=$cmc;?>img/favicon/apple-touch-icon.png"/>
<?php } ?>
	<meta name="robots" content="noindex, nofollow">
	<link rel="stylesheet" type="text/css" href="<?=$cmc;?>admin/css/style.php" media="screen" />
	<script type="text/javascript" src="<?=$cmc;?>admin/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?=$cmc;?>admin/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?=$cmc;?>admin/js/jquery.getsimple.js?v=<?=av::get('version');?>"></script>
<?php if( ((get_filename_id()=='upload') || (get_filename_id()=='image')) && (!getDef('GSNOUPLOADIFY',true)) ) { ?>
	<script type="text/javascript" src="<?=$cmc;?>admin/js/uploadify/jquery.uploadify.js?v=3.0"></script>
<?php } ?>
<?php if(get_filename_id()=='image') { ?>
	<script type="text/javascript" src="<?=$cmc;?>admin/js/jcrop/jquery.Jcrop.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?=$cmc;?>admin/js/jcrop/jquery.Jcrop.css" media="screen" />
<?php } ?>
<?php 
	if(!isAuthPage()) event::create('header'); 
	if(!isAuthPage()) event::create('ev_client_admin_head');
?>
	<script type="text/javascript">
		var GS = {};
		GS.i18n = new Array();
		GS.i18n['PLUGIN_UPDATED'] = '<?php i18n("PLUGIN_UPDATED"); ?>';
		GS.i18n['ERROR'] = '<?php i18n("ERROR"); ?>';
	</script>
</head>

<body id="<?=get_filename_id();?>" >
	<div class="header" id="header" >
		<div class="wrapper clearfix"><?php
event::create('header-body'); ?>
