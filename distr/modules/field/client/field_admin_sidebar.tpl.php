<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>

<p><?php i18n('field/usage_descr'); ?></p>
<p><?php i18n('field/descr_in_code'); ?></p>
<ul>
	<li><code>field::get('field_name');</code></li> 
	<li><code>field::get_by_ref('field_name',$out); if(!empty($out){echo $out;}</code></li>   
</ul>

<p><?php i18n('field/descr_in_content'); ?></p>
<ul>
	<li><code>[$field_name$];</code></li>   
</ul>
