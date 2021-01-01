"use strict";

if (typeof window.menu_hold_time=='undefined')
{
	window.menu_hold_time=500;

	window.clean_menus_timeout=null;
	window.active_menu=null; // ID of currently hovered branch
	window.last_active_menu=null; // Actual menu name
}

function clean_menus()
{
	window.clean_menus_timeout=null;

	var m=document.getElementById('r_'+window.last_active_menu);
	if (!m) return;
	var tags=get_elements_by_class_name(m,'nlevel');
	var e=(window.active_menu==null)?null:document.getElementById(window.active_menu),t;
	var i,hideable;
	for (i=tags.length-1;i>=0;i--)
	{
		if (tags[i].nodeName.toLowerCase()!='ul' && tags[i].nodeName.toLowerCase()!='div') continue;

		hideable=true;
		if (e)
		{
			// Make sure tags[i] is not at or above the current expansion position 
			t=e;
			do
			{
				if (tags[i].id==t.id) hideable=false;
				t=t.parentNode.parentNode;
			}
			while (t.id!='r_'+window.last_active_menu); // Until you get to root node of menu
		}
		if (hideable)
		{
			tags[i].style.left='-999px';
			tags[i].style.display='none';
		}
	}
}

function set_active_menu(id,menu)
{
	window.active_menu=id;
	if (menu!=null) window.last_active_menu=menu;
}

function deset_active_menu()
{
	window.active_menu=null;

	recreate_clean_timeout();
}

function recreate_clean_timeout()
{
	if (window.clean_menus_timeout)
	{
		window.clearTimeout(window.clean_menus_timeout);
	}
	window.clean_menus_timeout=window.setTimeout(clean_menus,window.menu_hold_time);
}

function pop_up_menu(id,place,menu,event,outside_fixed_width)
{
	if ((typeof place=='undefined') || (!place)) var place='right';
	if (typeof outside_fixed_width=='undefined') outside_fixed_width=false;

	var e=document.getElementById(id);
	if (!e) return false;

	if (e.style.display=='block')
	{
		// Already expanded, possibly because it's a parent branch of to what we are now looking at - so we must not do anything
		return false;
	}

	set_active_menu(id,menu);
	if (window.clean_menus_timeout)
	{
		window.clearTimeout(window.clean_menus_timeout);
		window.clean_menus_timeout=null;
	}
	clean_menus();

	var l=0;
	var t=0;
	var p=e.parentNode;

	// Our own position computation as we are positioning relatively, as things expand out
	if (abstract_get_computed_style(p.parentNode,'position')=='fixed' || abstract_get_computed_style(p.parentNode,'position')=='absolute')
	{
		l+=p.offsetLeft;
		t+=p.offsetTop;
	} else
	{
		while (p)
		{	
			if ((p) && (abstract_get_computed_style(p,'position')=='relative')) break;
			l+=p.offsetLeft;
			t+=p.offsetTop-sts(p.style.borderTop);
			p=p.offsetParent;
			if ((p) && (abstract_get_computed_style(p,'position')=='fixed')) break;
			if ((p) && (abstract_get_computed_style(p,'position')=='absolute')) break;
		}
	}
	if (place=='below')
	{
		t+=e.parentNode.offsetHeight;
	} else
	{
		l+=e.parentNode.offsetWidth;
	}

	var full_height=get_window_scroll_height(); // Has to be got before e is visible, else results skewed
	e.style.position='absolute';
	e.style.left='0'; // Setting this lets the browser calculate a more appropriate (larger) width, before we set the correct left for that width will fit
	e.style.display='block';
	if (typeof window.fade_transition!='undefined')
	{
		set_opacity(e,0.0);
		fade_transition(e,100,30,8);
	}
	var full_width=(window.scrollX==0)?get_window_width():get_window_scroll_width();
	/*{+START,IF,{$CONFIG_OPTION,fixed_width}}*/
		if (!outside_fixed_width)
		{
			var main_website_inner=document.getElementById('main_website_inner');
			if (main_website_inner) full_width=find_width(main_website_inner);
		}
	/*{+END}*/
	var e_parent_width=find_width(e.parentNode);
	e.style.minWidth=e_parent_width+'px';
	var e_parent_height=find_height(e.parentNode);
	var e_width=find_width(e);
	var position_l=function() {
		var pos_left=l;
		if (place=='below') // Top-level of drop-down
		{
			if (pos_left+e_width>full_width)
			{
				pos_left+=e_parent_width-e_width;
			}
		} else
		{ // NB: For non-below, we can't assume 'left' is absolute, as it is actually relative to parent node which is itself positioned
			if ((find_pos_x(e.parentNode,true)+e_width+e_parent_width+10>full_width) && (pos_left-e_width-e_parent_width>0)) pos_left-=e_width+e_parent_width;
		}
		e.style.left=pos_left+'px';
	};
	position_l();
	window.setTimeout(position_l,0);
	var position_t=function() {
		var pos_top=t;
		if (pos_top+find_height(e)+10>full_height)
		{
			var above_pos_top=pos_top-find_height(e,true)+e_parent_height-10;
			if (above_pos_top>0) pos_top=above_pos_top;
		}
		e.style.top=pos_top+'px';
	};
	position_t();
	window.setTimeout(position_t,0);
	e.style.zIndex=200;


	recreate_clean_timeout();

	if ((typeof event!='undefined') && (event))
	{
		cancel_bubbling(event);
	}

	return false;
}

