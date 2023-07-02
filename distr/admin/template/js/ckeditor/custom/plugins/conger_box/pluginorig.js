plg = {};
plg.name = 'conger_box';
plg.obj ={};
plg.obj.requires='widget';
plg.obj.icons=plg.name;
plg.obj.icons=plg.name;
plg.obj.init= function( editor ){};
CKEDITOR.plugins.add( plg.name,
{
	requires: 'widget',
	
	icons: 'conger_box',
	
	init: function( editor )
	{
		CKEDITOR.dialog.add( 'conger_box', this.path + 'dialogs/conger_box.js' );
		editor.widgets.add( 'conger_box',
		{
			button: 'Create a box',
			template:
			'<div class="conger_box">' +
			'<h2 class="conger_box-title">Title</h2>' +
			'<div class="conger_box-content"><p>Content...</p></div>' +
			'</div>',
			editables:
			{
				title:
				{
					selector: '.conger_box-title',
					allowedContent: 'br strong em'
				},
				content:
				{
					selector: '.conger_box-content',
					allowedContent: 'p br ul ol li strong em'
				}
			},
			allowedContent:
				'div(!conger_box,align-left,align-right,align-center){width};' +
				'div(!conger_box-content); h2(!conger_box-title)',
			requiredContent: 'div(conger_box)',
			dialog: 'conger_box',
			upcast: function( element )
			{
			return element.name == 'div' && element.hasClass( 'conger_box' );
			},
			init: function() {
			var width = this.element.getStyle( 'width' );
			if ( width )
			this.setData( 'width', width );
			if ( this.element.hasClass( 'align-left' ) )
			this.setData( 'align', 'left' );
			if ( this.element.hasClass( 'align-right' ) )
			this.setData( 'align', 'right' );
			if ( this.element.hasClass( 'align-center' ) )
			this.setData( 'align', 'center' );
			},
			
			data: function() {
			
			if ( this.data.width == '' )
			this.element.removeStyle( 'width' );
			else
			this.element.setStyle( 'width', this.data.width );
			
			this.element.removeClass( 'align-left' );
			this.element.removeClass( 'align-right' );
			this.element.removeClass( 'align-center' );
			if ( this.data.align )
			this.element.addClass( 'align-' + this.data.align );
			}
		} );
	}
} );
delete plg;
