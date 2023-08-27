// https://www.w3schools.com/howto/howto_js_scroll_to_top.asp
/*
When the user scrolls down header offset height or 20px from the top
of the document, show the button.
*/
window.onscroll = function() {to_top.show_hide()};

const to_top={};

to_top.show_hide = function()
{
	if ( !to_top.ready )
	{
		to_top.el = document.getElementById("to_top");
		to_top.header_el = document.querySelector('header');
		if ( to_top.header_el )
		{
			to_top.lim = to_top.header_el.getBoundingClientRect().height;
			to_top.lim = to_top.lim + to_top.header_el.getBoundingClientRect().top;
			to_top.lim = Math.round(to_top.lim);
		}
		else
		{
			to_top.lim=20;
		}
		to_top.ready = true;
	}
	if (document.body.scrollTop > to_top.lim || document.documentElement.scrollTop > to_top.lim)
	{
		to_top.el.classList.add("display_block");
	}
	else
	{
		to_top.el.classList.remove("display_block");
	}
}
