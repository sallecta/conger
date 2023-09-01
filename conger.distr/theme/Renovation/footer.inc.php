<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } 
$email=field::get('site_email');
?>
	<footer class="clearfix" >
		
		<?php get_footer(); ?>

	 	<div class="wrapper">
			<div class="left"><?php //echo date('Y'); ?> <a href="<?=field::get('site_url'); ?>" ><?php get_site_name(); ?></a></div>
			<div class="right">
				<?php if ($email) { ?>
				<a href="mailto:<?=field::get('site_email');?>"><?=field::get('site_email');?></a>
				<?php } ?>
			</div>
		</div>
	</footer>
	 
</body>
</html>
