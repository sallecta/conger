/*

Client UI

Site
https://sallecta.github.io/client_ui

Github
https://github.com/sallecta/client_ui 

 */
"use strict";
const client_ui = {};
client_ui.version = '0.4.2';//https://semver.org/
client_ui.name = 'client_ui';
client_ui.resources = ['core','gallery'];
client_ui.resources = [];
client_ui.resources.push({type:'css',id:'client_ui',});
client_ui.resources.push({type:'module',id:'core',});
client_ui.resources.push({type:'module',id:'gallery',});
client_ui.resources.push({type:'module',id:'sortman',});
client_ui.resources.push({type:'module',id:'to_top',});
client_ui.resmdls_total=0;

client_ui.mdls = {};
client_ui.load_err = false;
client_ui.events = {};
client_ui.dirname = function( a_path,a_levels=1 )
{
	const me=client_ui.name+'.dirname';
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
} // client_ui.dirname

client_ui.path = client_ui.dirname(window.document.currentScript.src);

// Create the events.
client_ui.events.included = document.createEvent("Event");
client_ui.events.included.name = 'client_ui.included';
client_ui.events.included.initEvent(client_ui.events.included.name, true, true);
//
client_ui.events.loaded = document.createEvent("Event");
client_ui.events.loaded.name = 'client_ui.loaded';
client_ui.events.loaded.initEvent(client_ui.events.loaded.name, true, true);
//
client_ui.events.ready = document.createEvent("Event");
client_ui.events.ready.name = 'client_ui.ready';
client_ui.events.ready.initEvent(client_ui.events.ready.name, true, true);
//
client_ui.include = function()
{
	const me = 'client_ui.include';
	
	function add_resource(a_res, a_fn)
	{
		const me = 'client_ui.includer.add_resource';
		let el;
		const id = a_res.id;
		if ( a_res.type === 'module' )
		{
			el = document.createElement('script');
			el.src = client_ui.path+'/modules/'+id+'/'+id+'.js'; 
			el.type = 'text/javascript';
		}
		if ( a_res.type === 'css' )
		{
			el = document.createElement('link');
			el.rel = 'stylesheet'; 
			el.href = client_ui.path+'/css/'+id+'.css'; 
			el.type = 'text/css';
		}
		el.id = id;
		el.addEventListener('error',a_fn);
		el.addEventListener('load',a_fn);
		document.head.appendChild(el);
	}
	
	function next(a_ev)
	{
		client_ui.cnt = client_ui.cnt + 1;
		if ( a_ev.type === 'load')
		{
			//console.log(me,client_ui.cnt,' resource',a_ev.target.id,'included',JSON.stringify(client_ui.mdls));
			if ( client_ui.resources[client_ui.cnt] )
			{
				//console.log( me,client_ui.cnt,' Including resource',client_ui.resources[client_ui.cnt] );
				add_resource(client_ui.resources[client_ui.cnt],next);
			}
			else
			{
				//console.log(me,client_ui.cnt,'All client_ui resources included.');
				document.dispatchEvent(client_ui.events.included);
			}
		}
		else if ( a_ev.type === 'error' )
		{
			client_ui.load_err = true;
			console.error(me,client_ui.cnt,' resource',a_ev.target.id,'of type',a_ev.target.type,'not loaded');
		}
		else
		{
			client_ui.load_err = true;
			console.error(me,client_ui.cnt,'unknown result on resource',a_ev.id,a_ev.type);
		}
	};
	
	client_ui.cnt = 0;
	add_resource(client_ui.resources[client_ui.cnt],next);
}
client_ui.include();

client_ui.included = function(a_ev)
{
	const me = 'client_ui.included';
	
	for(let ndx=0; ndx < client_ui.resources.length; ndx++)
	{
		const item = client_ui.resources[ndx];
		if ( item.type !== 'module')
		{
			continue;
		}
		client_ui.resmdls_total = client_ui.resmdls_total + 1;
		//console.log(me, '-- counted as module',item);
	}
}

client_ui.loaded = function(a_ev)
{
	const me = 'client_ui.loaded';
	const mdls_total = Object.keys(client_ui.mdls).length;
	if ( mdls_total !== client_ui.resmdls_total)
	{
		//console.log(me, 'app not ready','mdls_total',mdls_total,'client_ui.resmdls_total',client_ui.resmdls_total);
	}
	else
	{
		//console.log(me, 'app is ready','mdls_total',mdls_total,'client_ui.resmdls_total',client_ui.resmdls_total);
		document.dispatchEvent(client_ui.events.ready);
	}
}

client_ui.ready = function(a_ev)
{
	const me = 'client_ui.ready';
	//console.log(me, 'hi');
	
	if (typeof client_ui.run === "function")
	{
		//console.log(me, 'executing external function client_ui.run');
		client_ui.run();
	}
	else
	{
		//console.log(me, 'missing client_ui.run(), nothing to do');
		return;
	}
}
document.addEventListener( client_ui.events.included.name, client_ui.included,false );
document.addEventListener( client_ui.events.loaded.name, client_ui.loaded,false );
document.addEventListener( client_ui.events.ready.name, client_ui.ready,false );
