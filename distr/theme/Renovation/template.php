<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

require_once dirname(__FILE__).'/shared.php';
# Include the header template
include('header.inc.php'); 
?>
	<div class="wrapper clearfix">
		<!-- page content -->
		<article>
			<section>
				<h1><?php get_page_title(); ?></h1>
				<?php get_page_content(); ?>
				<div class="footer">
					<?php if ($timestamp) {?>
					<p>Published on <time datetime="<?php get_page_date('Y-m-d'); ?>" pubdate><?php get_page_date('F jS, Y'); ?></time></p>
					<?php }?>
				</div>
			</section>
			
		</article>
		<?php include('sidebar.inc.php'); ?>
	</div>
<?php include('footer.inc.php'); ?>
