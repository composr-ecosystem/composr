"use strict";

function make_tooltip_func(ob,op)
{
	return function(event) {
		if (typeof event=='undefined') event=window.event;

		ob.hovered=true;

		if (typeof window.tpl_descrips[op]=='undefined')
		{
			var get_and_pass_descrip=function(callback) {
				do_ajax_request(window.tpl_load_url+'?theme='+window.encodeURIComponent(window.current_theme)+'&id='+window.encodeURIComponent(op)+keep_stub(),function(loaded_result) {
					if (!loaded_result) return '';
					window.tpl_descrips[op]=loaded_result.responseText;
					callback('<div class="whitespace_visible">'+escape_html(window.tpl_descrips[op])+'</div>');
				});
			};
		} else
		{
			var get_and_pass_descrip=function(callback) {
				callback('<div class="whitespace_visible">'+escape_html(window.tpl_descrips[op])+'</div>');
			};
		}

		if (typeof window.activate_tooltip!='undefined') {
			var callback=function(text) {
				if (ob.hovered) // If still hovered
					activate_tooltip(ob,event,text,'20%',null,'130px');
			};
			get_and_pass_descrip(callback);
		}
	}
}

if (typeof window.tpl_descrips=='undefined')
{
	window.tpl_descrips=[];
	window.tpl_load_url='{$FIND_SCRIPT_NOHTTP;,load_template}';
}

function load_template_previews()
{
	var elements=document.getElementById('f0file').options;
	var ob;
	for (var i=0;i<elements.length;i++)
	{
		ob=elements[i];
		if ((ob.value=='') || (ob.disabled)) continue;

		var op=ob.value;
		ob.onmouseover=make_tooltip_func(ob,op);

		ob.onmouseout=function(ob) { return function() {
			ob.hovered=false;

			if (!event) var event=window.event;
			window.deactivate_tooltip(ob);
		}; }(ob);
	}
}

function preview_generator_mouseover(event)
{
	if (typeof event=='undefined') event=window.event;
	if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'<iframe frameBorder="0" title="{!PREVIEW*;^}" style="width: 800px; height: 400px" src="'+escape_html(this.href)+'">{!PREVIEW;^}</iframe>','800px');
}

function preview_generator_mousemove(event)
{
	if (typeof event=='undefined') event=window.event;
	if (typeof window.activate_tooltip!='undefined') reposition_tooltip(this,event);
}

function preview_generator_mouseout(event)
{
	if (typeof event=='undefined') event=window.event;
	if (typeof window.deactivate_tooltip!='undefined') deactivate_tooltip(this);
}

function load_previews()
{
	var links=document.links;
	for (var i=0;i<links.length;i++)
	{
		if (links[i].id.substr(0,15)=='theme_preview__')
		{
			links[i].onmouseover=preview_generator_mouseover;
			links[i].onmousemove=preview_generator_mousemove;
			links[i].onmouseout=preview_generator_mouseout;
		}
	}
}

function template_edit_page(name,id)
{
	if (typeof window.insert_textbox_wrapping=='undefined') return false;
	if (typeof window.insert_textbox=='undefined') return false;

	var params='';

	var box=document.getElementById('f'+id+'_new');
	var value=document.getElementById(name).value;
	var value_parts=value.split('__');
	value=value_parts[0];

	if (value=='---') return false;

	var ecw=document.getElementById('frame_'+box.name).contentWindow;

	if ((value=='BLOCK')/*{+START,IF,{$NOT,{$CONFIG_OPTION,js_overlays}}}*/ && (typeof window.showModalDialog!='undefined')/*{+END}*/)
	{
		if (ecw) box.value=window.editAreaLoader.getValue(box.name);
		var url='{$FIND_SCRIPT_NOHTTP;,block_helper}?field_name='+box.name+'&block_type=template'+keep_stub();
		window.faux_showModalDialog(
			maintain_theme_in_link(url),
			null,
			'dialogWidth=750;dialogHeight=600;status=no;resizable=yes;scrollbars=yes;unadorned=yes',
			function() {
				if (ecw) window.editAreaLoader.setValue(box.name,box.value);
			}
		);
		return;
	}

	var arity=value_parts[1];
	var definite_gets=0;
	if (arity=='1') definite_gets=1;
	else if (arity=='2') definite_gets=2;
	else if (arity=='3') definite_gets=3;
	else if (arity=='4') definite_gets=4;
	else if (arity=='5') definite_gets=5;
	else if (arity=='0-1') definite_gets=0;
	else if (arity=='3-4') definite_gets=3;
	else if (arity=='0+') definite_gets=0;
	else if (arity=='1+') definite_gets=1;
	var parameter=['A','B','C','D','E','F','G','H','I','J','K'];

	_get_parameter_parameters(
		definite_gets,
		parameter,
		arity,
		box,
		name,
		value,
		0,
		'',
		function(box,name,value,params)
		{
			if (ecw) box.value=window.editAreaLoader.getValue(box.name);

			if (name.indexOf('ppdirective')!=-1)
			{
				insert_textbox_wrapping(box,'{'+'+START,'+value+params+'}','{'+'+END}');
			} else
			{
				var st_value;
				if (name.indexOf('ppparameter')==-1)
				{
					st_value='{'+'$';
				} else
				{
					st_value='{';
				}

				value=st_value+value+'*'+params+'}';

				insert_textbox(box,value);
			}

			if (ecw) window.editAreaLoader.setValue(box.name,box.value);
		}
	);

	return false;
}

function _get_parameter_parameters(definite_gets,parameter,arity,box,name,value,num_done,params,callback)
{
	if (num_done<definite_gets)
	{
		window.fauxmodal_prompt(
			'{!themes:INPUT_NECESSARY_PARAMETER;^}'+', '+parameter[num_done],
			'',
			function(v)
			{
				if (v!==null)
				{
					params=params+','+v;
					_get_parameter_parameters(definite_gets,parameter,arity,box,name,value,num_done+1,params,callback);
				}
			},
			'{!themes:INSERT_PARAMETER;^}'
		);
	} else
	{
		if ((arity=='0+') || (arity=='1+'))
		{
			window.fauxmodal_prompt(
				'{!themes:INPUT_OPTIONAL_PARAMETER;^}',
				'',
				function(v)
				{
					if (v!==null)
					{
						params=params+','+v;
						_get_parameter_parameters(definite_gets,parameter,arity,box,name,value,num_done+1,params,callback);
					} else callback(box,name,value,params);
				},
				'{!themes:INSERT_PARAMETER;^}'
			);
		}
		else if ((arity=='0-1') || (arity=='3-4'))
		{
			window.fauxmodal_prompt(
				'{!themes:INPUT_OPTIONAL_PARAMETER;^}',
				'',
				function(v)
				{
					if (v!=null)
						params=params+','+v;
					callback(box,name,value,params);
				},
				'{!themes:INSERT_PARAMETER;^}'
			);
		}
		else callback(box,name,value,params);
	}
}

function dec_to_hex(number)
{
	var hexbase='0123456789ABCDEF';
	return hexbase.charAt((number>>4)&0xf)+hexbase.charAt(number&0xf);
}

function load_contextual_css_editor(file)
{
	window.doing_css_for=file.replace('.css','');

	var ui=document.getElementById('selectors');
	ui.style.display='block';
	var list=document.createElement('ul');
	document.getElementById('selectors_inner').appendChild(list);
	list.id='selector_list';

	set_up_parent_page_highlighting();

	// Set up background compiles
	if (typeof window.do_ajax_request!='undefined')
	{
		var textarea=document.getElementById('css');
		var last_css=textarea.value;
		window.css_recompiler_timer=window.setInterval(function() {
			if ((window.opener) && (window.opener.document))
			{
				if (typeof window.opener.have_set_up_parent_page_highlighting=='undefined') { set_up_parent_page_highlighting(); last_css='';/*force new CSS to apply*/ }

				var new_value=window.editAreaLoader.getValue('css');
				if (new_value!=last_css)
				{
					var url='{$BASE_URL_NOHTTP;}/data/snippet.php?snippet=css_compile__text'+keep_stub();
					do_ajax_request(url,receive_compiled_css,modsecurity_workaround_ajax('css='+window.encodeURIComponent(new_value)));
				}
				last_css=new_value;
			}
		}, 2000 );
	}
}

function set_up_parent_page_highlighting()
{
	if (typeof window.opener.find_active_selectors=='undefined') return;
	window.opener.have_set_up_parent_page_highlighting=true;

	var li,a,selector,elements,element,j;
	var selectors=window.opener.find_active_selectors(window.doing_css_for,window.opener);
	var list=document.getElementById('selector_list'),cssText;
	set_inner_html(list,'');

	for (var i=0;i<selectors.length;i++)
	{
		selector=selectors[i].selectorText;
		li=document.createElement('li');
		a=document.createElement('a');
		li.appendChild(a);
		a.href='#';
		a.id='selector_'+i;
		set_inner_html(a,escape_html(selector));
		list.appendChild(li);

		cssText=(typeof selectors[i].cssText=='undefined')?selectors[i].style.cssText:selectors[i].cssText;
		if (cssText.indexOf('{')!=-1)
		{
			cssText=cssText.replace(/ \{ /g,' {<br />\n&nbsp;&nbsp;&nbsp;').replace(/; \}/g,'<br />\n}').replace(/; /g,';<br />\n&nbsp;&nbsp;&nbsp;');
		} else // IE
		{
			cssText=cssText.toLowerCase().replace(/; /,';<br />\n');
		}
		li.onmouseout=function(event) { if (typeof event=='undefined') event=window.event; if (typeof window.deactivate_tooltip!='undefined') deactivate_tooltip(this); };
		li.onmousemove=function(event) { if (typeof event=='undefined') event=window.event; if (typeof window.activate_tooltip!='undefined') reposition_tooltip(this,event); };
		li.onmouseover=function(cssText) { return function(event) { if (typeof event=='undefined') event=window.event; if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,cssText,'auto'); } } (cssText);

		// Jump-to
		a.onclick=function(selector) { return function(event) {
			cancel_bubbling(event);
			do_editarea_search('^[ \t]*'+selector.replace(/\./g,'\\.').replace(/\[/g,'\\[').replace(/\]/g,'\\]').replace(/\{/g,'\\{').replace(/\}/g,'\\}').replace(/\+/g,'\\+').replace(/\*/g,'\\*').replace(/\s/g,'[ \t]+')+'\\s*\\{'); // Opera does not support \s
			return false;
		} }(selector);

		// Highlighting on parent page
		a.onmouseover=function(selector) { return function(event) {
			if (typeof event=='undefined') event=window.event;

			if ((window.opener) && (!event.ctrlKey) && (!event.metaKey))
			{
				var elements=find_selectors_for(window.opener,selector);
				for (var i=0;i<elements.length;i++)
				{
					elements[i].style.outline='3px dotted green';
					elements[i].style.backgroundColor='green';
				}
			}
		} }(selector);
		a.onmouseout=function(selector) { return function(event) {
			if (typeof event=='undefined') event=window.event;

			if ((window.opener) && (!event.ctrlKey) && (!event.metaKey))
			{
				var elements=find_selectors_for(window.opener,selector);
				for (var i=0;i<elements.length;i++)
				{
					elements[i].style.outline='';
					elements[i].style.backgroundColor='';
				}
			}
		} }(selector);

		// Highlighting from parent page
		elements=find_selectors_for(window.opener,selector);
		for (j=0;j<elements.length;j++)
		{
			element=elements[j];

			add_event_listener_abstract(element,'mouseover',function(a,element) { return function(event) {
				if (typeof event=='undefined') event=window.event;

				if ((window) && (typeof window.dec_to_hex!='undefined') && (!event.ctrlKey) && (!event.metaKey))
				{
					var target=event.target || event.srcElement;
					var target_distance=0;
					var element_recurse=element;
					do
					{
						if (element_recurse==target) break;
						element_recurse=element_recurse.parentNode;
						target_distance++;
					}
					while (element_recurse);
					if (target_distance>10) target_distance=10; // Max range

					a.style.outline='1px dotted green';
					a.style.background='#00'+(dec_to_hex(255-target_distance*25))+'00';
					if (target_distance>4)
						a.style.color='white';
					else
						a.style.color='black';
				}
			} }(a,element) );
			add_event_listener_abstract(element,'mouseout',function(a) { return function(event) {
				if (typeof event=='undefined') event=window.event;

				if ((window) && (!event.ctrlKey) && (!event.metaKey))
				{
					a.style.outline='';
					a.style.background='';
					a.style.color='';
				}
			} }(a) );
		}
	}
}

function find_selectors_for(opener,selector)
{
	var result=[],result2;
	try
	{
		result2=opener.document.querySelectorAll(selector);
		for (var j=0;j<result2.length;j++) result.push(result2[j]);
	}
	catch (e) {}
	for (var i=0;i<opener.frames.length;i++)
	{
		if (opener.frames[i]) // If test needed for some browsers, as window.frames can get out-of-date
		{
			result2=find_selectors_for(opener.frames[i],selector);
			for (var j=0;j<result2.length;j++) result.push(result2[j]);
		}
	}
	return result;
}

function do_editarea_search(regexp)
{
	var ecw=document.getElementById('frame_css').contentWindow;
	ecw.editArea.execCommand('show_search');
	ecw.document.getElementById('area_search_reg_exp').checked=true;
	ecw.document.getElementById('area_search').value=regexp;
	ecw.editArea.execCommand('area_search');
	ecw.editArea.execCommand('hidden_search');
	window.setTimeout(function() {
		try
		{
			window.scrollTo(0,find_pos_y(document.getElementById('css').parentNode));
		}
		catch (e) {}
	}, 25);

	// Force scroll to bottom, so scrolls up when searching and shows result without scrolling back down
	ecw.document.getElementById('result').scrollTop=ecw.document.getElementById('result').scrollHeight;
	ecw.editArea.scroll_to_view();
}

function receive_compiled_css(ajax_result_frame,win)
{
	if ((typeof win=='undefined') || (!win)) var win=window.opener;

	if (win)
	{
		try
		{
			var css=ajax_result_frame.responseText;

			// Remove old link tag
			var e;
			if (window.doing_css_for=='no_cache')
			{
				e=win.document.getElementById('inline_css');
				if (e)
				{
					e.parentNode.removeChild(e);
				}
			} else
			{
				var links=win.document.getElementsByTagName('link');
				for (var i=0;i<links.length;i++)
				{
					e=links[i];
					if ((e.type=='text/css') && (e.href.indexOf('/templates_cached/'+window.opener.cms_lang+'/'+window.doing_css_for)!=-1))
					{
						e.parentNode.removeChild(e);
					}
				}
			}

			// Create style tag for this
			var style=win.document.getElementById('style_for_'+window.doing_css_for);
			if (!style) style=win.document.createElement('style');
			style.type='text/css';
			style.id='style_for_'+window.doing_css_for;
			if (style.styleSheet)
			{
				style.styleSheet.cssText=css;
			} else
			{
				if (typeof style.childNodes[0]!='undefined') style.removeChild(style.childNodes[0]);
				var tn=win.document.createTextNode(css);
				style.appendChild(tn);
			}
			win.document.getElementsByTagName('head')[0].appendChild(style);

			for (var i=0;i<win.frames.length;i++)
			{
				if (win.frames[i]) // If test needed for some browsers, as window.frames can get out-of-date
				{
					receive_compiled_css(ajax_result_frame,win.frames[i]);
				}
			}
		}
		catch (ex) {}
	}
}
