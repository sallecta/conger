"use strict";
new function()
{
	const mdl={};
	mdl.key='gallery';
	mdl.name=client_ui.name+'.'+mdl.key;
	/**/
	
	mdl.defs = {};
	mdl.defs.cl_vis='vis_op1';
	mdl.defs.cl_dspl_blck='dspl_blck';
	mdl.defs.cl_pntr='pointer';
	Object.freeze(mdl.defs);
	
	mdl.inst_count = 0;
	mdl.instances = {};
	const app = client_ui;
	
	mdl.handleGesure= function()
	{    
		if (mdl.touch.endX > mdl.touch.startX + 20)
		{
			mdl.backward();
		}
		else if (mdl.touch.endX < mdl.touch.startX - 20)
		{
			mdl.forward();
		}
	}
	mdl.backward= function()
	{
		mdl.el_update(this.a_inst.curr_item_ndx-1,this.a_inst,'<');
	}
	mdl.forward= function()
	{
		mdl.el_update(this.a_inst.curr_item_ndx+1,this.a_inst,'>');
	}
	
	mdl.get_dot_by_item = function( a_inst )
	{
		const dots = a_inst.el.els.dots;
		for (let ndx = 0; ndx < dots.children.length; ndx++) 
		{
			const dot = dots.children[ndx];
			if ( a_inst.curr_item_ndx === dot.item_ndx)
			{
				return dot;
			}
		}
	}
	
	mdl.dots_regen = function( a_inst, a_direction )
	{
		const me = mdl.name+'.dots_regen';
		let item_ndx = a_inst.curr_item_ndx;
		const dots = a_inst.el.els.dots;
		let ndx, cond;
		if ( a_direction == '>')
		{
			//console.log(me, 'starting from item',item_ndx, 'a_direction=',a_direction);
			for (let ndx = 0; ndx < dots.children.length; ndx++) 
			{
				if ( !a_inst.items[item_ndx] )
				{
					//console.log(me, 'no more items from',item_ndx);
					item_ndx = 0;
				}
				//console.log(me, 'working with',item_ndx,a_direction);
				const dot = dots.children[ndx];
				dot.item_ndx = item_ndx;
				if (a_inst.cfg.dot_numb)
				{
					dot.innerHTML = item_ndx+1;
				}
				dot.title=item_ndx+1;
				dot.removeEventListener("click", dot.click_handler);
				
				dot.click_handler = function()
				{
					const me = mdl.name+'.dots_regen dot.click_handler';
					mdl.el_update(this.item_ndx, a_inst);
				};
				dot.addEventListener("click", dot.click_handler);
				/* */
				item_ndx = item_ndx+1;
			}
		}
		if ( a_direction == '<')
		{
			//console.log(me, 'starting from',item_ndx, 'a_direction=',a_direction);
			for (let ndx = dots.children.length-1; ndx > -1; ndx--) 
			{
				if ( !a_inst.items[item_ndx] )
				{
					//console.log(me, '-- no more items from',item_ndx,'a_inst.items.length=',a_inst.items.length);
					item_ndx = a_inst.items.length-1;
				}
				//console.log(me, 'working with',item_ndx, a_direction);
				const dot = dots.children[ndx];
				dot.item_ndx = item_ndx;
				if (a_inst.cfg.dot_numb)
				{
					dot.innerHTML = item_ndx+1;
				}
				dot.title=item_ndx+1;
				dot.removeEventListener("click", dot.click_handler);
				
				dot.click_handler = function()
				{
					const me = mdl.name+'.dots_regen dot.click_handler';
					mdl.el_update(this.item_ndx, a_inst);
				};
				dot.addEventListener("click", dot.click_handler);
				/* */
				item_ndx = item_ndx-1;
			}
		}
		//console.log(me, 'done',dots.children);
	} // dots_regen
	
	mdl.dots_scroll_adj = function( a_inst )
	{
		const me = mdl.name+'.dots_scroll_adj';
		const dots = a_inst.el.els.dots;
		const scrollable=dots.scrollWidth > dots.clientWidth
		//console.log(me,'scrollable?',scrollable);
		if ( !scrollable ) { return; }
		const dots_vis_l = dots.offsetLeft+dots.scrollLeft;
		const dots_vis_r = dots.offsetLeft+dots.offsetWidth+dots.scrollLeft;
		const active_dot = a_inst.active_dot;
		const margin_l = parseInt(window.getComputedStyle(active_dot).marginLeft);
		const dot_left =  active_dot.offsetLeft+margin_l;
		if ( active_dot.offsetLeft < dots_vis_l )
		{
			//console.log(me,active_dot.offsetLeft < dots_vis_l,'active_dot.offsetLeft',active_dot.offsetLeft,' < dots_vis_l',dots_vis_l);
			const scroll = active_dot.offsetLeft-dots.offsetLeft-dots.offsetWidth+margin_l+active_dot.offsetWidth;
			//console.log(me,'scrolling <--',scroll);
			dots.scrollTo({top:0,left:scroll,behavior:"instant"});
		}
		if ( active_dot.offsetLeft+active_dot.offsetWidth > dots_vis_r )
		{
			//console.log(me,dot_left > dots_vis_r, 'dot_left',dot_left,' > dots_vis_r',dots_vis_r);
			const scroll = active_dot.offsetLeft-dots.offsetLeft-margin_l;
			//console.log(me,'scrolling -->',scroll);
			dots.scrollTo({top:0,left:scroll,behavior:"instant"});
		}
	}
	
	mdl.el_update = function( a_ndx, a_inst, a_direction='>' )
	{
		const me = mdl.name+'.el_update';
		
		const last_item_ndx = a_inst.items.length - 1;
		const last_dot_ndx = a_inst.el.els.dots.children.length - 1;
		let direction='';
		let ndx_item;
		
		if( a_ndx > last_item_ndx )
		{
			//console.log(me,' a_ndx > last_item_ndx',  a_ndx,last_item_ndx);
			if(a_inst.cfg.loop) { ndx_item = 0; }
			else { ndx_item = last_item_ndx; }
		}
		
		else if( a_ndx<0 )
		{
			//console.log(me,' a_ndx < 0',  a_ndx, 'a_inst.items.length',a_inst.items.length);
			if(a_inst.cfg.loop) { ndx_item = last_item_ndx; }
			else { ndx_item = 0; }
			//console.log(me,' a_ndx < 0',  a_ndx);
		}
		else
		{
			ndx_item=a_ndx;
		}
		
		if (a_ndx===a_inst.curr_item_ndx)
		{
			//console.log(me, 'a_ndx=mdl.ndx_curr, nothing to do, but scroll adj', 'item equal?',(a_ndx===a_inst.curr_item_ndx));
			mdl.dots_scroll_adj( a_inst );
			return;
		}
		
		if ( a_inst.curr_item_ndx !== undefined )
		{
			//console.log(me,'a_inst.curr_item_ndx exists', a_inst.curr_item_ndx);
			mdl.get_dot_by_item(a_inst).classList.remove("active");
			a_inst.el.els.item.remove();
		}
		
		//console.log(me,'ndx_item', ndx_item);
		
		a_inst.curr_item_ndx = ndx_item;
		
		let active_dot = mdl.get_dot_by_item(a_inst);
		if ( !active_dot )
		{
			//console.log(me, 'need to regen dots',a_inst.curr_item_ndx);
			mdl.dots_regen( a_inst, a_direction );
			active_dot = mdl.get_dot_by_item(a_inst);
		}
		if ( !active_dot )
		{
			//console.error(me, 'error getting dot by iitem',a_inst.curr_item_ndx);
			return;
		}
		active_dot.classList.add("active");
		const dots = a_inst.el.els.dots;
		a_inst.active_dot = active_dot;
		
		a_inst.el.els.counter.innerHTML =
			(ndx_item + 1)+'<span>'+
			a_inst.cfg.counterDivider+'</span>'+
			+a_inst.items.length;
		
		let img_path = a_inst.items[ndx_item].dataset.large;
		if (!img_path) {img_path=a_inst.items[ndx_item].src}
		////console.log(me,'img_path', img_path);
		
		let el_img = document.createElement("img");
		let el_item = document.createElement("div");
		el_item.classList.add("item");
		el_img.src = img_path;
		el_item.appendChild(el_img);
		a_inst.el.els.loading.classList.add(mdl.defs.cl_dspl_blck);
		el_img.addEventListener(
			"load", 
			function(a_ev)
			{
				a_inst.el.els.loading.classList.remove(mdl.defs.cl_dspl_blck);
				a_inst.el.els.item.classList.add(mdl.defs.cl_vis);
			}
		);
		a_inst.el.els.item=el_item;
		a_inst.el.els.main.append(a_inst.el.els.item);
		if ( a_inst.cfg.nfo && a_inst.cfg.nfo.cfg==='file' )
		{
			a_inst.el.els.nfo.innerHTML=client_ui.mdls.core.basename(img_path);
		}
		
		//console.log(me,'calling dots_scroll_adj');
		mdl.dots_scroll_adj( a_inst );
		let active_dot_hspace = parseInt(window.getComputedStyle(active_dot).marginLeft);
		active_dot_hspace = active_dot_hspace+parseInt(window.getComputedStyle(active_dot).marginRight);
		active_dot_hspace = active_dot_hspace+parseInt(window.getComputedStyle(active_dot).paddingRight);
		active_dot_hspace = active_dot_hspace+parseInt(window.getComputedStyle(active_dot).paddingLeft);
		active_dot_hspace = active_dot_hspace+active_dot.offsetWidth;
		
		//console.log(me,'done, a_inst.curr_item_ndx');
	} // el_update
	
	mdl.on_wdgt_close= function()
	{
		const me = mdl.name+'.on_wdgt_close';
		//console.log(me);
	}
	
	mdl.create_els = function( a_inst )
	{
		const me = mdl.name+'.create_els';
		//console.log(me,'a_inst=',a_inst);
		const app = client_ui;
		const wdgt= app.mdls.core.wdgt;
		//console.log(me,'getting wdgt overflow');
		a_inst.el = wdgt.get('overflow',{title:a_inst.cfg.title});
		a_inst.el.classList.add("mdl_gallery");
		//console.log(me,'adding listener to mdl.el.ac.events_c.wdgt_close',a_inst.el.ac.events_c.wdgt_close);
		document.addEventListener( a_inst.el.ac.events_c.wdgt_close.type, mdl.on_wdgt_close,false );
		
		//console.log(me,'create listening in wdgt overflow');
		a_inst.el.ac.listen();
		window.document.body.append(a_inst.el);
		
		a_inst.el.els={};
		
		//console.log('a_inst.el',a_inst.el);
		
		a_inst.el.els.main = document.createElement("div");
		a_inst.el.els.main.classList.add('main');
		a_inst.el.els.backward = document.createElement("button");
		a_inst.el.els.backward.classList.add('backward');
		a_inst.el.els.main.append(a_inst.el.els.backward);
		
		const to_fn = {};
		to_fn.a_inst = a_inst;
		
		a_inst.el.els.backward.addEventListener("click", mdl.backward.bind(to_fn));
		
		a_inst.el.els.forward = document.createElement("button");
		a_inst.el.els.forward.classList.add('forward');
		a_inst.el.els.main.append(a_inst.el.els.forward);
		a_inst.el.els.forward.addEventListener("click", mdl.forward.bind(to_fn));
		
		a_inst.el.els.loading = document.createElement("span");
		a_inst.el.els.loading.classList.add('loading');
		a_inst.el.els.loading.appendChild(wdgt.get('spinner'));
		a_inst.el.els.main.append(a_inst.el.els.loading);
		/**/
		a_inst.el.append(a_inst.el.els.main);
		
		a_inst.el.els.bot = document.createElement("bot");
		a_inst.el.els.bot.classList.add('bot');
		
		a_inst.el.els.nfo = document.createElement("div");
		a_inst.el.els.nfo.classList.add('nfo');
		a_inst.el.els.bot.append(a_inst.el.els.nfo);
		
		a_inst.el.els.counter = document.createElement("div");
		a_inst.el.els.counter.classList.add('counter');
		a_inst.el.els.bot.append(a_inst.el.els.counter);
		/**/
		a_inst.el.append(a_inst.el.els.bot);
		

		for (let ndx = 0; ndx < a_inst.items.length; ndx++) 
		{
			const item = a_inst.items[ndx];
			//console.log(me,'adding pointer style to',item);
			item.classList.add(mdl.defs.cl_pntr);
			//console.log(me,'adding onclick to item',item.src);
			item.click_handler = function(a_ev)
			{
				const me = mdl.name+'.create_els item.click_handler';
				a_inst.el.ac.show();
				mdl.el_update(ndx,a_inst);
			};
			const to_fn = {};
			//to_fn.a_inst = a_inst;
			item.addEventListener("click", item.click_handler.bind(to_fn));
		}
		
		a_inst.el.els.dots = document.createElement("div");
		a_inst.el.els.dots.classList.add('dots');
		for (let ndx = 0; ndx < a_inst.items.length; ndx++) 
		{
			if ( a_inst.cfg.dot_max && (ndx+1)>a_inst.cfg.dot_max )
			{
				//console.log(me,'skipping dot from', ndx);
				break;
			}
			const dot = document.createElement("div");
			if (a_inst.cfg.dot_numb)
			{
				dot.innerHTML = ndx+1;
			}
			dot.title=ndx+1;
			dot.item_ndx = ndx;
			dot.click_handler = function()
			{
				mdl.el_update( ndx,a_inst );
			};
			dot.addEventListener("click", dot.click_handler);
			a_inst.el.els.dots.append(dot);
		}
		a_inst.el.append(a_inst.el.els.dots);
		//console.log(me,'done');
	}; // create_els
	
	mdl.run = function (a_cfg)
	{
		const me = mdl.name+'.run';
		const app = client_ui.mdls.core;
		//console.log(me);
		const cfg =
		{
			selector: "body",
			loop: true,
			forward: undefined,
			title: undefined,
			prev: undefined,
			dot_max: undefined,
			//dot_max: false,
			dot_numb: true,
			close: undefined,
			nfo: undefined,
			counter: undefined,
			counterDivider: "/",
			keyboardNavigation: true
		};
		Object.assign(cfg, a_cfg);
		mdl.el = null;
		mdl.ndx = 0;
		
		const items_nl= document.querySelectorAll(cfg.selector + ' img');
		
		if ( !items_nl || items_nl.length < 1 )
		{
			console.warn(me,'mothing selected, items_nl=',items_nl,items_nl.length);
			return;
		}
		
		// nodelist to array
		const items=[];
		for (let ndx = 0; ndx < items_nl.length; ndx++)
		{
			items.push(items_nl[ndx]);
		}
		
		for ( const key in mdl.instances)
		{
			const inst = mdl.instances[key];
			for (let ndx = 0; ndx < inst.items.length; ndx++) 
			{
				const item = inst.items[ndx];
				for (let ndx2 = 0; ndx2 < items.length; ndx2++)
				{
					const freshitem = items[ndx2];
					if ( freshitem===item )
					{
						items.splice(ndx2, 1); /* remove conflicted item */
					}
				} 
			}
		}
		
		//console.log(me,'got items=',items);
		
		mdl.inst_count = mdl.inst_count + 1;
		const inst_name = 'inst' + mdl.inst_count;
		mdl.instances[inst_name] = {};
		const inst = mdl.instances[inst_name];
		inst.name = inst_name;
		
		
		inst.cfg = cfg;
		inst.items = items;
		
		if ( inst.cfg.dot_max > inst.items.length )
		{
			inst.cfg.dot_max=inst.items.length;
		}
		
		if ( app.is_object(inst.cfg.title) )
		{
			if ( inst.cfg.title.cfg==='auto' )
			{
				inst.cfg.title='auto title';
				let txt = inst.items[0].parentElement.previousElementSibling.innerText;
				if ( !txt ) { txt = ''; }
				inst.cfg.title = txt;
			}
		}
		else if ( typeof inst.cfg.title !== 'string' )
		{
			inst.cfg.title='';
		}
		
		inst.touch = { endX: 0, startX: 0 };
		mdl.create_els(inst);
		//console.log(me,'created instance', inst);
	} // create
	
	/**/
	function add_module()
	{
		const me = mdl.name+'.add_module';
		client_ui.mdls[mdl.key] = mdl;
		//console.log(me,'done');
		document.dispatchEvent(client_ui.events.loaded);
	}
	document.addEventListener( client_ui.events.included.name, add_module,false );
}



