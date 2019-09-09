"use strict";

function set_edited_panel(object_ignore /* No longer used */,id)
{
	var store;

	/* The editing box */

	var object=document.getElementById('edit_'+id+'_textarea');
	if ((object) && (object.nodeName.toLowerCase()=='textarea'))
	{
		store=document.getElementById('store_'+id);
		if (!store)
		{
			store=document.createElement('textarea');
			store.name=object.name;
			store.id='store_'+id;
			document.getElementById('edit_field_store').appendChild(store);
		}
		store.value=get_textbox(object);
	}

	/* The WYSIWYG setting (not the actual HTML text value of the editor, the setting of whether WYSIWYG was used or not) */

	var object=document.getElementById('edit_'+id+'_textarea__is_wysiwyg');
	if (object)
	{
		store=document.getElementById('wysiwyg_store_'+id);
		if (!store)
		{
			store=document.createElement('textarea');
			store.name=object.name;
			store.id='wysiwyg_store_'+id;
			document.getElementById('edit_field_store').appendChild(store);
		}
		store.value=object.value;
	}

	/* The redirect setting */

	var object=document.getElementById('redirect_'+id);
	if ((object) && (object.nodeName.toLowerCase()=='select'))
	{
		store=document.getElementById('redirects_store_'+id);
		if (!store)
		{
			store=document.createElement('textarea');
			store.name=object.name;
			store.id='redirects_store_'+id;
			document.getElementById('edit_field_store').appendChild(store);
		}
		store.value=object.options[object.selectedIndex].value;
	}
}

function fetch_more_fields()
{
	set_edited_panel(null,'panel_left');
	set_edited_panel(null,'panel_right');
	set_edited_panel(null,'panel_top');
	set_edited_panel(null,'panel_bottom');
	set_edited_panel(null,'start');

	var form=document.getElementById('middle_fields');
	var edit_field_store=document.getElementById('edit_field_store');
	var i,store;
	for (i=0;i<form.elements.length;i++)
	{
		if (form.elements[i].name==='csrf_token') continue;

		store=document.createElement('input');
		store.setAttribute('type','hidden');
		store.name=form.elements[i].name;
		if (form.elements[i].getAttribute('type')=='checkbox')
		{
			store.value=form.elements[i].checked?'1':'0';
		} else
		{
			store.value=form.elements[i].value;
		}
		edit_field_store.appendChild(store);
	}
//	window.setTimeout(function() { form.submit(); } , 1000);
}

function select_ze_tab(id,tab)
{
	var tabs=['view','edit','info','settings'];
	var i,j,element,elementh,selects;

	for (i=0;i<tabs.length;i++)
	{
		element=document.getElementById(tabs[i]+'_'+id);
		elementh=document.getElementById(tabs[i]+'_tab_'+id);
		if (element)
		{
			element.style.display=(tabs[i]==tab)?'block':'none';
			if ((tabs[i]==tab) && (tab=='edit'))
			{
				if (is_wysiwyg_field(document.getElementById('edit_'+id+'_textarea')))
				{
					// Fix for Firefox
					if (typeof window.wysiwyg_editors['edit_'+id+'_textarea'].document!='undefined')
					{
						if (window.wysiwyg_editors['edit_'+id+'_textarea'].document.getBody().$) {
							window.wysiwyg_editors['edit_'+id+'_textarea'].document.getBody().$.contentEditable='false';
							window.wysiwyg_editors['edit_'+id+'_textarea'].document.getBody().$.contentEditable='true';
						}
					}
				}
			}
			if ((typeof window.fade_transition!='undefined') && (tabs[i]==tab))
			{
				set_opacity(element,0.0);
				fade_transition(element,100,30,4);

				elementh.className+=' tab_active';
			} else
			{
				elementh.className=elementh.className.replace(/ tab_active$/,'');
			}
		}
	}
}

if (typeof window.ze_timer=='undefined')
{
	window.ze_timer={};
	window.ze_delay_function={};
}
function ze_animate_to(ob,amount,towards_expanded,now)
{
	// Actually, we won't do an animation, as it's annoying
	ob.style.width=(amount)+'em';
	return;

	/*{+START,IF,{$NOT,{$CONFIG_OPTION,enable_animations}}}*/
		ob.style.width=(amount)+'em';
		if (true) return;
	/*{+END}*/

	if ((!now) && (!towards_expanded))
	{
		window.ze_delay_function[ob.id]=window.setTimeout(function() { ze_animate_to(ob,amount,towards_expanded,true); } ,1500);
		return;
	}

	if (window.ze_timer[ob.id])
	{
		window.clearTimeout(window.ze_timer[ob.id]);
		window.ze_timer[ob.id]=null;
	}

	var currently_expanded=(ob.className.indexOf('ze_panel_expanded')!=-1);

	if (
		( ((currently_expanded) && (towards_expanded)) || ((!currently_expanded) && (!towards_expanded)) ) &&
		(ob.style.width!=amount+'em')
	)
	{
		var w_now=window.parseFloat(ob.style.width.replace('em',''));
		if (w_now<amount)
		{
			ob.style.width=(w_now+0.5)+'em';
		} else
		{
			ob.style.width=(w_now-0.5)+'em';
		}
		window.ze_timer[ob.id]=window.setTimeout(function() { ze_animate_to(ob,amount,towards_expanded,now); } , 10 );
	} else
	{
		window.ze_timer[ob.id]=null;
	}
}

function reload_preview(id)
{
	if (typeof window.do_ajax_request=='undefined') return;
	if (typeof window.merge_text_nodes=='undefined') return;

	var element=document.getElementById('view_'+id);

	var edit_element=document.getElementById('edit_'+id+'_textarea');
	if (!edit_element) return; // Nothing interactively edited

	set_inner_html(element,'<div aria-busy="true" class="ajax_loading vertical_alignment"><img src="'+'{$IMG;,loading}'.replace(/^https?:/,window.location.protocol)+'" /> <span>{!LOADING;^}</span></div>');

	window.loading_preview_of=id;

	var data='';
	data+=get_textbox(edit_element);
	var url='{$FIND_SCRIPT_NOHTTP;,comcode_convert}?fix_bad_html=1&css=1&javascript=1&from_html=0&is_semihtml='+(is_wysiwyg_field(edit_element)?'1':'0')+'&panel='+(((id=='panel_left') || (id=='panel_right'))?'1':'0')+keep_stub();
	var post=(is_wysiwyg_field(edit_element)?'data__is_wysiwyg=1&':'')+'data='+window.encodeURIComponent(data);
	post=modsecurity_workaround_ajax(post);
	do_ajax_request(url,reloaded_preview,post);
}

function reloaded_preview(ajax_result_frame,ajax_result)
{
	if (typeof window.loading_preview_of=='undefined') return;
	var element=document.getElementById('view_'+window.loading_preview_of);
	set_inner_html(element,merge_text_nodes(ajax_result.childNodes).replace(/^((\s)|(\<br\s*\>)|(&nbsp;))*/,'').replace(/((\s)|(\<br\s*\>)|(&nbsp;))*$/,''));

	disable_preview_scripts(element);
}


