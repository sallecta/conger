<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } ?>

		<h3><?php i18n(PLGID_CATALOG.'/INSTR1'); ?></h3>
		<?php i18n(PLGID_CATALOG.'/INSTR2'); ?><br/>
		<?php highlight_string('<?php getFAQ(); ?>'); ?><br/><br/>
		<?php i18n(PLGID_CATALOG.'/INSTR3'); ?><br/>
		<?php highlight_string('<?php getFAQ(\''.i18n_r(PLGID_CATALOG.'/YR_CATNAME').'\'); ?>'); ?><br/><br/><br/>
		<strong><?php i18n(PLGID_CATALOG.'/INSTR4'); ?></strong><br/>
		<?php i18n(PLGID_CATALOG.'/INSTR5'); ?><br/>
		<pre>{$ <?php i18n(PLGID_CATALOG.'/YR_CATNAME'); ?> $}</pre>
