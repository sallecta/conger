<?php if(!defined('APP')){ die('you cannot load this page directly.'); }

if (function_exists('return_i18n_pages'))
{
	require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
}

function i18n_customfields_list_pages_json()
{
	if (function_exists('find_i18n_url') && class_exists('I18nNavigationFrontend'))
	{
		$slug = isset($_GET['id']) ? $_GET['id'] : (isset($_GET['newid']) ? $_GET['newid'] : '');
		$pos = strpos($slug, '_');
		$lang = $pos !== false ? substr($slug, $pos+1) : null;
		$structure = I18nNavigationFrontend::getPageStructure(null, false, null, $lang);
		$pages = array();
		$nbsp = html_entity_decode('&nbsp;', ENT_QUOTES, 'UTF-8');
		$lfloor = html_entity_decode('&lfloor;', ENT_QUOTES, 'UTF-8');
		foreach ($structure as $page)
		{
			$text = ($page['level'] > 0 ? str_repeat($nbsp,5*$page['level']-2).$lfloor.$nbsp : '').cl($page['title']);
			$link = find_i18n_url($page['url'], $page['parent'], $lang ? $lang : return_i18n_default_language());
			$pages[] = array($text, $link);
		}
		return json_encode($pages);
	}
	else
	{
		return list_pages_json();
	}
} // i18n_customfields_list_pages_json

function i18n_customfields_customize_ckeditor($editorvar) { // copied and modified from ckeditor_add_page_link()
	echo "
	// modify existing Link dialog
	CKEDITOR.on( 'dialogDefinition', function( ev )	{
		if ((ev.editor != " . $editorvar . ") || (ev.data.name != 'link')) return;

		// Overrides definition.
		var definition = ev.data.definition;
		definition.onFocus = CKEDITOR.tools.override(definition.onFocus, function(original) {
			return function() {
				original.call(this);
					if (this.getValueOf('info', 'linkType') == 'localPage') {
						this.getContentElement('info', 'localPage_path').select();
					}
			};
		});

		// Overrides linkType definition.
		var infoTab = definition.getContents('info');
		var content = getById(infoTab.elements, 'linkType');

		content.items.unshift(['Link to local page', 'localPage']);
		content['default'] = 'localPage';
		infoTab.elements.push({
			type: 'vbox',
			id: 'localPageOptions',
			children: [{
				type: 'select',
				id: 'localPage_path',
				label: 'Select page:',
				required: true,
				items: " . i18n_customfields_list_pages_json() . ",
				setup: function(data) {
					if ( data.localPage )
						this.setValue( data.localPage );
				}
			}]
		});
		content.onChange = CKEDITOR.tools.override(content.onChange, function(original) {
			return function() {
				original.call(this);
				var dialog = this.getDialog();
				var element = dialog.getContentElement('info', 'localPageOptions').getElement().getParent().getParent();
				if (this.getValue() == 'localPage') {
					element.show();
					if (" . $editorvar . ".config.linkShowTargetTab) {
						dialog.showPage('target');
					}
					var uploadTab = dialog.definition.getContents('upload');
					if (uploadTab && !uploadTab.hidden) {
						dialog.hidePage('upload');
					}
				}
				else {
					element.hide();
				}
			};
		});
		content.setup = function(data) {
			if (!data.type || (data.type == 'url') && !data.url) {
				data.type = 'localPage';
			}
			else if (data.url && !data.url.protocol && data.url.url) {
				if (path) {
					data.type = 'localPage';
					data.localPage_path = path;
					delete data.url;
				}
			}
			this.setValue(data.type);
		};
		content.commit = function(data) {
			data.type = this.getValue();
			if (data.type == 'localPage') {
				data.type = 'url';
				var dialog = this.getDialog();
				dialog.setValueOf('info', 'protocol', '');
				dialog.setValueOf('info', 'url', dialog.getValueOf('info', 'localPage_path'));
			}
		};
  });";
} // i18n_customfields_customize_ckeditor

global $SITEURL,$TEMPLATE;
global $data_edit; // SimpleXML to read from
$isI18N = function_exists('return_i18n_languages');
$creDate = $data_edit->creDate ? (string) $data_edit->creDate : (string) $data_edit->pubDate;
field::get_fields_and_types();
$fields = &field::$fields;

echo '<input type="hidden" name="creDate" value="'.htmlspecialchars($creDate).'"/>';

if (!$fields || count($fields) <= 0)
{
	return;
}
$id = $_GET['id'];

$EDLANG = i18n_r('CKEDITOR_LANG');
$EDTOOL = 'advanced';//basic/advanced
$EDOPTIONS = ''; 
$toolbar = "
['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
'/',
['Styles','Format','Font','FontSize']
";
// Editor settings end


?>
<h4><?=i18n_r('field/fields_management')?> (<?=i18n_r('by_module')?> <?=i18n_r('field/TITLE')?>).</h4>
<table class="formtable" style="clear:both;width:100%;margin-left:0;">
	<tbody>
<?php
$ndx = 0;
foreach ($fields as $field)
{
	$scope=&$field['scope'];
	if ( $scope !=='page' && $scope !=='all' )
	{
		continue;
	}
?>
		<tr style="border:0 none;">
<?php
	$ndx++;
	$key = $field['key'];
	$about = $field['about'];
	$type = $field['type'];
	$value = htmlspecialchars($id ? (isset($data_edit->$key) ? $data_edit->$key : '') : (isset($field['value']) ? $field['value'] : ''), ENT_QUOTES);

	if ($field['predef'] ) // draw a full width TextBox
	{
	?>
			<td colspan="2" style="border:0 none;"><p><strong><?=$key?></strong> (<?=i18n_r('field/predef')?>)</p>
				<p><?=$field['about']?>. <?=$key?>=<?=$field['value']?></p>
			</td> 
	<?php
	continue;
	}
	if ('textfull' == $type ) // draw a full width TextBox
	{
	?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input class="text" type="text" style="width:602px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
			</td> 
	<?php
	}
	elseif ('dropdown' == $type )
	{?>
			<td style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<select id="post-<?=$key?>" name="post-<?=$key?>" class="text" style="width:295px"><?php
		foreach ($field['options'] as $option)
		{
			$selected = $value == $option ? ' selected="selected"' : '';?>
				<option <?=$selected?>><?=$option?></option><?php
		}?>
				</select>
			</td><?php
	}
	elseif ('checkbox' == $type ) 
	{
		if($value){ $checked=' checked=\"checked\"';}else {$checked='';}
	?>
			<td style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input type="checkbox" style="width:auto;" id="post-<?=$key?>" name="post-<?=$key?>" value="on"<?=$checked?>/>
			</td> 
	<?php
	}
	elseif ('web_editor' == $type ) 
	{
	?>
			<td style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<textarea style="width:602px;height:200px;border: 1px solid #AAAAAA;" id="post-<?=$key?>" name="post-<?=$key?>"><?=$value?></textarea>
			</td>
			<script type="text/javascript">
				// missing border around text area, too much padding on left side, ...
				$(function()
				{
					var ckedroot=<?="'".av::get('cpath').'admin/template/js/ckeditor'."'";?>;
					var editor_<?php echo $ndx; ?> = CKEDITOR.replace( 'post-<?php echo $key; ?>',
						{
							customConfig: ckedroot+'/custom/config.js',
							skin : 'conger,'+ckedroot+'/custom/skins/conger/',
							<?php if (file_exists(GSTHEMESPATH.$TEMPLATE."/editor.css")) { ?>
							contentsCss: '<?=$SITEURL."theme/$TEMPLATE/style.css";?>',
							<?php } 
							else
							{ ?>
							contentsCss: ckedroot+'/custom/contents.css',
							<?php } ?>
							forcePasteAsPlainText : true,
							entities : true,
							height: '200px',
							toolbar : [ <?php echo $toolbar; ?> ],
							language : '<?php echo $EDLANG; ?>',
							filebrowserBrowseUrl : 'filebrowser.php?type=all',
							filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
							filebrowserWindowWidth : '730',
							filebrowserWindowHeight : '500'
						}
					);
					<?php //i18n_customfields_customize_ckeditor('editor_'.$ndx); ?>
					}
				);
			</script>
	<?php
	}
	elseif ('link' == $type )
	{
			$width = 500;
	?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input class="text" type="text" style="width:<?=$width?>px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
				<span class="edit-nav"><a id="browse-<?=$key?>" href="#"><?=i18n_r('field/BROWSE_PAGES')?></a></span>
			</td> 
			<script type="text/javascript">
			function fill_<?php echo $ndx; ?>(url) {
			$('#post-<?php echo $key; ?>').val(url);
			}
			$(function() { 
			$('#browse-<?php echo $key; ?>').click(function(e) {
			window.open('<?php echo $SITEURL; ?>plugins/i18n_customfields/browser/pagebrowser.php?func=fill_<?php echo $ndx; ?>&i18n=<?php echo $isI18N; ?>', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
			});
			});
			</script>
	<?php
	}
	elseif ('image' == $type || 'file' == $type ) 
	{
		$width=500;
		$browsetext=($type=='image' ? i18n_r('field/BROWSE_IMAGES') : i18n_r('field/BROWSE_FILES'))
	?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input class="text" type="text" style="width:<?=$width?>px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
				<span class="edit-nav"><a id="browse-<?=$key?>" href="#"><?=$browsetext?></a></span>
			</td> 
			<script type="text/javascript">
			function fill_<?php echo $ndx; ?>(url) {
			$('#post-<?php echo $key; ?>').val(url);
			}
			$(function() { 
			$('#browse-<?php echo $key; ?>').click(function(e) {
			window.open('<?php echo $SITEURL; ?>plugins/i18n_customfields/browser/filebrowser.php?func=fill_<?php echo $ndx; ?>&type=<?php echo $type=='image' ? 'images' : ''; ?>', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
			});
			});
			</script>
	<?php
	}
	elseif ('text' == $type )
	{
	?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input class="text short" type="text" style="width:295px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
			</td> 
	<?php
	}
	else // default
	{
	?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type.'/default'?>)<br/>
				<input class="text short" type="text" style="width:295px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
			</td> 
	<?php
	}?>
		</tr><?php
} // foreach ($fields as $field)
?>
	</tbody>
</table>
