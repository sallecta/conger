<?php 
/**
 * Support
 *
 * @package GetSimple
 * @subpackage Support
 */

# Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('SUPPORT') ); 

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
	
			<p><?php i18n('WELCOME_MSG'); ?> <?php i18n('WELCOME_P'); ?></p>
			
			<ul>
				<li><a href="health-check.php"><?php i18n('WEB_HEALTH_CHECK'); ?></a></li>
				<li><a href="edit.php"><?php i18n('CREATE_NEW_PAGE'); ?></a></li>
				<li><a href="upload.php"><?php i18n('UPLOADIFY_BUTTON'); ?></a></li>
				<li><a href="settings.php"><?php i18n('GENERAL_SETTINGS'); ?></a></li>
				<li><a href="theme.php"><?php i18n('CHOOSE_THEME'); ?></a></li>
				<?php event::create('welcome-link'); ?>
				<?php event::create('welcome-doc-link'); ?>
			</ul>
			
			<h3><?php i18n('SUPPORT');?></h3>
			<ul>
				<li><p><a href="log.php?log=failedlogins.log"><?php i18n('VIEW_FAILED_LOGIN');?></a></p></li>
				<?php event::create('support-extras'); ?>
			</ul>

		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-support.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>
