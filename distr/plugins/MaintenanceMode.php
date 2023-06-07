<?php
/*
Plugin Name: DY Maintenance
Description: Enabling maintenance mode of the website
Version: 1.0
Initial author : Dmitry Yakovlev http://dimayakovlev.ru/
*/

$plgid_maint = basename(__FILE__, '.php');

i18n_merge($plgid_maint) || i18n_merge($plgid_maint, 'en_US');

register_plugin(
  $plgid_maint,
  'Maintenance Mode',
  '1.1',
  'Alexander Gribkov, Dmitry Yakovlev',
  'http://example.org',
  i18n_r($plgid_maint . '/DESCRIPTION'),
  '',
  'MaintenanceModeSettingsRender'
);

add_action('settings-website-extras', 'MaintenanceModeSettingsRender');
add_action('settings-website', 'MaintenanceModeSettingsSave');
add_action('header', 'MaintenanceModeNotify');
add_action('index-pretemplate', 'MaintenanceMode');
function MaintenanceMode()
{
	global $dataw;
	global $TEMPLATE;
	global $USR;
	if ((string)$dataw->MAINTENANCE == '1' && $USR == null)
	{
		$protocol = ('HTTP/1.1' == $_SERVER['SERVER_PROTOCOL']) ? 'HTTP/1.1' : 'HTTP/1.0';
		header($protocol . ' 503 Service Unavailable', true, 503);
		header('Retry-After: 3600');
		if (is_readable($maintenance_template = GSTHEMESPATH . $TEMPLATE . '/maintenance.php'))
		{
			include_once $maintenance_template;
		}
		else
		{
?>
<!DOCTYPE html>
<html lang="<?php echo get_site_lang(true); ?>">
	<head>
		<meta charset="utf-8">
		<title><?php get_site_name(); ?></title>
	</head>
	<body>
		<div><?php echo strip_decode($dataw->MAINTENANCE_MESSAGE); ?></div>
	</body>
</html>
<?php
		}
		die;
	}
}

function MaintenanceModeNotify()
{
	global $plgid_maint;
	i18n_merge($plgid_maint) || i18n_merge($plgid_maint, 'en_US');
	$dataw = getXML(GSDATAOTHERPATH . 'website.xml');
	if ((string)$dataw->MAINTENANCE == '1')
	{
		$msg = json_encode(i18n_r($plgid_maint . '/NOTE'));
?>
<script type="text/javascript">
	$(function()
	{
		$('div.bodycontent').before('<div class="notify" style="display:block;">'+<?php echo $msg; ?>+'</div>');
		$(".maintenance-notify").fadeOut(500).fadeIn(500);
	});
</script>
<?php
	}
}

function MaintenanceModeSettingsRender()
{
	global $plgid_maint;
	$dataw = getXML(GSDATAOTHERPATH . 'website.xml');
?>
	<div class="section" id="maintenance">
		<p class="inline">
			<input type="checkbox" name="maintenance" value="1"<?php echo $dataw->MAINTENANCE ? ' checked="checked"' : ''; ?>>
			<label for="maintenance"><?php i18n($plgid_maint . '/SWITCH_LABEL'); ?></label>
		</p>
		<p>
			<label for="maintenance_message"><?php i18n($plgid_maint . '/TEXT_LABEL'); ?>:</label>
			<textarea name="maintenance_message" class="text short charlimit" style="height: 62px;"<?php if ($dataw->MAINTENANCE) echo ' required';?>><?php echo strip_decode($dataw->MAINTENANCE_MESSAGE); ?></textarea>
		</p>
	</div>
	<script>
		$(document).ready(function()
		{
			$('input[name="maintenance"]').click(function() {
			$('textarea[name="maintenance_message"]').prop('required', $(this).prop('checked'));
			});
		});
	</script>
<?php
}

function MaintenanceModeSettingsSave()
{
	global $xmls,  $plgid_maint;
	if (isset($_POST['maintenance']))
	{
	  $xmls->addChild('MAINTENANCE', '1');
	}
	if (isset($_POST['maintenance_message']))
	{
		$xmls->addChild('MAINTENANCE_MESSAGE')->addCData(safe_slash_html($_POST['maintenance_message']));
	}
}
