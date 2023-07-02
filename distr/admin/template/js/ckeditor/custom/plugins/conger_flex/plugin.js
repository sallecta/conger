plg = {};

plg.name = 'conger_box';

plg.obj = {};

plg.obj.requires='widget';

plg.obj.icons=plg.name;

plg.obj.icons=plg.name;

plg.widgets = {};

plg.widgets.button = 'Create a box';

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
{
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
	plg.path=this.path;
	CKEDITOR.dialog.add( plg.name, plg.path + 'dialogs/'+plg.name+'.dialog.js' );
	a_editor.widgets.add( plg.name, plg.widgets );
}; // plg.obj.init

CKEDITOR.plugins.add( plg.name, plg.obj);
CKEDITOR.on('instanceReady', function(){ delete plg; }); 
