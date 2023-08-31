"use strict";
const module_field = {};
module_field.msg='<?=field::$cmsg;?>';
module_field.defined = function( a_in )
{
	if ( a_in == 'undefined' )
	{
		return false;
	}
	return a_in;
}

module_field.datr_get=function( a_obj, a_atr )
{
	if ( !a_obj.dataset ) { return undefined; }
	//if ( !a_obj.dataset[a_atr] ) { return null; }
	return a_obj.dataset[a_atr];
}
module_field.reindex=function( a_obj={} )
{
	const app = module_field;
	const me='module_field.reindex';
	const fields=app.el.querySelectorAll('.field');
	//console.log(me);
	let uniques = [];
	for(let ndx=0; ndx < fields.length; ndx++)
	{ //break;
		const field = fields[ndx];
		if ( field.ndx==ndx && !uniques.includes(field.ndx) )
		{
			//console.log(me,'skipping correct field.ndx',field.ndx,'=',ndx);
			continue;
		}
		uniques.push(ndx);
		if ( !field.ndx )
		{
			field.ndx=ndx;
			//console.log(me,'first run, field.ndx set to ndx',field.ndx);
		}
		const inputs=field.querySelectorAll('input, select, textarea');
		//console.log(me,'---',ndx);
		//console.log(me,'need to reindex inputs for',field,ndx);
		for(let ndx2=0; ndx2 < inputs.length; ndx2++)
		{
			const el_input = inputs[ndx2];
			const newname = el_input.name.replace(/_\d+_/, '_'+(ndx)+'_');
			const newid ='f'+ndx+'_'+ndx2;
			el_input.id=newid;
			const el_label=el_input.previousElementSibling;
			if ( el_label.localName=='label')
			{
				el_label.htmlFor=newid;
			}
			el_input.name=newname;
		}
		//console.log(me,'reindexed field 1st input',field.querySelector('input'));
		//console.log(me,'reindexed field inputs',field.querySelectorAll('input'));
	}
	const len=fields.length;
	//console.log(me,'fields.length',len);
	if ( a_obj.upd_total )
	{
		//console.log(me,'--updating len',len);
		const els_cnt= document.querySelectorAll('.fields_total');
		for(let ndx=0; ndx < els_cnt.length; ndx++)
		{
			const el=els_cnt[ndx];
			el.innerHTML=len;
			
		}
	}
}
module_field.value_type_adjust=function( arg )
{
	const app = module_field;
	const dd = app.defined;
	const me='module_field.value_type_adjust';
	let el;
	if ( arg.target )
	{ el = arg.target; }
	else
	{ el = arg; }
	if ( 'type' !== el.getAttribute('data-field_item') )
	{
		return;
	}
	const opt = dd(dd(dd(el.selectedOptions))[0]).value;
	if ( !opt )
	{
		console.log(me,'no opt', opt);
		return;
	}
	const attributes=['id','className','dataset','name','value'];
	const el_src = el.closest('.field').querySelector('*[data-field_item=value]');
	if ( opt=='web_editor' )
	{
		const el_dest = document.createElement('textarea');
		for( const dkey in el_src.dataset)
		{
			el_dest.dataset[dkey]=el_src.dataset[dkey];
		}
		for( const key in el_src )
		{
			if ( !attributes.includes(key) ) { continue; }
			//console.log(me, 'key',key,'value',el_src[key]);
			if ( key == 'dataset' )
			{
				for( const dkey in el_src.dataset)
				{
					el_dest.dataset[dkey]=el_src.dataset[dkey];
				}
			}
			else if ( key == 'value' )
			{
				el_dest.innerHTML=el_src[key];
			}
			else
			{
				el_dest[key]=el_src[key];
			}
		}
		//console.log(me, 'el_dest',el_dest);
		//console.log(me, 'switching to web editor for',el_src);
		el_src.parentNode.replaceChild(el_dest, el_src);
		web_editor ( el_dest );
	}
	else
	{
		if ( el_src.localName !== 'textarea' )
		{ return; }
		const el_dest = document.createElement('input');
		console.log(me,'el_src.innerHTML',el_src.innerHTML,el_src,'opt',opt);
		const cke_inst = CKEDITOR.instances[el_src.id];
		if ( cke_inst )
		{
			el_dest.value=cke_inst.getData();
			CKEDITOR.instances[el_src.id].destroy();
		}
		else
		{
			el_dest.value=el_src.innerHTML;
		}
		for( const dkey in el_src.dataset)
		{
			el_dest.dataset[dkey]=el_src.dataset[dkey];
		}
		for( const key in el_src )
		{
			if ( !attributes.includes(key) ) { continue; }
			//console.log(me, 'key',key,'value',el_src[key]);
			if ( key == 'dataset' )
			{
				for( const dkey in el_src.dataset)
				{
					el_dest.dataset[dkey]=el_src.dataset[dkey];
				}
			}
			else
			{
				el_dest[key]=el_src[key];
			}
		}
		//el_dest.value=el_src.innerHTML;
		//console.log(me, 'el_dest',el_dest);
		//console.log(me, 'switching to text input for',el_src);
		el_src.parentNode.replaceChild(el_dest, el_src)
	}
}
module_field.del=function( a_el )
{
	const me='module_field.del';
	const app = module_field;
	const fields=app.el.querySelectorAll('.field');
	if ( fields.length == 1 )
	{
		app.add();
	}
	a_el.classList.add('anim_fade');
	//console.log(me,'a_el',a_el);
	setTimeout(function(){
			a_el.classList.remove('anim_fade');
			a_el.remove();
			client_ui.mdls.sortman.dest_els_reindex(app.sortman);
			app.reindex({upd_total:true});
		}, 500);
}
module_field.add=function()
{
	const me='module_field.add';
	const app = module_field;
	const dd=app.defined;
	const el_ref = app.sortman.dest_els[app.sortman.dest_els.length-1];
	
	const el_item_value = dd(el_ref.querySelector('*[data-field_item=value]'));
	const cke_inst = CKEDITOR.instances[el_item_value.id];
	console.log(me, 'cke_inst',cke_inst,'el_ref',el_ref,el_item_value.id);
	let ck_data;
	if ( cke_inst )
	{
		cke_inst.destroy();
	}
	
	const clone = el_ref.cloneNode(true);
	clone.ac = Object.assign({}, el_ref);
	//console.log(me,'el_ref',el_ref);
	clone.classList.add('anim_fade');
	el_ref.parentNode.insertBefore(clone, el_ref.nextSibling);
	const inputs=clone.querySelectorAll('input, select, textarea');
	//console.log(me, 'inputs',inputs);
	for(let ndx=0; ndx < inputs.length; ndx++)
	{
		const field = inputs[ndx];
		console.log(me, 'field',field);
		if ( field.localName=='input' ) { field.value=''; }
		if ( field.localName=='select' ) { field.selectedIndex = "-1"; }
		if ( field.localName=='textarea' )
		{
			field.innerHTML = "";
			field.value='';
			field.removeAttribute('value');
		}
	}
	clone.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
	setTimeout(function(){
			clone.classList.remove('anim_fade');
		}, 500);
	client_ui.mdls.sortman.dest_els_reindex(app.sortman);
	app.reindex({upd_total:true});
}
module_field.toggle_inner=function( a_el )
{
	const app = module_field;
	const me='module_field.toggle_inner';
	//console.log('app.sortman.ev_listeners_status',app.sortman.ev_listeners_status);
	if ( app.sortman.ev_listeners_status == '-' )
	{
		a_el.innerHTML="<?=$tr('field/drag_on');?>";
	}
	else if ( app.sortman.ev_listeners_status == '+' )
	{
		a_el.innerHTML="<?=$tr('field/drag_off');?>";
	}
}
module_field.dragtoggle=function( a_el )
{
	const app = module_field;
	const me='module_field.dragtoggle';
	
	if ( app.sortman.ev_listeners_status == '-' )
	{
		client_ui.mdls.sortman.ev_listeners(app.sortman,'add');
		a_el.innerHTML="<?=$tr('field/drag_off');?>"
	}
	else if ( app.sortman.ev_listeners_status == '+' )
	{
		client_ui.mdls.sortman.ev_listeners(app.sortman,'remove');
		a_el.innerHTML="<?=$tr('field/drag_on');?>";
	}
}
module_field.collapsetoggle=function( a_el )
{
	const app = module_field;
	const me='module_field.collapsetoggle';
	
	const els = document.querySelectorAll('.module_field .field .control')
	if ( els[0].classList.contains('collapsed') )
	{
		console.log(me,'collapsed:');
		for(let ndx=0; ndx < els.length; ndx++)
		{
			const el=els[ndx];
			//console.log(me,' - ',el);
			el.classList.remove('collapsed');
		}
		a_el.innerHTML="<?=$tr('field/collapse_on');?>";
	}
	else
	{
		console.log(me,'not collapsed:');
		for(let ndx=0; ndx < els.length; ndx++)
		{
			const el=els[ndx];
			//console.log(me,' - ',el);
			el.classList.add('collapsed');
		}
		a_el.innerHTML="<?=$tr('field/collapse_off');?>";
	}
}
module_field.click=function( a_ev )
{
	const app = module_field;
	const me='module_field.click';
	const el = a_ev.target;
	if ( 'field_dragtoggle'==el.getAttribute('data-cmd') )
	{
		a_ev.preventDefault();
		app.dragtoggle( el );
	}
	if ( 'field_collapsetoggle'==el.getAttribute('data-cmd') )
	{
		a_ev.preventDefault();
		app.collapsetoggle( el );
	}
	if ( 'field_del'==el.getAttribute('data-cmd') )
	{
		if ( !app.el.contains(el) )
		{ return; }
		const el_target = el.closest('.field');
		a_ev.preventDefault();
		app.del(el_target);
	}
	if ( 'field_add'==el.getAttribute('data-cmd') )
	{
		a_ev.preventDefault();
		client_ui.mdls.sortman.dest_els_reindex(app.sortman);
		app.add();
	}
	if ( 'field_up'==el.getAttribute('data-cmd') )
	{
		a_ev.preventDefault();
		app.el.scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });
	}
	if ( 'field_down'==el.getAttribute('data-cmd') )
	{
		a_ev.preventDefault();
		app.el.scrollIntoView({ behavior: 'smooth', block: 'end', inline: 'nearest' });
	}
}
module_field.run = function()
{
	const me='module_field.run';
	const app = module_field;
	const dd = app.defined;
	const el_controls = document.querySelector('.module_field.controls');
	app.el=document.querySelector('#field_form');
	console.log(me);
	app.sortman=client_ui.mdls.sortman.run('#field_form .field',{disabled:true});
	app.el.addEventListener( 'drop', function(a_ev){app.reindex(a_ev.target); } );
	app.el.addEventListener( 'change', app.value_type_adjust );
	app.el.addEventListener( 'click', module_field.click );
	el_controls.addEventListener( 'click', module_field.click );
	
	const el_collapsetgl=document.querySelector('.module_field.controls *[data-cmd=field_collapsetoggle]');
	if ( document.querySelector('.module_field .field .control.collapsed') )
	{
		el_collapsetgl.innerHTML="<?=$tr('field/collapse_off');?>";
	}
	else
	{
		el_collapsetgl.innerHTML="<?=$tr('field/collapse_on');?>";
	}
	const el_dragtgl = document.querySelector('.module_field.controls *[data-cmd=field_dragtoggle]');
	if ( app.sortman.ev_listeners_status == '-' )
	{
		el_dragtgl.innerHTML="<?=$tr('field/drag_on');?>";
	}
	else if ( app.sortman.ev_listeners_status == '+' )
	{
		el_dragtgl.innerHTML="<?=$tr('field/drag_off');?>";
	}
	
	if ( module_field.msg )
	{
		const msg = module_field.msg;
		const el_ref = document.querySelector('div.bodycontent');
		const el = document.createElement('div');
		el_ref.parentNode.insertBefore(el, el_ref);
		el.outerHTML='<div class="updated" style="display:block;">'+msg+'</div>';
	}
	const els_ftype = app.el.querySelectorAll('*[data-field_item=type]');
	for(let ndx=0; ndx < els_ftype.length; ndx++)
	{
		const el = dd(els_ftype[ndx]);
		const opt =dd(el.selectedOptions[0])['value'];
		if ( !opt ) { return; }
		if ( 'web_editor' == opt )
		{
			//console.log(me,'el_ftype',el,opt,);
			app.value_type_adjust(el);
		}
	}
}
document.addEventListener('client_ui.ready',module_field.run);
