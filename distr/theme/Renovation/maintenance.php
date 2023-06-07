<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

require_once dirname(__FILE__).'/shared.php';
?>
<!DOCTYPE html>
<html lang="en" >
<head>
<meta charset="utf-8">
	<title><?php get_site_name(); ?></title>
	<meta name="robots" content="index, follow">
	<link href='//fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'>
	<link href="<?php get_theme_url(); ?>/assets/css/reset.css" rel="stylesheet">
	<link href="<?php get_theme_url(); ?>/style.css?v=<?php echo get_site_version(); ?>" rel="stylesheet">
	<?php get_header(); ?>
</head> 
<body >
	<header>
		<div class="header">
			<div class="wrapper">
				<!-- logo/sitename -->
				<a href="<?php get_site_url(); ?>" id="logo" ><?php get_site_name(); ?></a>
				<!-- new nav -->
				<div class="menudt">
				</div>
			</div>
		</div>
</header>
	<div class="wrapper clearfix">
		<!-- page content -->
		<article>
			<section>
				<?php echo strip_decode($dataw->MAINTENANCE_MESSAGE); ?>
				<!-- page footer -->
				<div class="footer">
				</div>
			</section>
		</article>
	</div>

<!-- include the footer template -->
<?php include('footer.inc.php'); ?>
