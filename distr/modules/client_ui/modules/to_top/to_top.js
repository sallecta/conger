"use strict";
new function()
{
	const mdl={};
	mdl.key='to_top';
	mdl.name=client_ui.name+'.'+mdl.key;
	mdl.defs={};
	mdl.defs.cl_hide='hide';
	mdl.defs.cl_show='show';;
	//
	mdl.script_path = window.document.currentScript.src;
	Object.freeze(mdl.defs);
	//
	mdl.ev={};
	mdl.ev.lis={};

	mdl.ev.lis.scroll = function()
	{
		window.scroll({top:0,left:0,behavior:"smooth",});
	};
	
	mdl.ev.lis.toggle = function()
	{
		mdl.toggle();
	};
	mdl.ev.lis.toggle_end = function()
	{
		console.log('end');
	}
	
	mdl.hide_cnt = 0;
	mdl.toggle = function()
	{
		mdl.scroll_top1=document.body.scrollTop;
		mdl.scroll_top2=document.documentElement.scrollTop;
		if ( !mdl.ready )
		{
			mdl.header_el = document.querySelector('header');
			if ( mdl.header_el )
			{
				mdl.lim = mdl.header_el.getBoundingClientRect().height;
				mdl.lim = mdl.lim + mdl.header_el.getBoundingClientRect().top;
				mdl.lim = Math.round(mdl.lim);
			}
			else
			{
				mdl.lim=20;
			}
			mdl.ready = true;
		}
		if (mdl.scroll_top1 > mdl.lim || mdl.scroll_top2 > mdl.lim)
		{
			mdl.el.classList.remove(mdl.defs.cl_hide);
			mdl.el.classList.add(mdl.defs.cl_show);
		}
		else
		{
			mdl.el.classList.remove(mdl.defs.cl_show);
		}
		if ( ! mdl.el.classList.contains(mdl.defs.cl_show) )
		{
			mdl.hide_cnt = mdl.hide_cnt + 1;
			//console.log('hide_cnt',mdl.hide_cnt);
			if ( mdl.hide_cnt >= 5 )
			{
				//console.log('need to hide',mdl.hide_cnt);
				mdl.el.classList.add(mdl.defs.cl_hide);
			}
		}
		else
		{
			mdl.hide_cnt = 0;
		}
	} // mdl.toggle
	
	mdl.configure = function ()
	{
		const me = mdl.name+'.configure';
		mdl.path = client_ui.mdls.core.dirname(mdl.script_path);
		//console.log(me,mdl.name+'.path',mdl.path);
		const href =  mdl.path+'/css/'+mdl.name+'.css';
		const el_preload = document.createElement('link');
		el_preload.rel = 'preload';
		el_preload.href = href;
		el_preload.as = 'style';
		document.head.appendChild(el_preload);
		
		const el_css = document.createElement('link');
		el_css.rel = 'stylesheet'; 
		el_css.href = href; 
		document.head.appendChild(el_css);
		mdl.configured=true;
	
		mdl.el = document.createElement("div");
		mdl.el.id = mdl.key;
		mdl.el.classList.add(mdl.defs.cl_hide);
		document.body.appendChild(mdl.el);
	}
	
	mdl.run = function ()
	{
		if ( !mdl.configured )
		{
			mdl.configure();
		}
		
		mdl.el.addEventListener('click', mdl.ev.lis.scroll);
		window.addEventListener('scroll', mdl.ev.lis.toggle);
		window.addEventListener('load', mdl.ev.lis.toggle);
	}
	/**/
	function add_module()
	{
		const me = mdl.name+'.add_module';
		client_ui.mdls[mdl.key] = mdl;
		console.log(me,'done');
	}
	document.addEventListener( client_ui.events.ready.name, add_module,false );
	/**/
} // new function
