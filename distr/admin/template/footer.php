<?php
$logoimg=av::get('cpath_modules').'client/img/logo/logo.png';
$url=av::get('cpath');
$url=field::get('site_url');
?>
<div id="footer">
<?php 
include(av::get('spath_admin_inc') ."configuration.php");
	
if(!isAuthPage()){ ?>
	<div class="footer-left" >
		<p>&copy; 2023-<?php echo date('Y'); ?> <a href="<?=av::get('url');?>" target="_blank" ><?=av::get('name');?></a>&ndash; <?=i18n_r('VERSION');?> <?=av::get('version');?></p> 
	</div> <!-- end .footer-left -->
	<div class="logo" >
		<a href="<?=$url;?>" target="_blank" ><img src="<?=$logoimg?>" alt="Conger CMS" /></a>
	</div>
	<div class="clear"></div>
<?php
	get_scripts_backend(TRUE);
	event::create('footer'); 
}
?>
</div><!-- end #footer -->
</div><!-- end .wrapper -->
<?php event::create('client_admin_body_end');?> 
</body>
</html>
