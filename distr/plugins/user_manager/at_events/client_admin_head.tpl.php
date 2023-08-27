<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php
function indent( $a_times=1 )
{
	return str_repeat("\t",$a_times);
}
if ( count($styles)>0 )
{
?>
	<style type="text/css"><?php
	foreach ($styles as &$style)
	{
		print("\n".indent(2).$style);
	}
	print("\n");
	?>
	</style>
<?php
}

if ( count($scripts)>0 )
{
?>
	<script type="text/javascript">
		$(document).ready(function()
			{<?php
	foreach ($scripts as &$script)
	{
		print("\n".indent(4).$script);
	}
	print("\n");
	?>
			}
		);
	</script>
<?php
}
?>
