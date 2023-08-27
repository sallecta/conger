<?php if(!defined('APP')){ die('you cannot load this page directly.'); }?>
<!DOCTYPE html>
<html lang="<?=th::$lang;?>">
<head>
	<title><?=th::$title_tag;?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="<?=th::$cpath;?>client/fonts/fonts.css">
	<link rel="stylesheet" type="text/css" href="<?=th::$cpath;?>client/css/vars.css">
	<link rel="stylesheet" type="text/css" href="<?=th::$cpath;?>client/css/flex_layout.css">
	<link rel="stylesheet" type="text/css" href="<?=th::$cpath;?>client/css/menu.css">
	<link rel="stylesheet" type="text/css" href="<?=th::$cpath;?>client/css/main.css">
<?php get_header();?>
<?php if (th::$img_wide) { ?>
	<style>
		header .header {background-image: url("<?=th::$img_wide;?>");}
	</style>
<?php } echo "\n";?>
</head>
<body>
<div class="site">
	<header>
		<div class="header">
			<div class="logo">
<?php if (th::$logo){ ?>
				<div class="img_link">
					<a href="<?=av::get('cpath');?>">
						<img src="<?=th::$logo;?>" alt="logo">
					</a>
				</div>
<?php } ?>
<?php $out='[$header1$]'; if(field::shortcode($out)){ ?>
				<div>
					<?=$out;?>
				</div>
<?php } unset($out);?>
			</div> <?=dev::rhtmlcom('logo');?>
			<nav class="menu">
				<?=th::$nav."\n";?>
			</nav>
		</div> <?=dev::rhtmlcom('header');?>
		<div class="wr">
			<div class="breadcrumbs"><?=th::breadcrumbs();?></div>
		</div>
	</header>
	<div class="flexc">
<?php if (th::$sidebar1){ ?>
		<div class="flexc_1 block_side left">
			<?=th::$sidebar1;?>
		</div> <?=dev::rhtmlcom('flexc_1');?>
<?php } ?>
<?php if (th::$sidebar2){ ?>
		<div class="flexc_2 block_side right">
			<?=th::$sidebar2."\n";?>
		</div> <?=dev::rhtmlcom('flexc_2');?>
<?php } ?>
		<div class="flexc_3 block_main">
			<?php get_page_content(); ?>
		</div> <?=dev::rhtmlcom('flexc_3');?>
	</div> <?=dev::rhtmlcom('flexc');?>
	<footer>
		<div class="footer">
			<div class="footer_item" id="contact">
<?php $out='[$footer1$]'; if(field::shortcode($out)){ ?>
				<?=$out;?>
<?php } unset($out);?>
			</div> <?=dev::rhtmlcom('footer_item');?>
			<div class="footer_item">
<?php $out='[$footer2$]'; if(field::shortcode($out)){ ?>
				<?=$out;?>
<?php } unset($out);?>
			</div> <?=dev::rhtmlcom('footer_item');?>
		</div> <?=dev::rhtmlcom('footer');?>
		<div class="footer_last">
			<div class="footer_item_last">
<?php $out='[$footer3$]'; if(field::shortcode($out)){ ?>
				<?=$out;?>
<?php } unset($out);?>
			</div>
		</div> <?=dev::rhtmlcom('footer_last');?>
<?php get_footer(); ?>
	</footer>
	<a id="to_top" href="#"></a>
</div> <?=dev::rhtmlcom('site');?>
	<script src="<?=th::$cpath;?>client/js/to_top.js"></script>
</body>
</html>
