"use strict";
new function()
{
	const mdl={};
	mdl.key='core';
	mdl.name=client_ui.name+'.'+mdl.key;
	/**/
	
	mdl.defs = {};
	mdl.defs.cl_vis='vis_op1';
	mdl.defs.cl_dspl_blck='dspl_blck';
	mdl.defs.cl_overflow_hide='client_ui_overflow_hidden';
	Object.freeze(mdl.defs);
	
	mdl.first_up= function(a_str)
	{
		return a_str.charAt(0).toUpperCase() + a_str.slice(1);
	}
	
	mdl.basename = function(a_path,a_suffix=null)
	{
		const me=mdl.name+'.basename';
		let path_len = a_path.length;
		let out;
		for (let ndx = path_len-1; ndx > -1; ndx--)
		{
			if ( a_path.charAt(ndx) === '/')
			{
				const next_ndx = ndx + 1;
				if ( next_ndx >= path_len )
				{
					//console.log(me,'next_ndx >= path_len',next_ndx,path_len);
					out='';
					break;
				}
				out = a_path.slice(next_ndx)
				//console.log(me,'out=',out);
				if ( a_suffix )
				{
					const suffix_ndx = out.length-a_suffix.length;
					if ( suffix_ndx > -1 )
					{
						const without_sfx = out.slice(0,suffix_ndx);
						//console.log(me,'without_sfx=',without_sfx);
						out = without_sfx;
						break;
					}
				}
				break;
			}
		}
		if ( out === undefined ) { out = a_path; }
		if ( a_suffix )
		{
			const suffix_ndx = out.length-a_suffix.length;
			if ( suffix_ndx > -1 )
			{
				const without_sfx = out.slice(0,suffix_ndx);
				//console.log(me,'without_sfx=',without_sfx);
				out = without_sfx;
			}
		}
		return out;
	} // mdl.basename
	
	mdl.dirname = function( a_path,a_levels=1 )
	{
		const me=mdl.name+'.dirname';
		let path_len = a_path.length;
		let out;
		let level=1;
		for (let ndx = path_len-1; ndx > -1; ndx--)
		{
			if ( a_path.charAt(ndx-1) === '/')
			{
				if ( level === a_levels )
				{
					//console.log('level === a_levels',level,a_levels);
					out = a_path.slice(0,ndx-1);
					break;
				}
				//console.log('level != a_levels',level,a_levels);
				level = level + 1;
			}
		}
		return out;
	} // mdl.dirname
	mdl.is_object = function(a_var)
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
	mdl.wdgt={};
	mdl.wdgt.els={};
	mdl.wdgt.els.spinner={};
	mdl.wdgt.els.spinner.create = function(a_cfg)
	{
		const el = document.createElement('div');
		el.classList.add('wdgt_spinner');
		return el;
	}
	
	
	mdl.wdgt.els.overflow={};
	const wdgt_overflow = mdl.wdgt.els.overflow;
	wdgt_overflow.events_c={};
	wdgt_overflow.events={};
	
	wdgt_overflow.ev_c_create = function(a_name)
	{
		const me = mdl.name+'.wdgt.els.overflow.ev_c_create';
		const events_c = wdgt_overflow.events_c;
		events_c[a_name] = document.createEvent("Event");
		events_c[a_name].initEvent(a_name, true, true);
		//console.log(me, a_name, 'created',events_c[a_name]);
	};
	
	wdgt_overflow.ev_add = function(a_name, a_type, a_fn, a_el=null)
	{
		const me = mdl.name+'.wdgt.els.overflow.ev_add';
		if ( !a_el ) { a_el=this.el;  } 
		const events = wdgt_overflow.events;
		events[a_name]={};
		const ev=events[a_name];
		ev.key=a_name;
		ev.type=a_type;
		ev.fn=a_fn;
		ev.el=a_el;
		//console.log(me, ev, 'added');
	};
	
	wdgt_overflow.listen = function()
	{
		const me = mdl.name+'.wdgt.els.overflow.listen';
		const events = wdgt_overflow.events;
		for ( const key in events )
		{
			const ev = events[key]
			//console.log(me, 'listening to evt', key,ev);
			ev.el.addEventListener(ev.type, ev.fn);
		}
	};
	

	wdgt_overflow.el=null
	wdgt_overflow.show= function()
	{
		document.body.classList.add("client_ui_overflow_hidden");
		this.el.classList.add(mdl.defs.cl_dspl_blck);
	}
	wdgt_overflow.showing= function()
	{
		//return this.el.classList.contains(mdl.defs.cl_vis);
		return this.el.classList.contains(mdl.defs.cl_dspl_blck);
	}
	wdgt_overflow.wdgt_close= function()
	{
		const me = mdl.name+'.wdgt.els.overflow.wdgt_close';
		//console.log(me,'dispatching event',this.ac.events_c.wdgt_close);
		document.dispatchEvent(this.ac.events_c.wdgt_close);
		//console.log(me,'hiding',this.ac.el);
		this.ac.el.classList.remove(mdl.defs.cl_dspl_blck);
		document.body.classList.remove(mdl.defs.cl_overflow_hide);
	}
	wdgt_overflow.hide= function()
	{
		this.el.classList.remove(mdl.defs.cl_dspl_blck);
		document.body.classList.remove(mdl.defs.cl_overflow_hide);
	}
	wdgt_overflow.create = function(a_cfg)
	{
		const me = mdl.name+'.wdgt.els.overflow.create';
		//console.log(me);
		const ac = {}; // export
		ac.el = document.createElement("div");
		ac.el.classList.add("wdgt_overflow");
		const top = document.createElement("div");
		top.classList.add("top");
		if ( a_cfg.title )
		{
			const ttl = document.createElement("p");
			ttl.classList.add('wdgt_title');
			ttl.innerHTML=a_cfg.title;
			top.append(ttl);
			//console.log(me, 'added title',a_cfg.title,ttl);
		}
		const btn_close = document.createElement("button");
		btn_close.classList.add('wdgt_close');
		top.append(btn_close);
		
		ac.el.append(top);
		//console.log(me,'created',btn_close);
		
		//console.log(me,'adding ev to',btn_close);
		wdgt_overflow.ev_add('click_btn_close','click',wdgt_overflow.wdgt_close, btn_close);
		
		btn_close.ac = ac;
		ac.el.ac = ac;
		ac.ev_add = wdgt_overflow.ev_add;
		ac.listen = wdgt_overflow.listen;
		ac.show = wdgt_overflow.show;
		ac.showing = wdgt_overflow.showing;
		ac.events_c = wdgt_overflow.events_c;
		//console.log(me,'creating c_ev');
		wdgt_overflow.ev_c_create('wdgt_close');
		//console.log(me,'returning ac.el',ac.el);
		return ac.el;
	}
	mdl.wdgt.get = function( a_name, a_cfg={} )
	{
		const me = mdl.name+'.wdgt.get';
		for ( const key in mdl.wdgt.els )
		{
			if ( key === a_name )
			{
				//console.log(me,'creating widget',key);
				return mdl.wdgt.els[key].create(a_cfg);
			}
		}
		throw new Error(me+'. Widget "'+a_name+'" not found.');  
	}
	
	
	/**/
	function add_module()
	{
		client_ui.mdls[mdl.key] = mdl;
		//console.log(mdl.key,'added');
	}
	document.addEventListener( client_ui.events.ready.name, add_module,false );
}



