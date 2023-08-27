/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	
	// %REMOVE_START%
	// The configuration options below are needed when running CKEditor from source files.
	config.plugins = 'dialogui,dialog,about,a11yhelp,basicstyles,blockquote,notification,button,toolbar,clipboard,panel,floatpanel,menu,contextmenu,resize,elementspath,enterkey,entities,popup,filetools,filebrowser,floatingspace,listblock,richcombo,format,horizontalrule,htmlwriter,wysiwygarea,image,indent,indentlist,fakeobjects,link,list,magicline,maximize,pastetext,xml,ajax,pastetools,pastefromgdocs,pastefromlibreoffice,pastefromword,removeformat,showborders,sourcearea,specialchar,menubutton,scayt,stylescombo,tab,table,tabletools,tableselection,undo,lineutils,widgetselection,widget,notificationaggregator,uploadwidget,uploadimage,codesnippet';
	config.plugins = config.plugins+',btns,autogrow,showblocks';
	config.skin = 'conger';
	// %REMOVE_END%

	// Define changes to default configuration here.
	// For complete reference see:
	// https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html

	// The toolbar groups arrangement, optimized for two toolbar rows. All toolbars if commented,
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		'/',
		{ name: 'tools', groups: [ 'source', 'maximize', 'showblocks' ] },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup', 'btns' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	//config.removeButtons = 'Underline,Subscript,Superscript';

	// Set the most common block elements.
	//config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	//config.removeDialogTabs = 'image:advanced;link:advanced';
	


	/* from GetSimple */
	
	config.defaultLanguage             = 'en';
	config.resize_dir                  = 'vertical'; // vertical resize
	config.toolbarCanCollapse          = false;      // hide toolbar collapse button
	config.forcePasteAsPlainText       = true;
	config.tabSpaces                   = 10;    
	config.dialog_backgroundCoverColor = '#000000';  // color for dialog popups
	config.uiColor                     = '#FFFFFF';
	config.magicline_color             = '#CF3805'; 
	config.entities                    = false;    
	config.allowedContent              = true;       // disable acf
	config.disableAutoInline           = true;       // disable automatic inline editing of elements with contenteditable=true
	// customize file browser popup windows below
	// config.filebrowserWindowWidth      = '960';
	// config.filebrowserWindowHeight     = '700';
	//config.toolbar_advanced = 
	//[['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
	//'/',
	//['Styles','Format','Font','FontSize','CodeSnippet']];
	//config.toolbar_basic = 
	//[['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']];
	
	/*
	 * Configure Floating tools
	 */
	// config.floatingtools_basic = config.toolbar_basic; // copy our basic toolbar
	// config.floatingtools_Basic = [['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link']]; // define a custom same syntax as cke
	// config.floatingtools_advanced = config.toolbar_advanced; // copy our advanced toolbar
	// config.floatingtools = 'basic'; // pick which toolbar definition to use `floatingtools_TOOLBAR_ID`
	/*
	 * Configure autoGrow plugin
	 */
	 //config.extraPlugins = 'btns,autogrow,showblocks';
	 config.autoGrow_minHeight   = 150; 
	 config.autoGrow_maxHeight   = 2000;
	 config.autoGrow_bottomSpace = 50; 
	 config.autoParagraph = false;

	/*
	 * configure codesnippet plugin
	 */
	config.codeSnippet_theme = 'monokai_sublime';
	config.codeSnippet_languages = {
		javascript: 'JavaScript',
		php: 'PHP',
		html: 'HTML',
		css: 'CSS',
		C: 'C++',
		json: 'JSON',
		sql: 'SQL',
		xml: 'XML'
	};
	/*
	 * Remove plugin example
	 */
	// config.removePlugins = 'pluginid';
}; // CKEDITOR.editorConfig




// prevent removal of empty inline tags
CKEDITOR.dtd.$removeEmpty['i']    = false;
CKEDITOR.dtd.$removeEmpty['span'] = false;

// Disable some dialog fields we do not need
CKEDITOR.on( 'dialogDefinition', function( ev )
{
		var dialogName = ev.data.name;
		var dialogDefinition = ev.data.definition;
		ev.data.definition.resizable = CKEDITOR.DIALOG_RESIZE_NONE;
		if ( dialogName == 'link' )
		{
			var infoTab = dialogDefinition.getContents( 'info' );
			//dialogDefinition.removeContents( 'target' );
			var advTab = dialogDefinition.getContents( 'advanced' );
			advTab.remove( 'advLangDir' );
			advTab.remove( 'advLangCode' );
			advTab.remove( 'advContentType' );
			advTab.remove( 'advTitle' );
			advTab.remove( 'advCharset' );
		}
		if ( dialogName == 'image' )
		{
			var infoTab = dialogDefinition.getContents( 'info' );
			infoTab.remove( 'txtBorder' );
			infoTab.remove( 'txtHSpace' );
			infoTab.remove( 'txtVSpace' );
			infoTab.remove( 'btnResetSize' );
			dialogDefinition.removeContents( 'Link' );
			var advTab = dialogDefinition.getContents( 'advanced' );
			advTab.remove( 'cmbLangDir' );
			advTab.remove( 'txtLangCode' );
			advTab.remove( 'txtGenLongDescr' );
			advTab.remove( 'txtGenTitle' );
		}
});

// linkdefault = "url";

// if ajax list_pages_json then create menu items and call CKEsetupLinks
var menuItems;
$.getJSON(CKEDITOR.cpath_admin+"inc/ajax.php?list_pages_portable_json=1", 
function (a_data)
{
	menuItems = a_data;
	if (typeof editor == "undefined") 
	{ console.error("editor is undefined"); }
	else
	{
		//console.log(menuItems);
		CKEsetupLinks(editor);
	}
});

/**
 * CKEditor Add Local Page Link
 * This is used by the CKEditor to link to internal pages
 * @param a_editor	an editor instance
**/
CKEsetupLinks = function(a_editor)
{
	if (typeof a_editor === "undefined") { return; }
	CKEDITOR.on( 'dialogDefinition', function( a_ev )
	{
		// modify dialog definition for "link" dialog else return
		if ((a_ev.editor != a_editor) || (a_ev.data.name != 'link') || !menuItems)
		{
			return;
		}
		var definition = a_ev.data.definition;
		// override onfocus handler
		// Supposed to select the select box, not working
		definition.onFocus = CKEDITOR.tools.override(definition.onFocus, function(original)
		{
			return function()
			{
				original.call(this);
				//console.log(['this',this]);
				if (this.getValueOf('info', 'linkType') == 'localPage')
				{
					// this.getContentElement('info', 'localPage_path').select(); // disabled, object has no method select
				}
			};
		});
		// Add localpage to linktypes
		var infoTab = definition.getContents('info');
		var content = CKEgetById(infoTab.elements, 'linkType');
		content.items.unshift(['Link to local page', 'localPage']);
		content['default'] = 'localPage';
		infoTab.elements.push
		(
		{
			type: 'vbox',
			id: 'localPageOptions',
			children:
			[{
				type: 'select',
				id: 'localPage_path',
				label: 'Select page:',
				required: true,
				items: menuItems,
				setup: function(data) 
				{
					if ( data.localPage )
					{ this.setValue( data.localPage ); }
				}
			}]
		}
		);
		// hide and show tabs and stuff as type is changed
		content.onChange = CKEDITOR.tools.override(content.onChange, function(original)
		{
			return function()
			{
				original.call(this);
				var dialog = this.getDialog();
				var element = dialog.getContentElement('info', 'localPageOptions').getElement().getParent().getParent();
				if (this.getValue() == 'localPage')
				{
					//console.log(['this.getValue() == localPagedialog',this]);
					element.show();
					if (a_editor.config.linkShowTargetTab)
					{
						dialog.showPage('target');
					}
					var uploadTab = dialog.definition.getContents('upload');
					if (uploadTab && !uploadTab.hidden)
					{
						dialog.hidePage('upload');
					}
				}
				else
				{
					//console.log(['not localPage',this]);
					element.hide();
				}
			};
		});
		content.setup = function(data)
		{
			//console.log(['content.setup data.type',data.type]);
			// if no url, set selection to localpage
			if (!data.type || (data.type == 'url') && !data.url)
			{
				data.type = 'localPage'; // default to localPage
				if(typeof(linkdefault) !== 'undefined')
				{ data.type = linkdefault; }
				//console.log(['empty',data]);
			}
			else if (data.url && !data.url.protocol && data.url.url)
			{
				path = this.path;
			// already a link
				////if (path)
				////{
					////// what is path, this seems to do nothing
					////console.log(path);
					////data.type = 'localPage';
					////data.localPage_path = path;
					////delete data.url;
				////}
			}
			this.setValue(data.type);
		};
		content.commit = function(data)
		{
			data.type = this.getValue();
			if (data.type == 'localPage')
			{
				data.type = 'url';
				var dialog = this.getDialog();
				dialog.setValueOf('info', 'protocol', '');
				dialog.setValueOf('info', 'url', dialog.getValueOf('info', 'localPage_path'));
			}
		};
	},null,null,1); 
} // CKEsetupLinks


// Helper function to get a CKEDITOR.dialog.contentDefinition object by its ID.
CKEgetById = function(array, id, recurse) {
	for (var i = 0, item; (item = array[i]); i++) {
		if (item.id == id) return item;
			if (recurse && item[recurse]) {
				var retval = CKEgetById(item[recurse], id, recurse);
				if (retval) return retval;
			}
	}
	return null;
};

var getById = CKEgetById; // alias for legacy

// Fix for IE onbeforeunload bubbling up from dialogs
CKEDITOR.on('instanceReady', function(event) {
  event.editor.on('dialogShow', function(dialogShowEvent) {
	if(CKEDITOR.env.ie) {
	  $(dialogShowEvent.data._.element.$).find('a[href*="void(0)"]').removeAttr('href');
	}
  });
});



