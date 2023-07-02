<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }?>
		<h3><?php echo $faq_edit_add; ?></h3>
		<form action="" method="post" accept-charset="utf-8">
			<?php echo $add_new_hidden_field; ?>
			<input type="text" name="title" class="text" style="width:635px;" value="<?php echo $faq_title; ?>" onFocus="if(this.value == '<?php i18n(PLGID_CATALOG.'/TITLE'); ?>') {this.value = '';}" onBlur="if (this.value == '') {this.value = '<?php i18n(PLGID_CATALOG.'/TITLE'); ?>';}" />
			<select name="category" class="text" style="width:647px;margin:5px 0px 5px 0px">
				<?php
					if($edit_faq != null)
					{
						$selected_choice = '<option value="'.$faq_category.'">'.$faq_category.'</option>';
					}
					else 
					{
						$selected_choice = '';
						echo '<option value="">', i18n_r(PLGID_CATALOG.'/CHOOSECAT'), '</option>';
					}
					$content_file = getXML(PLGCATALOGFILE);
					foreach($content_file->category as $edit_cate)
					{	
						$atts = $edit_cate->attributes();
						if($selected_choice == $atts['name'])
						{
							echo '<option value="', $selected_choice, '">', $selected_choice, '</option>';
						}
						else
						{
							echo '<option value="', $atts['name'], '">', $atts['name'], '</option>';
						}
					}
				?>
			</select>
	
			<textarea id="post-content" name="contents"><?php echo $faq_content; ?></textarea>
		<script type="text/javascript" src="<?=av::get('cpath')?>admin/template/js/ckeditor/distr/ckeditor.js<?php echo getDef("GSCKETSTAMP",true) ? "?t=".getDef("GSCKETSTAMP") : ""; ?>"></script>
<script type="text/javascript">
	// CKEditor run script
	var startCKeditor = ( function()
	{
		//var ckedroot='rooot/dir/where/ckeditor/listed';
		var ckedroot=<?="'".av::get('cpath').'admin/template/js/ckeditor'."'";?>;
		var editor_el_name = 'post-content';
		var custom_opts =
		{
			customConfig: ckedroot+'/custom/config.js',
			//extraPlugins: 'format_buttons',
			skin : 'conger,'+ckedroot+'/custom/skins/conger/',
			filebrowserBrowseUrl : 'filebrowser.php?type=all',
			filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
			filebrowserWindowWidth : '730',
			filebrowserWindowHeight : '500',
			language : '<?php echo $EDLANG; ?>',
			defaultLanguage : '<?php echo $EDLANG; ?>',
			<?php if (file_exists(GSTHEMESPATH.$TEMPLATE."/editor.css")) { ?>
			contentsCss: '<?=$SITEURL."theme/$TEMPLATE/editor.css";?>',
			<?php } 
			else
			{ ?>
			contentsCss: ckedroot+'/custom/contents.css',
			<?php } ?>
		};
		//console.log(['ckedroot',ckedroot,'custom_opts',custom_opts]);
		if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
		{
			CKEDITOR.tools.enableHtml5Elements( document );
		}
		// The trick to keep the editor in the sample quite small
		// unless user specified own height.
		//CKEDITOR.config.height = 150;
		CKEDITOR.config.width = 'auto';
		CKEDITOR.config.contentsCss = ckedroot+'/custom/contents.css';
		CKEDITOR.plugins.basePath = ckedroot+'/custom/plugins/';
		var wysiwygareaAvailable = isWysiwygareaAvailable();
		return function()
		{
			//var editor; // uncomment for local scope
			editor =  document.getElementById( editor_el_name );
			/* Create classic or inline editor. */
			if ( wysiwygareaAvailable )
			{
				editor = CKEDITOR.replace( editor_el_name, custom_opts );
			}
			else
			{
				console.warn('CKEditor missing wysiwygarea plugin, using inline mode.');
				editor.setAttribute( 'contenteditable', 'true' );
				editor = CKEDITOR.inline( editor_el_name, custom_opts );
			}
			//isSomePlugin = !!CKEDITOR.plugins.get( 'plugin_name' );
			//if ( true ) { editorElement.setHtml('Sample text.');}
			//console.log('editor element=',editor);
		};
		function isWysiwygareaAvailable()
		{
			// If in development mode, then the wysiwygarea must be available.
			// Split REV into two strings so builder does not replace it :D.
			if ( CKEDITOR.revision == ( '%RE' + 'V%' ) )
			{
				return true;
			}
			return !!CKEDITOR.plugins.get( 'wysiwygarea' );
		}
	} )();
	startCKeditor();
			</script>
			<input type="submit" class="submit" value="<?php i18n(PLGID_CATALOG.'/ADD_CONTENT'); ?>" style="float:right;"/>
		</form>
		<div style="clear:both">&nbsp;</div>
	
