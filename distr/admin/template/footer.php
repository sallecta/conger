<?php

?>
		<div id="footer">
      		<div class="footer-left" >
      		<?php 
	  		include(av::get('spath_admin_inc') ."configuration.php");
      		
      		if(!isAuthPage()){ ?>
	      		<p>&copy; 2023-<?php echo date('Y'); ?> <a href="<?=av::get('url');?>" target="_blank" ><?=av::get('title');?></a>
	      		&ndash; <?=i18n_r('VERSION');?> <?=av::get('version');?>
	      		</p> 
      		</div> <!-- end .footer-left -->
	      	<div class="gslogo" >
		      	<a href="http://example.org/" target="_blank" ><img src="<?=av::get('cpath_admin');?>template/images/conger_logo.svg" alt="GetSimple Content Management System" /></a>
		    </div>
	      	<div class="clear"></div>
	      	<?php
		      		get_scripts_backend(TRUE);
		      		event::create('footer'); 
	      		}
	      	?>

		</div><!-- end #footer -->
	</div><!-- end .wrapper -->
</body>
</html>
