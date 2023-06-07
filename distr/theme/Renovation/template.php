<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

require_once dirname(__FILE__).'/shared.php';

# Include the header template
include('header.inc.php'); 
?>
	<div class="wrapper clearfix">
		<!-- page content -->
		<article>
			<section>
				<!-- title and content -->
				<h1><?php get_page_title(); ?></h1>
				<?php get_page_content(); ?>
				<!-- page footer -->
				<div class="footer">
					<?php if ($timestamp) {?>
					<p>Published on <time datetime="<?php get_page_date('Y-m-d'); ?>" pubdate><?php get_page_date('F jS, Y'); ?></time></p>
					<?php }?>
				</div>
			</section>
			
		</article>
		<!-- include the sidebar template -->
		<?php include('sidebar.inc.php'); ?>
	</div>

<!-- include the footer template -->
<?php include('footer.inc.php'); ?>
