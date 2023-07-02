<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }?>
<!DOCTYPE html>
<html lang="en" >
<head>
	<meta charset="utf-8">
	<title><?php get_site_name(); ?> - <?php get_page_clean_title(); ?></title>
	<meta name="robots" content="index, follow">
	<link href="<?php get_theme_url(); ?>/assets/fonts/fonts.php"  rel='stylesheet' type='text/css'>
	<link href="<?php get_theme_url(); ?>/assets/css/reset.css" rel="stylesheet">
	<link href="<?php get_theme_url(); ?>/style.css" rel="stylesheet">
	<?php get_header(); echo "\n"; ?>
<?php if (field::get_by_ref('img_wide',$out)) { ?>
	<style>
		header .header {background-image: url("<?=av::get('cpath').$out;?>");}
	</style>
<?php } ?>
</head> 
<body id="<?php get_page_slug(); ?>" >
	<header>
		<div class="header">
			<div class="wrapper">
				<a href="<?php get_site_url(); ?>" id="logo" ><?php get_site_name(); ?></a>
				<p class="role"><?=field::get('site_role');?></p>
				<div class="menudt">
					<ul>
						<?php get_nested_navigation(return_page_slug()); ?>
					</ul>
				</div>
			</div>
		</div>
		<p class="breadcrumbs" >
			<span class="wrapper">
				<?php Renovation_Parent_Link(get_parent(FALSE)); ?> <b><?php get_page_clean_title(); ?></b>
			</span>
		</p>
</header>
