<?php if(!defined('APP')){ die('you cannot load this page directly.'); }

	echo "
	// modify existing Link dialog
	CKEDITOR.on( 'dialogDefinition', function( ev )	{
		if ((ev.editor != " . $a_editor . ") || (ev.data.name != 'link')) return;

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
				items: " . get_pages() . ",
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
					if (" . $a_editor . ".config.linkShowTargetTab) {
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
