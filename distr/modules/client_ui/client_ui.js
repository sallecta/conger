"use strict";
const client_ui = {};
client_ui.version = '0.4.0';//https://semver.org/
client_ui.name = 'client_ui';
client_ui.resources = ['core','gallery'];
client_ui.resources = [];
client_ui.resources.push({type:'css',id:'client_ui',});
client_ui.resources.push({type:'module',id:'core',});
client_ui.resources.push({type:'module',id:'gallery',});
client_ui.resources.push({type:'module',id:'sortman',});
client_ui.resources.push({type:'module',id:'to_top',});
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

client_ui.includer = function()
{
	const me = 'client_ui.includer';
	var cnt = 0;
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
	
	// Create the event.
	client_ui.events.ready = document.createEvent("Event");
	client_ui.events.ready.name = 'client_ui.ready';
	client_ui.events.ready.initEvent(client_ui.events.ready.name, true, true);
	
	//console.log( me,cnt,' loading resource',client_ui.resources[cnt] );
	add_resource(client_ui.resources[cnt],next);
	
	function next(a_ev)
	{
		cnt = cnt + 1;
		if ( a_ev.type === 'load')
		{
			//console.log(me,cnt,' resource',a_ev.target.id,'loaded');
			if ( client_ui.resources[cnt] )
			{
				//console.log( me,cnt,' loading resource',client_ui.resources[cnt] );
				add_resource(client_ui.resources[cnt],next);
			}
			else
			{
				//console.log(me,cnt,'client_ui is ready');
				document.addEventListener( client_ui.events.ready.name, client_ui.exec,false );
				document.dispatchEvent(client_ui.events.ready);
			}
		}
		else if ( a_ev.type === 'error' )
		{
			client_ui.load_err = true;
			console.error(me,cnt,' resource',a_ev.target.id,'of type',a_ev.target.type,'not loaded');
		}
		else
		{
			client_ui.load_err = true;
			console.error(me,cnt,'unknown result on resource',a_ev.id,a_ev.type);
		}
		
	};
}();

client_ui.exec = function(a_ev)
{
	const me = 'client_ui.exec';
	if (typeof client_ui.run === "function")
	{
		//console.log(me, 'executing external function client_ui.run');
		client_ui.run();
	}
	else
	{
		console.warn(me, 'missing client_ui.run(), nothing to do');
	}
}
