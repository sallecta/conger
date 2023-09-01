/* 
Btns plugin
https://github.com/sallecta/ckeditor_btns
 */
"use strict";
new function()
{
	const app =  Object.create(null);
	app.name = 'btns';
	app.version = '1.0.10';
	app.orig = -1;
	app.disabled = CKEDITOR.TRISTATE_DISABLED;//0
	app.on = CKEDITOR.TRISTATE_ON;//1
	app.off = CKEDITOR.TRISTATE_OFF//2
	app.fn_defs = function(a_defs, a_fn)
	{
		a_fn.defs=a_defs; 
		return a_fn.bind(a_fn);
	}
	app.is_array = function(a_var)
	{
		if ( !a_var ) {return false;}
		return a_var.constructor === Array;
	}
	app.is_object = function(a_var)
	{
		if ( !a_var ) {return false;}
		if ( a_var.constructor === undefined && typeof a_var === 'object' )
		{ this.result = true; }
		else if ( a_var.constructor === Object )
		{ this.result = true; }
		else
		{ this.result = false; }
		return this.result;
	}
	app.in_array = function(a_val, a_array)
	{
		// ['BODY', 'HTML'].indexOf(el.nodeName) >= 0
		if ( !a_val ) {return false;}
		if ( !a_array ) {return false;}
		if ( !a_array.constructor === Array ) {return false;}
		if ( a_array.indexOf(a_val) >= 0 ) { return true; }
		return false;
	}
	
	app.retag = function( a_el, a_tag, a_editor )
	{
		var elnew = a_editor.document.createElement(a_tag);
		const content = a_el.innerHTML;
		elnew.$.innerHTML = content;
		a_el.parentNode.replaceChild(elnew.$, a_el);
	};
	app.retag = app.retag.bind({defs:{name:app.name+'.retag'}});
	
	app.cmd_last=0;
	
	app.pnested = Object.create(null); /*v1.1*/
	app.pnested.set = function (a_walk, a_obj_ref, a_val=null )
	{ /* creates nested property from array */
		//noconsole.me='pnested.set';
		var cnt=0;
		var limit = 100; /* max recursion limit, or max prop nesting */
		function recurse(a_obj)
		{
			//noconsole.me2='--'.repeat(cnt*2)+'pnested.set>recurse';
			var key = cnt;
			cnt = cnt+1;
			if (cnt>=limit)
			{
				//noconsole.log(//noconsole.me2,'max rec');
				return false;
			}
			var prop = a_walk[key];
			//noconsole.log(//noconsole.me2,[prop]);
	
			if (key!==last_key)
			{
				if ( typeof a_obj[prop] !== 'object' )
				{
					//noconsole.log(//noconsole.me2,[prop,'creating missing a_obj['+prop+']', JSON.stringify(a_obj[prop])]);
					//a_obj[prop] = {};
					a_obj[prop] = Object.create(null);;
				}
				//noconsole.log(//noconsole.me2,[prop,'updated a_obj_ref=',JSON.stringify(a_obj_ref)]);
				var subobj = a_obj[prop];
				return recurse(subobj);
			}
	
			if (key===last_key)
			{
				if ( a_val )
				{
					a_obj[prop] = a_val;
				}
				if ( !a_val )
				{
					if ( typeof a_obj[prop] !== 'object' )
					{
						//noconsole.log(//noconsole.me2,[prop,'creating missing a_obj['+prop+']', JSON.stringify(a_obj[prop])]);
						//a_obj[prop] = {};
						a_obj[prop] = Object.create(null);;
					}
					//noconsole.log(//noconsole.me2,[prop,'updated a_obj_ref=',JSON.stringify(a_obj_ref)]);
				}
				//noconsole.log(//noconsole.me2,['done, a_obj_ref=',JSON.stringify(a_obj_ref)]); 
				return a_obj_ref;
			}
		} // recurse
		if ( a_walk.constructor !== Array ) { return null };
		if ( a_obj_ref.constructor !== Object ) { return null };
		//noconsole.log(//noconsole.me,['a_walk.constructor=', a_walk.constructor]);
		//noconsole.log(//noconsole.me,['a_obj_ref.constructor=', a_obj_ref.constructor]);
		var last_key = a_walk.length-1;
		const out= recurse(a_obj_ref);
		//noconsole.log(//noconsole.me,['done']);
		return out;
	} // pnested.set
	
	
	app.create_cmd_struct= function(a_name,a_obj_ref)
	{
		app.pnested.set([a_name,'cmd'],a_obj_ref);
		app.pnested.set([a_name,'btn'],a_obj_ref);
		app.pnested.set([a_name,'evts'],a_obj_ref);
	}
	app.cmd_switch= function (a_cmd)
	{
		if (app.cmd_last===0)
		{
			app.cmd_last=a_cmd;
		};
		if (app.cmd_last !== a_cmd)
		{
			app.cmd_last.setState( CKEDITOR.TRISTATE_OFF );
		};
		app.cmd_last=a_cmd;
	}
	app.cmd_switch = app.cmd_switch.bind({defs:{name:app.name+'.cmd_switch'}});
	
	app.sel_get = function( a_editor )
	{
		const inst = a_editor[app.name];
		if (!inst.sel)
		{
			inst.sel={};
		}
		inst.sel.ck = a_editor.getSelection();
		if (!inst.sel.ck)
		{
			//noconsole.log(this.defs.name,'no a_sel_ck',a_sel_ck);
			inst.sel = null;
			return;
		}
		inst.sel.nat = inst.sel.ck.getNative();
		if (!inst.sel.nat)
		{
			console.log(this.defs.name,'inst.sel.nat',inst.sel.nat, 'from inst.sel.ck',inst.sel.ck);
			inst.sel = null;
			return;
		}
		inst.sel.start = inst.sel.nat.anchorNode;
		inst.sel.end = inst.sel.nat.focusNode;
		if (!inst.sel.start || !inst.sel.end)
		{
			//noconsole.log(this.defs.name,'bad sel',inst.sel);
			inst.sel = null;
			return;
		}
		inst.sel.el = inst.sel.end.parentElement;
		if (!inst.sel.el)
		{
			//noconsole.log(this.defs.name,'no inst.sel.el',inst.sel.el);
			inst.sel = null;
			return;
		}
		if (!inst.sel.el.parentNode)
		{
			//noconsole.log(this.defs.name,'no inst.sel.el.parentNode',inst.sel.el.parentNode);
			inst.sel = null;
			return;
		}
		//noconsole.log(this.defs.name,'got valid inst.sel',inst.sel);
	}
	app.sel_get = app.sel_get.bind({defs:{name:app.name+'.sel_upd'}});
	
	app.no_auto_p = function( a_editor, a_cmd )
	{
		if ( !a_editor.commands[a_cmd.name] ) { return; }
		const cfgval = a_editor.config.autoParagraph;
		if ( cfgval === undefined || cfgval === true )
		{
			const msg = 'Sorry, your autoParagraph '+
				'config has just been set to false, '+
				'to let '+a_cmd.name+' work correctly.'+"\n"+
				'Enabling autoParagraph back will wrap in <p> tag '+
				'all free text nodes.';
			console.warn(this.defs.name,msg);
			a_editor.config.autoParagraph = false;
		}
	}
	app.no_auto_p = app.no_auto_p.bind({defs:{name:app.name+'.no_auto_p'}});
	
	
	app.cmd_on = function(a_cmd,a_editor)
	{
		if ( a_cmd )
		{
			this.cmd = a_cmd;
		}
		else
		{
			this.cmd = inst.cmd_last;
		}
		if ( !app.is_object(this.cmd) )
		{
			return;
		}
		this.cmd.setState( CKEDITOR.TRISTATE_ON )
	}
	app.cmd_on = app.cmd_on.bind({defs:{name:app.name+'.cmd_on'}});
	
	app.cmd_off = function(a_cmd, a_ed)
	{
		if ( a_cmd ) { this.cmd = a_cmd; }
		else { this.cmd = app.cmd_last; }
		if ( !app.is_object(this.cmd) ) { return; }
		this.cmd.setState( CKEDITOR.TRISTATE_OFF )
	}
	app.cmd_off = app.cmd_off.bind({defs:{name:app.name+'.cmd_off'}});
	
	app.cmd_disabled = function(a_cmd=null)
	{
		if ( a_cmd ) { this.cmd = a_cmd; }
		else { this.cmd = app.cmd_last; }
		if ( !app.is_object(this.cmd) ) { return; }
		this.cmd.setState( CKEDITOR.TRISTATE_DISABLED )
	}
	app.cmd_disabled = app.cmd_disabled.bind({defs:{name:app.name+'.cmd_disabled'}});
	
	/* cmds */
	app.cmds = {};
	const cmds = app.cmds;
	
	/*
	 cmd txt
	*/
	app.create_cmd_struct('txt',cmds);
	cmds.txt.btn.toolbar='basicstyles',
	cmds.txt.cmd.exec = function(a_editor)
	{
		app.no_auto_p( a_editor, this );
		const inst = a_editor[app.name];
		app.cmd_switch(this);
		if ( !inst.sel )
		{
			inst.sel_get(a_editor);
		}
		if (!inst.sel)
		{
			//noconsole.log(this.defs.name,'no sel',inst.sel);
			return;
		}
		//noconsole.log(this.defs.name,'sel is',inst.sel);
		if ( app.in_array(inst.sel.el.localName,['body','html']) )
		{
			//noconsole.log(this.defs.name,'bad tag to replace:',app.sel.el.localName);
			app.cmd_on(this,a_editor);
			return;
		}
		//
		const content = inst.sel.el.innerText;
		const text_node = document.createTextNode(content);
		inst.sel.el.parentNode.replaceChild(text_node, inst.sel.el);
		//console.nolog(this.defs.name,'text_node',text_node );
		inst.sel=null;
		app.sel_get(a_editor);
		
		inst.sel.nat.modify("move", "forward", "character");
		inst.sel.nat.modify("move", "forward", "lineboundary");
		inst.sel.nat.modify("move", "backward", "character");
		
		inst.cmd_last=this;
		app.cmd_on(this,a_editor);
		inst.sel=null;
		
	};
	// cmd may have own event functions
	//cmds.txt.evts.ev1={};
	//cmds.txt.evts.ev1.fn=function(a_event)...
	//cmds.txt.evts.ev1.on=['selectionChange',...];
	/* txt end */
	
	/*
	cmd h1
	*/
	app.create_cmd_struct('h1',cmds);
	const h1 = app.cmds.h1;
	h1.recall = ['h2','h3','h4','h5','h6','p'];
	cmds.h1.btn.toolbar='basicstyles',
	cmds.h1.cmd.exec = function(a_editor)
	{
		const inst = a_editor[app.name];
		app.cmd_switch(this);
		if ( !inst.sel )
		{
			app.sel_get(a_editor);
		}
		if (!inst.sel)
		{
			console.log(this.defs.name,'no sel',inst.sel);
			return;
		}
		var newtag=null;
		if ( inst.cmd_last.tag_old === undefined )
		{
			//console.nolog('inst.cmd_last.tag_old',inst.cmd_last.tag_old,'inst.sel.el.localName',inst.sel.el.localName,'this.defs.key',this.defs.key);
			if ( inst.sel.el.localName === this.defs.key )
			{
				//inst.cmd_last.tag_old = 'p';
				return;
			}
			else
			{
				inst.cmd_last.tag_old = inst.sel.el.localName;
			}
		}
		if (inst.sel.el.localName === this.defs.key)
		{
			newtag = inst.cmd_last.tag_old;
			var oldtag = true;
		}
		else if (inst.sel.el.localName !== this.defs.key)
		{
			newtag = this.defs.key;
		}
		
		var range = inst.sel.nat.getRangeAt(0);
		var range_str = range.toString();
		if ( app.in_array(inst.sel.el.localName,['body','html']) )
		{
			if ( !range_str )
			{
				//noconsole.log(this.defs.name,'bad tag to replace:',inst.sel.el.localName);
				return;
			}
		}
		if ( range_str )
		{
			//noconsole.log(this.defs.name,'going to tag',range_str, 'with', newtag);
			range.deleteContents();
			const newel = document.createElement(newtag);
			newel.innerHTML =  range_str;
			range.insertNode(newel);
			inst.sel.nat.modify("move", "forward", "lineboundary");
		}
		else
		{
			//noconsole.log(this.defs.name,'going to replace',app.sel.el.localName, 'with', newtag);
			app.retag( inst.sel.el, newtag, a_editor );
			inst.sel.nat.modify("move", "forward", "character");
			inst.sel.nat.modify("move", "forward", "lineboundary");
		}
		if ( oldtag )
		{
			//noconsole.log(this.defs.name,'newtag === app.cmd_last.tag_old',this);
			app.cmd_off(this,a_editor);
		}
		else
		{
			app.cmd_on(this,a_editor);
		}
		app.evts.ev1.fn(a_editor);
		inst.cmd_last=this;
		inst.sel=null;
	};
	/* h1 end */
	/* cmds end */
	
	/* app events */
	app.evts = {};
	const evts = app.evts;
	/* evt1 */
	evts.ev1={};
	evts.ev1.fn=function(a_in)
	{
		let ed;
		if ( !a_in )
		{
			console.warn(this.defs.name,'no a_in',a_in);
			return;
		}
		if ( a_in.editor )
		{
			ed=a_in.editor;
		}
		else
		{
			ed=a_in;
		}
		//console.nolog(this.defs.name,'ed',ed);
		//noconsole.log(this.defs.name,'this.defs',this.defs);
		app.sel_get(ed);
		const inst = ed[app.name];
		if ( !inst.sel )
		{
			console.warn(this.defs.name, 'no app.sel',inst.sel,'inst',inst);
			return;
		}
		//noconsole.log(this.defs.name,'new sel is',app.sel);
		for ( var cmd_key in app.cmds )
		{
			const cmd_name = app.cmds[cmd_key].cmd.defs.name;
			this.cmd_curr = ed.commands[cmd_name];
			if( cmd_key === inst.sel.el.localName)
			{
				this.found = this.cmd_curr;
			}
			if ( this.cmd_curr.state !== app.off )
			{
				//noconsole.log(this.defs.name,'set to off',this.cmd_curr);
				app.cmd_off(this.cmd_curr,ed);
			}
		}
		if ( this.found )
		{
			//noconsole.log(this.defs.name,'this.found is',this.found);
			app.cmd_on(this.found,ed);
			inst.cmd_last = this.found;
			this.found = null;
		}
		if ( app.in_array(inst.sel.el.localName,['body','html']) )
		{
			const cmd_name = app.cmds.txt.cmd.defs.name;
			app.cmd_on(ed.commands[cmd_name],ed);
			inst.cmd_last = ed.commands[cmd_name];
		}
	};
	evts.ev1.on=['selectionChange','change'];
	/* evt1 end */
	/* app events end*/
	
	app.cmd_build={};
	app.cmd_build.limit = 20;/* recursion limit */
	app.cmd_build.cnt = 0;
	app.cmd_build.reacaller = null;
	app.cmd_build.fn= function(a_cmd_key,a_cmd,a_editor)
	{
		const cmd_obj = a_cmd;
		const cmd_name = app.name+'_'+a_cmd_key;
		/* button */
		cmd_obj.btn.name = cmd_name;
		cmd_obj.btn.icon = cmd_name;
		cmd_obj.btn.command = cmd_name;
		cmd_obj.btn.label = a_editor.lang[app.name][a_cmd_key];
		cmd_obj.btn.toolbar = 'basicstyles,100';
		//noconsole.log('-'.repeat(6),this.defs.name,a_cmd_key,'creating btn', cmd_obj.btn);
		a_editor.ui.addButton(cmd_obj.btn.name, cmd_obj.btn);
		/* command */
		cmd_obj.cmd.defs={};
		cmd_obj.cmd.defs.name = cmd_name;
		cmd_obj.cmd.defs.key = a_cmd_key;
		//noconsole.log('-'.repeat(6),this.defs.name,a_cmd_key,'creating cmd', cmd_obj.cmd);
		a_editor.addCommand(cmd_name, cmd_obj.cmd);
		/* events */
		for ( var evt_key in cmd_obj.evts )
		{
			const evt = cmd_obj.evts[evt_key];
			evt.defs={};
			evt.defs.name= cmd_name+'_evts_'+evt_key;
			//noconsole.log('-'.repeat(4),this.defs.name,a_cmd_key,'parsing evt', evt.defs.name,evt);
			evt.fn=evt.fn.bind(evt);
			for ( var key in evt.on )
			{
				var on_evt = evt.on[key];
				//noconsole.log('-'.repeat(6),this.defs.name,a_cmd_key,'a_editor.on',evt.fn);
				a_editor.on(on_evt, evt.fn);
			}
		}
		//noconsole.log('-'.repeat(4),this.defs.name,'END building cmd',a_cmd_key,cmd_obj);
		if ( app.is_array(a_cmd.recall) && a_cmd.recall.length>0 )
		{
			app.cmd_build.cnt = app.cmd_build.cnt + 1;
			if ( app.cmd_build.cnt >= app.cmd_build.limit )
			{
				//noconsole.log('-'.repeat(4), this.defs.name,'too much recursion',app.cmd_build.cnt,a_cmd_key);
				return;
			}
			const new_cmd_key = a_cmd.recall.shift(); // return first and delete from src
			app.create_cmd_struct(new_cmd_key,app.cmds);
			const new_obj = app.cmds[new_cmd_key];
			//Object.assign(new_obj, a_cmd);
			Object.assign(new_obj.btn,a_cmd.btn);
			Object.assign(new_obj.cmd,a_cmd.cmd);
			Object.assign(new_obj.evts,a_cmd.evts);
			new_obj.recall=a_cmd.recall;
			//noconsole.log('-'.repeat(4),this.defs.name,'recall self with',new_cmd_key,new_obj);
			app.cmd_build.fn(new_cmd_key,new_obj,a_editor);
		}
	}; // app.cmd_build
	app.cmd_build.fn = app.cmd_build.fn.bind({defs:{name:app.name+'.cmd_build'}});
	
	//
	//app.plg_ready=false;
	app.plg=Object.create(null);
	app.plg.lang='en,ru';
	app.plg.hidpi=true;
	app.plg_icons = function()
	{
		app.plg.icons = [];
		for ( var cmd_key in app.cmds )
		{
			const cmd = app.cmds[cmd_key];
			const icon = app.name+'_'+cmd_key;
			app.plg.icons.push(icon);
			if ( app.is_array(cmd.recall) )
			{
				for ( var key in cmd.recall )
				{
					const icon = app.name+'_'+cmd.recall[key];
					app.plg.icons.push(icon);
				}
			}
		}
		app.plg.icons = app.plg.icons.join();
		//no//noconsole.log(this.defs.name,'> done, app.plg.icons =',app.plg.icons);
	}
	app.plg_icons=app.fn_defs({name:app.name+'.plg_icons'}, app.plg_icons);
	
	/* init plugin function */
	app.plg.defs = {name:app.name+'.plg',version:app.version}
	app.plg.init = function( a_editor )
	{
		//if ( app.plg_ready ) { return; }
		
		a_editor = a_editor;
		a_editor[app.name]={};
		
		//noconsole.log('-'.repeat(1), this.defs.name,'app.cmd_build.fn');
		for ( var cmd_key in app.cmds )
		{
			app.cmd_build.fn(cmd_key,app.cmds[cmd_key],a_editor);
		}
		//noconsole.log('-'.repeat(1), this.defs.name,'END app.cmd_build.fn');
		for ( var evt_key in app.evts )
		{
			const evt = app.evts[evt_key];
			evt.defs={};
			evt.defs.name= app.name+'_evts_'+evt_key;
			//noconsole.log('-'.repeat(1),this.defs.name,'parsing evt', evt.defs.name,evt);
			evt.fn=evt.fn.bind(evt);
			for ( var key in evt.on )
			{
				var on_evt = evt.on[key];
				//noconsole.log('-'.repeat(2),this.defs.name,evt.defs.name,'on',on_evt,evt.fn);
				a_editor.on(on_evt, evt.fn);
			}
		}
		//noconsole.log('-'.repeat(1), this.defs.name,'builded cmds',app.cmds)
		a_editor.filter.allow( 'p h1 h2 h3 h4 h5 h6 br em a' );
		//noconsole.log( this.defs.name, 'a_editor.filter.allowedContent', a_editor.filter.allowedContent );
		//app.plg_ready = true;
	} // btns.init
	
	app.plg_icons();
	CKEDITOR.plugins.add(app.name, app.plg);
}
