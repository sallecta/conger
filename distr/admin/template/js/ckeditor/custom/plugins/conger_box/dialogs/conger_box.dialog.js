plg.dlg={};
plg.el={};


plg.dlg.title=plg.lang.dlgTitle;

plg.dlg.minWidth=200;

plg.dlg.minHeight=100;

plg.dlg.contents = [];

plg.dlg.contents[0] = {};

plg.dlg.contents[0].id = 'info';

plg.dlg.contents[0].elements = [];

plg.el_align = 0;

plg.dlg.contents[0].elements[plg.el_align]={};

plg.dlg.contents[0].elements[plg.el_align].id = 'align';

plg.dlg.contents[0].elements[plg.el_align].type = 'select';

plg.dlg.contents[0].elements[plg.el_align].label = plg.lang.align;

plg.dlg.contents[0].elements[plg.el_align].items =
[
	[ editor.lang.common.notSet, '' ],
	[ editor.lang.common.alignLeft, 'left' ],
	[ editor.lang.common.alignRight, 'right' ],
	[ editor.lang.common.alignCenter, 'center' ]
]

plg.dlg.contents[0].elements[plg.el_align].setup = function( a_widget )
{
	this.setValue( a_widget.data.align );
};

plg.dlg.contents[0].elements[plg.el_align].commit = function( widget )
{
	widget.setData( 'align', this.getValue() );
}


plg.width_el = 1;

plg.dlg.contents[0].elements[plg.width_el]={};

plg.dlg.contents[0].elements[plg.width_el].id = 'width';

plg.dlg.contents[0].elements[plg.width_el].type = 'text';

plg.dlg.contents[0].elements[plg.width_el].label = plg.lang.width;

plg.dlg.contents[0].elements[plg.width_el].width = '50px';

plg.dlg.contents[0].elements[plg.width_el].setup = function( a_widget )
{
	this.setValue( a_widget.data.width );
};

plg.dlg.contents[0].elements[plg.width_el].commit = function( widget )
{
	widget.setData( 'width', this.getValue() );
}

plg.dlg_function = function( a_editor )
{
	var tmp = plg;
	delete plg; 
	return tmp.dlg;
}

CKEDITOR.dialog.add( 'conger_box', plg.dlg_function );

//CKEDITOR.dialog.add( 'conger_box', function( editor ) {
	//var lang = editor.lang.conger_box; return plg.dlg; });

//plg.dlg.contents.push='emm';
//CKEDITOR.dialog.add( 'conger_box', function( editor ) {
    //return {
        //title: 'Edit',
        //minWidth: 200,
        //minHeight: 100,
        //contents: [
            //{
                //id: 'info',
                //elements: [
                    //{
                        //id: 'align',
                        //type: 'select',
                        //label: 'Align',
                        //items: [
                            //[ editor.lang.common.notSet, '' ],
                            //[ editor.lang.common.alignLeft, 'left' ],
                            //[ editor.lang.common.alignRight, 'right' ],
                            //[ editor.lang.common.alignCenter, 'center' ]
                        //],
                        //setup: function( widget ) {
                            //this.setValue( widget.data.align );
                        //},
                        //commit: function( widget ) {
                            //widget.setData( 'align', this.getValue() );
                        //}
                    //},
                    //{
                        //id: 'width',
                        //type: 'text',
                        //label: 'Width',
                        //width: '50px',
                        //setup: function( widget ) {
                            //this.setValue( widget.data.width );
                        //},
                        //commit: function( widget ) {
                            //widget.setData( 'width', this.getValue() );
                        //}
                    //}
                //]
            //}
        //]
    //};
//} );

CKEDITOR.on('instanceReady', function(){ delete plg; }); 
