plg.dlg={};
plg.el={};

plg.dlg.title='Edit';

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

plg.dlg.contents[0].elements[plg.el_align].label = 'Align';

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




plg.el_width = 1;

plg.dlg.contents[0].elements[1]={};
plg.dlg.contents[0].elements[1]={};
plg.dlg.contents[0].elements[1].id = 'align';
plg.dlg.contents[0].elements[1].type = 'select';
plg.dlg.contents[0].elements[1].label = 'Align';
plg.dlg.contents[0].elements[1].items =
[
	[ editor.lang.common.notSet, '' ],
	[ editor.lang.common.alignLeft, 'left' ],
	[ editor.lang.common.alignRight, 'right' ],
	[ editor.lang.common.alignCenter, 'center' ]
];

//plg.dlg.contents.push='emm';
CKEDITOR.dialog.add( 'conger_box', function( editor ) {
    return {
        title: 'Edit',
        minWidth: 200,
        minHeight: 100,
        contents: [
            {
                id: 'info',
                elements: [
                    {
                        id: 'align',
                        type: 'select',
                        label: 'Align',
                        items: [
                            [ editor.lang.common.notSet, '' ],
                            [ editor.lang.common.alignLeft, 'left' ],
                            [ editor.lang.common.alignRight, 'right' ],
                            [ editor.lang.common.alignCenter, 'center' ]
                        ],
                        setup: function( widget ) {
                            this.setValue( widget.data.align );
                        },
                        commit: function( widget ) {
                            widget.setData( 'align', this.getValue() );
                        }
                    },
                    {
                        id: 'width',
                        type: 'text',
                        label: 'Width',
                        width: '50px',
                        setup: function( widget ) {
                            this.setValue( widget.data.width );
                        },
                        commit: function( widget ) {
                            widget.setData( 'width', this.getValue() );
                        }
                    }
                ]
            }
        ]
    };
} );


//CKEDITOR.on('instanceReady', function(){ delete plg; }); 
