<?php
$tr='i18n_r';
?>
	<?php if (field::$active) { ;?><script type="text/javascript" src="<?=av::get('cpath_modules_client');?>admin/js/ckeditor/distr/ckeditor.js<?php echo getDef("GSCKETSTAMP",true) ? "?t=".getDef("GSCKETSTAMP") : ""; ?>"></script>
<?php } ?>
	<?php if (field::$active) { ;?><script type="text/javascript" src="<?=field::$cpath;?>admin/js/web_editor.js.php"></script>
<?php } ?>
	<?php if (field::$active) { ;?><script type="text/javascript" src="<?=field::$cpath;?>admin/js/module_field.js.php"></script>
<?php } ?>
	<?php if (field::$active) { ;?><script type="text/javascript">client_ui.conger_field.msg='<?=field::$cmsg;?>';</script>
<?php } ?>
