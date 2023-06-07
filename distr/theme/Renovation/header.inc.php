<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }?>
<!DOCTYPE html>
<html lang="en" >
<head>
<meta charset="utf-8">
	<title><?php get_page_clean_title(); ?> - <?php get_site_name(); ?></title>
	<meta name="robots" content="index, follow">
	<link href='//fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'>
	<link href="<?php get_theme_url(); ?>/assets/css/reset.css" rel="stylesheet">
	<link href="<?php get_theme_url(); ?>/style.css?v=<?php echo get_site_version(); ?>" rel="stylesheet">
	<?php get_header(); ?>
</head> 
<body id="<?php get_page_slug(); ?>" >
	<header>
		<div class="header">
			<div class="wrapper">
				<!-- logo/sitename -->
				<a href="<?php get_site_url(); ?>" id="logo" ><?php get_site_name(); ?></a>
				<!-- new nav -->
				<div class="menudt">
					<ul>
						<?php get_nested_navigation(return_page_slug()); ?>
					</ul>
				</div>
			</div>
		</div>
		<!-- breadcrumbs: only show when NOT on homepage -->
		<p class="breadcrumbs" >
			<span class="wrapper">
				<?php Renovation_Parent_Link(get_parent(FALSE)); ?> <b><?php get_page_clean_title(); ?></b>
			</span>
		</p>
</header>
