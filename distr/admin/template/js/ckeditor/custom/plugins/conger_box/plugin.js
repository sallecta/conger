plg = {};

plg.name = 'conger_box';

plg.lang = {};

plg.obj = {};

plg.obj.requires='widget';

plg.obj.lang = 'en,ru';

plg.obj.icons=plg.name,'emm';

plg.widgets = {};

//plg.widgets.button = 'Create a box';

plg.widgets.template =
	'<div class="'+plg.name+'">' +
		'<h2 class="'+plg.name+'-title">Title</h2>' +
		'<div class="'+plg.name+'-content"><p>Content...</p></div>' +
	'</div>';

plg.widgets.editables = {};

plg.widgets.editables.title = {};

plg.widgets.editables.title.selector = '.'+plg.name+'-title';

plg.widgets.editables.title.allowedContent = 'br strong em';

plg.widgets.editables.content = {};

plg.widgets.editables.content.selector = '.'+plg.name+'-content';

plg.widgets.editables.content.allowedContent = 'p br ul ol li strong em';

plg.widgets.allowedContent =
	'div(!'+plg.name+',align-left,align-right,align-center){width};' +
	'div(!'+plg.name+'-content); h2(!'+plg.name+'-title)',

plg.widgets.requiredContent = 'div('+plg.name+')',

plg.widgets.dialog = plg.name;

plg.widgets.upcast = function( element )
{// Defines which elements will become widgets.
	return element.name == 'div' && element.hasClass( plg.name );
};

plg.widgets.init = function()
{
	var width = this.element.getStyle( 'width' );
	if ( width )
	{
		this.setData( 'width', width );
	}
	if ( this.element.hasClass( 'align-left' ) )
	{
		this.setData( 'align', 'left' );
	}
	if ( this.element.hasClass( 'align-right' ) )
	{
		this.setData( 'align', 'right' );
	}
	if ( this.element.hasClass( 'align-center' ) )
	{
		this.setData( 'align', 'center' );
	}
}; // plg.widgets.init

plg.widgets.data = function()
{
	if ( this.data.width == '' )
	{
		this.element.removeStyle( 'width' );
	}
	else
	{
		this.element.setStyle( 'width', this.data.width );
	}
	this.element.removeClass( 'align-left' );
	this.element.removeClass( 'align-right' );
	this.element.removeClass( 'align-center' );
	if ( this.data.align )
	{
		this.element.addClass( 'align-' + this.data.align );
	}
}; // plg.widgets.data


plg.obj.init = function( a_editor )
{
	plg.lang = a_editor.lang.conger_box;
	plg.path=this.path;
	CKEDITOR.dialog.add( plg.name, plg.path + 'dialogs/'+plg.name+'.dialog.js' );
	a_editor.widgets.add( plg.name, plg.widgets );
	
	plg.btns = {};
	plg.btns.main = {};
	plg.btns.main.label = plg.lang.dlgTitle;
	plg.btns.main.command = plg.name;
	plg.btns.main.isToggle = true;
	plg.btns.main.icon = plg.path+'icons/'+plg.name+'.png';
	plg.btns.main.toolbar = 'styles';
	
	plg.btns.selector = {};
	plg.btns.selector.label = "Classes";
	plg.btns.selector.title = plg.name;
	plg.btns.selector.multiSelect = false;
	plg.btns.selector.toolbar = 'styles';
	plg.btns.selector.panel = {};
	plg.btns.selector.panel.css = [ CKEDITOR.skin.getPath( 'editor' ) ].concat( a_editor.config.contentsCss );
	plg.btns.selector.init = function()
	{
		var list = ['flex 1', 'flex 2', 'flex 3'];
		var item;
		this.startGroup( this.label );
		// Loop over the Array, adding all items to the
		// combo.
		this.add( 'exit', 'exit', 'exit' );
		for ( ndx = 0 ; ndx < list.length ; ndx++ )
		{
			item = list[ ndx ];
			// value, html, text
			this.add( item, item, item);
		}
		// Default value on first click
		//this.setValue('flex 1', "flex 1");
	};

	a_editor.ui.addRichCombo( plg.name+'_2', plg.btns.selector);
	a_editor.ui.addButton(plg.name+'_1', plg.btns.main);
	
}; // plg.obj.init


//todo 
// editor.on( 'contentDom', function( )
CKEDITOR.plugins.add( plg.name, plg.obj);
CKEDITOR.on('instanceReady', function(){ delete plg.obj; delete plg.widgets; delete plg.btns; }); 
