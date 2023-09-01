<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } ?>

<div style="width:100%;margin:0 -15px -15px -10px;padding:0px;">
	<h3 class="floated"><?php i18n(PLGID_CATALOG.'/PLUGIN_TITLE'); ?></h3>
	<div class="edit-nav clearfix" style="">
		<a href="load.php?id=catalog&faq_help" <?php if (isset($_GET['faq_help'])) {echo 'class="current"';}?>> <?php i18n(PLGID_CATALOG.'/HELP'); ?></a>
		<a href="load.php?id=catalog&faq_categories" <?php if (isset($_GET['faq_categories'])){echo 'class="current"';}?>><?php i18n(PLGID_CATALOG.'/CATEGORIES'); ?></a>
		<a href="load.php?id=catalog"><?php i18n(PLGID_CATALOG.'/THINGS'); ?></a>
	</div> 
</div>
</div>
<div class="main" style="margin-top:-10px;">
