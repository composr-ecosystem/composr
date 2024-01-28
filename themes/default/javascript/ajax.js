"use strict";

if (typeof window.AJAX_REQUESTS=='undefined')
{
	window.AJAX_REQUESTS=[];
	window.AJAX_METHODS=[];
}

/*
	Faux frames and faux scrolling
*/

if (typeof window.block_data_cache=='undefined')
{
	window.block_data_cache={};
}

if (typeof window.infinite_scroll_pending=='undefined')
{
	window.infinite_scroll_pending=false; // Blocked due to queued HTTP request
	window.infinite_scroll_blocked=false; // Blocked due to event tracking active
}
function infinite_scrolling_block(event)
{
	if (event.keyCode==35) // 'End' key pressed, so stop the expand happening for a few seconds while the browser scrolls down
	{
		window.infinite_scroll_blocked=true;
		window.setTimeout(function() {
			window.infinite_scroll_blocked=false;
		}, 3000);
	}
}
if (typeof window.infinite_scroll_mouse_held=='undefined')
{
	window.infinite_scroll_mouse_held=false;
}
function infinite_scrolling_block_hold()
{
	if (!window.infinite_scroll_blocked)
	{
		window.infinite_scroll_blocked=true;
		window.infinite_scroll_mouse_held=true;
	}
}
function infinite_scrolling_block_unhold(infinite_scrolling)
{
	if (window.infinite_scroll_mouse_held)
	{
		window.infinite_scroll_blocked=false;
		window.infinite_scroll_mouse_held=false;
		infinite_scrolling();
	}
}
function internalise_infinite_scrolling(url_stem,wrapper,recursive)
{
	if (window.infinite_scroll_blocked || window.infinite_scroll_pending) return false; // Already waiting for a result

	var _pagination=get_elements_by_class_name(wrapper,'pagination');

	if (_pagination.length==0) return false;

	var more_links=[],found_new_links=null;

	for (var _i=0;_i<_pagination.length;_i++)
	{
		var pagination=_pagination[_i];

		if (pagination.style.display!='none')
		{
			// Remove visibility of pagination, now we've replaced with AJAX load more link
			var pagination_parent=pagination.parentNode;
			pagination.style.display='none';
			var num_node_children=0;
			for (var i=0;i<pagination_parent.childNodes.length;i++)
			{
				if (pagination_parent.childNodes[i].nodeName!='#text') num_node_children++;
			}
			if (num_node_children==0) // Remove empty pagination wrapper
			{
				pagination_parent.style.display='none';
			}

			// Add AJAX load more link before where the last pagination control was
				// Remove old pagination_load_more's
				var pagination_load_more=get_elements_by_class_name(wrapper,'pagination_load_more');
				if (pagination_load_more.length>0) pagination_load_more[0].parentNode.removeChild(pagination_load_more[0]);

				// Add in new one
				var load_more_link=document.createElement('div');
				load_more_link.className='pagination_load_more';
				var load_more_link_a=document.createElement('a');
				set_inner_html(load_more_link_a,'{!LOAD_MORE;^}');
				load_more_link_a.href='#';
				load_more_link_a.onclick=function() { internalise_infinite_scrolling_go(url_stem,wrapper,more_links); return false; }; // Click link -- load
				load_more_link.appendChild(load_more_link_a);
				_pagination[_pagination.length-1].parentNode.insertBefore(load_more_link,_pagination[_pagination.length-1].nextSibling);

			more_links=pagination.getElementsByTagName('a');
			found_new_links=_i;
		}
	}
	for (var _i=0;_i<_pagination.length;_i++)
	{
		var pagination=_pagination[_i];
		if (found_new_links===null) // Find links from an already-hidden pagination
		{
			more_links=pagination.getElementsByTagName('a');
			if (more_links.length!=0) break;
		}
	}

	// Is more scrolling possible?
	var rel,found_rel=false;
	for (var i=0;i<more_links.length;i++)
	{
		rel=more_links[i].getAttribute('rel');
		if (rel && rel.indexOf('next')!=-1)
		{
			found_rel=true;
		}
	}
	if (!found_rel) // Ah, no more scrolling possible
	{
		// Remove old pagination_load_more's
		var pagination_load_more=get_elements_by_class_name(wrapper,'pagination_load_more');
		if (pagination_load_more.length>0) pagination_load_more[0].parentNode.removeChild(pagination_load_more[0]);

		return false;
	}

	// Used for calculating if we need to scroll down
	var wrapper_pos_y=find_pos_y(wrapper);
	var wrapper_height=find_height(wrapper);
	var wrapper_bottom=wrapper_pos_y+wrapper_height;
	var window_height=get_window_height();
	var page_height=get_window_scroll_height();
	var scroll_y=get_window_scroll_y();

	// Scroll down -- load
	if ((typeof recursive=='undefined') || (!recursive))
	{
		if ((scroll_y+window_height>wrapper_bottom-window_height*2) && (scroll_y+window_height<page_height-30)) // If within window_height*2 pixels of load area and not within 30 pixels of window bottom (so you can press End key)
		{
			return internalise_infinite_scrolling_go(url_stem,wrapper,more_links);
		}
	}

	return false;
}
function remove_previous_paginations(wrapper)
{
	var _pagination=get_elements_by_class_name(wrapper,'pagination');
	for (var _i=0;_i<_pagination.length;_i++)
	{
		var pagination=_pagination[_i];
		var _more_links=pagination.getElementsByTagName('a');
		var num_links=_more_links.length;
		for (var i=num_links-1;i>=0;i--)
		{
			_more_links[i].parentNode.removeChild(_more_links[i]);
		}
	}
}
function internalise_infinite_scrolling_go(url_stem,wrapper,more_links)
{
	if (window.infinite_scroll_pending) return false;

	var wrapper_inner=document.getElementById(wrapper.id+'_inner');
	if (!wrapper_inner) wrapper_inner=wrapper;

	var rel;
	for (var i=0;i<more_links.length;i++)
	{
		rel=more_links[i].getAttribute('rel');
		if (rel && rel.indexOf('next')!=-1)
		{
			var next_link=more_links[i];
			var url_stub='';

			var matches=next_link.href.match(new RegExp('[&?](start|[^_&]*_start|start_[^_&]*)=([^&]*)'));
			if (matches)
			{
				url_stub+=(url_stem.indexOf('?')==-1)?'?':'&';
				url_stub+=matches[1]+'='+matches[2];
				url_stub+='&raw=1';

				window.infinite_scroll_pending=true;
				remove_previous_paginations(wrapper);

				return call_block(url_stem+url_stub,'',wrapper_inner,true,function() {
					window.infinite_scroll_pending=false;
					internalise_infinite_scrolling(url_stem,wrapper,true);
				});
			}
		}
	}

	return false;
}

function internalise_ajax_block_wrapper_links(url_stem,block_element,look_for,extra_params,append,forms_too,scroll_to_top)
{
	if (!block_element) return;

	if (typeof look_for=='undefined') look_for=[];
	if (typeof extra_params=='undefined') extra_params=[];
	if (typeof append=='undefined') append=false;
	if (typeof forms_too=='undefined') forms_too=false;
	if (typeof scroll_to_top=='undefined') scroll_to_top=true;

	var block_pos_y=find_pos_y(block_element,true);
	if (block_pos_y>get_window_scroll_y()) {
		scroll_to_top=false;
	}

	var _link_wrappers=get_elements_by_class_name(block_element,'ajax_block_wrapper_links');
	if (_link_wrappers.length==0) _link_wrappers=[block_element];
	var links=[];
	for (var i=0;i<_link_wrappers.length;i++)
	{
		var _links=_link_wrappers[i].getElementsByTagName('a');
		for (var j=0;j<_links.length;j++)
			links.push(_links[j]);
		if (forms_too)
		{
			_links=_link_wrappers[i].getElementsByTagName('form');
			for (var j=0;j<_links.length;j++)
				links.push(_links[j]);
			if (_link_wrappers[i].nodeName.toLowerCase()=='form')
				links.push(_link_wrappers[i]);
		}
	}
	for (var i=0;i<links.length;i++)
	{
		if ((links[i].target) && (links[i].target=='_self') && ((!links[i].href) || (links[i].href.substr(0,1)!='#')))
		{
			var submit_func=function()
			{
				var url_stub='';

				var href=(this.nodeName.toLowerCase()=='a')?this.href:this.action;

				// Any parameters matching a pattern must be sent in the URL to the AJAX block call
				for (var j=0;j<look_for.length;j++)
				{
					var matches=href.match(new RegExp('[&\?]('+look_for[j]+')=([^&]*)'));
					if (matches)
					{
						url_stub+=(url_stem.indexOf('?')==-1)?'?':'&';
						url_stub+=matches[1]+'='+matches[2];
					}
				}
				for (var j in extra_params)
				{
					url_stub+=(url_stem.indexOf('?')==-1)?'?':'&';
					url_stub+=j+'='+window.encodeURIComponent(extra_params[j]);
				}

				// Any POST parameters?
				var post_params=null,param;
				if (this.nodeName.toLowerCase()=='form')
				{
					post_params='';
					for (var j=0;j<this.elements.length;j++)
					{
						if (this.elements[j].name)
						{
							param=this.elements[j].name+'='+window.encodeURIComponent(clever_find_value(this,this.elements[j]));

							if ((!this.method) || (this.method.toLowerCase()!='get'))
							{
								if (post_params!='') post_params+='&';
								post_params+=param;
							} else
							{
								url_stub+=(url_stem.indexOf('?')==-1)?'?':'&';
								url_stub+=param;
							}
						}
					}
				}

				if (typeof window.history.pushState!='undefined')
				{
					try
					{
						window.has_js_state=true;
						history.pushState({js: true},document.title,href.replace('&ajax=1','').replace(/&zone=[{$URL_CONTENT_REGEXP_JS}]+/,''));
					}
					catch (e) {}; // Exception could have occurred due to cross-origin error (e.g. "Failed to execute 'pushState' on 'History': A history state object with URL 'https://xxx' cannot be created in a document with origin 'http://xxx'")
				}

				clear_out_tooltips(null);

				// Make AJAX block call
				return call_block(url_stem+url_stub,'',block_element,append,function() { if (scroll_to_top) window.setTimeout(function() { window.scrollTo(0,block_pos_y); }, 25); },false,post_params);
			};
			if (links[i].nodeName.toLowerCase()=='a')
			{
				if (links[i].onclick)
				{
					links[i].onclick=function(old_onclick) { return function(event) { return old_onclick.call(this,event)!==false && submit_func.call(this,event); } }(links[i].onclick);
				} else
				{
					links[i].onclick=submit_func;
				}
			} else
			{
				if (links[i].onsubmit)
				{
					links[i].onsubmit=function(old_onsubmit) { return function(event) { return old_onsubmit.call(this,event)!==false && submit_func.call(this,event); } }(links[i].onsubmit);
				} else
				{
					links[i].onsubmit=submit_func;
				}
			}
		}
	}
}

function guarded_form_submit(form)
{
	if ((!form.onsubmit) || (form.onsubmit())) form.submit();
}

// This function will load a block, with options for parameter changes, and render the results in specified way - with optional callback support
function call_block(url,new_block_params,target_div,append,callback,scroll_to_top_of_wrapper,post_params,inner,show_loading_animation)
{
	if (typeof scroll_to_top_of_wrapper=='undefined') scroll_to_top_of_wrapper=false;
	if (typeof post_params=='undefined') post_params=null;
	if (typeof inner=='undefined') inner=false;
	if (typeof show_loading_animation=='undefined') show_loading_animation=true;
	if ((typeof block_data_cache[url]=='undefined') && (new_block_params!=''))
		block_data_cache[url]=get_inner_html(target_div); // Cache start position. For this to be useful we must be smart enough to pass blank new_block_params if returning to fresh state

	var ajax_url=url;
	if (new_block_params!='') ajax_url+='&block_map_sup='+window.encodeURIComponent(new_block_params);
	if (typeof window.cms_theme!='undefined') ajax_url+='&utheme='+window.cms_theme;
	if (typeof block_data_cache[ajax_url]!='undefined' && post_params==null)
	{
		// Show results from cache
		show_block_html(block_data_cache[ajax_url],target_div,append,inner);
		if (callback) callback();
		return false;
	}

	// Show loading animation
	var loading_wrapper=target_div;
	if ((loading_wrapper.id.indexOf('carousel_')==-1) && (get_inner_html(loading_wrapper).indexOf('ajax_loading_block')==-1) && (show_loading_animation))
	{
		var raw_ajax_grow_spot=get_elements_by_class_name(target_div,'raw_ajax_grow_spot');
		if (typeof raw_ajax_grow_spot[0]!='undefined' && append) loading_wrapper=raw_ajax_grow_spot[0]; // If we actually are embedding new results a bit deeper
		var loading_wrapper_inner=document.createElement('div');
		var position_type=abstract_get_computed_style(loading_wrapper,'position');
		if ((position_type!='relative') && (position_type!='absolute'))
		{
			if (append)
			{
				loading_wrapper_inner.style.position='relative';
			} else
			{
				loading_wrapper.style.position='relative';
			}
		}
		var loading_image=document.createElement('img');
		loading_image.className='ajax_loading_block';
		loading_image.src='{$IMG;,loading}'.replace(/^https?:/,window.location.protocol);
		loading_image.style.position='absolute';
		loading_image.style.zIndex='1000';
		loading_image.style.left=(find_width(target_div)/2-10)+'px';
		if (!append)
		{
			loading_image.style.top=(find_height(target_div)/2-20)+'px';
		} else
		{
			loading_image.style.top=0;
			loading_wrapper_inner.style.height='30px';
		}
		loading_wrapper_inner.appendChild(loading_image);
		loading_wrapper.appendChild(loading_wrapper_inner);
		window.document.body.style.cursor='wait';
	}

	// Make AJAX call
	do_ajax_request(
		ajax_url+keep_stub(),
		function(raw_ajax_result) { // Show results when available
			if (!target_div.parentNode) return; // A prior AJAX result came in and did a set_outer_html, wiping the container

			_call_block_render(raw_ajax_result,ajax_url,target_div,append,callback,scroll_to_top_of_wrapper,inner);
		},
		post_params
	);

	return false;
}

function _call_block_render(raw_ajax_result,ajax_url,target_div,append,callback,scroll_to_top_of_wrapper,inner)
{
	var new_html=raw_ajax_result.responseText;
	block_data_cache[ajax_url]=new_html;

	// Remove loading animation if there is one
	var ajax_loading=get_elements_by_class_name(target_div,'ajax_loading_block');
	if (typeof ajax_loading[0]!='undefined')
	{
		ajax_loading[0].parentNode.parentNode.removeChild(ajax_loading[0].parentNode);
	}
	window.document.body.style.cursor='';

	// Put in HTML
	show_block_html(new_html,target_div,append,inner);

	// Scroll up if required
	if (scroll_to_top_of_wrapper)
	{
		window.setTimeout(function() {
			try
			{
				window.scrollTo(0,find_pos_y(target_div));
			}
			catch (e) {}
		}, 25);
	}

	// Defined callback
	if (callback) callback();
}

function show_block_html(new_html,target_div,append,inner)
{
	var raw_ajax_grow_spot=get_elements_by_class_name(target_div,'raw_ajax_grow_spot');
	if (typeof raw_ajax_grow_spot[0]!='undefined' && append) target_div=raw_ajax_grow_spot[0]; // If we actually are embedding new results a bit deeper
	if (append)
	{
		set_inner_html(target_div,new_html,true,true);
	} else
	{
		if (inner)
		{
			set_inner_html(target_div,new_html);
		} else
		{
			set_outer_html(target_div,new_html);
		}
	}
}

function ajax_form_submit__admin__headless(event,form,block_name,map)
{
	if (typeof window.clever_find_value=='undefined') return true;

	cancel_bubbling(event);

	var post='';
	if (typeof block_name!='undefined')
	{
		if (typeof map=='undefined') map='';
		var comcode='[block'+map+']'+block_name+'[/block]';
		post+='data='+window.encodeURIComponent(comcode);
	}
	for (var i=0;i<form.elements.length;i++)
	{
		if (!form.elements[i].disabled && typeof form.elements[i].name!='undefined' && form.elements[i].name!=null && form.elements[i].name!='')
			post+='&'+form.elements[i].name+'='+window.encodeURIComponent(clever_find_value(form,form.elements[i]));
	}
	var request=do_ajax_request(maintain_theme_in_link('{$FIND_SCRIPT_NOHTTP;,comcode_convert}'+keep_stub(true)),null,post);

	if ((request.responseText!='') && (request.responseText!=''))
	{
		if (request.responseText!='false')
		{
			var result_tags=request.responseXML.documentElement.getElementsByTagName('result');
			if ((result_tags) && (result_tags.length!=0))
			{
				var result=result_tags[0];
				var xhtml=merge_text_nodes(result.childNodes);

				var element_replace=form;
				while (element_replace.className!='form_ajax_target')
				{
					element_replace=element_replace.parentNode;
					if (!element_replace) return true; // Oh dear, target not found
				}

				set_inner_html(element_replace,xhtml);

				window.fauxmodal_alert('{!SUCCESS;^}');

				return false; // We've handled it internally
			}
		}
	}

	return true;
}

/*
	Validation
*/

/* Calls up a URL to check something, giving any 'feedback' as an error (or if just 'false' then returning false with no message) */
function do_ajax_field_test(url,post)
{
	if (typeof window.keep_stub!='undefined') url=url+keep_stub();
	var xmlhttp=do_ajax_request(url,null,post);
	if ((xmlhttp.responseText!='') && (xmlhttp.responseText.replace(/[ \t\n\r]/g,'')!='0'/*some cache layers may change blank to zero*/))
	{
		if (xmlhttp.responseText!='false')
		{
			if (xmlhttp.responseText.length>1000)
			{
				if (typeof window.console!='undefined') console.log(xmlhttp.responseText);

				fauxmodal_alert(xmlhttp.responseText,null,'{!ERROR_OCCURRED;^}',true);
			} else
			{
				window.fauxmodal_alert(xmlhttp.responseText);
			}
		}
		return false;
	}
	return true;
}

/*
	Request backend
*/

function do_ajax_request(url,callback__method,post,timeout) // Note: 'post' is not an array, it's a string (a=b)
{
	var synchronous=!callback__method;

	if (typeof timeout=='undefined') timeout=10000;

	if ((url.indexOf('://')==-1) && (url.substr(0,1)=='/'))
	{
		url=window.location.protocol+'//'+window.location.host+url;
	}

	if ((typeof window.AJAX_REQUESTS=='undefined') || (!window.AJAX_REQUESTS)) return null; // Probably the page is in process of being navigated away so window object is gone

	var index=window.AJAX_REQUESTS.length;
	window.AJAX_METHODS[index]=callback__method;

	var base=window.location.protocol+'//'+window.location.host+'/';
	if ((url.substr(0,base.length)!=base) && (typeof window.XDomainRequest!='undefined') && ((typeof window.XMLHttpRequest!='undefined') && (typeof (new XMLHttpRequest().responseType)=='undefined')))
	{
		window.AJAX_REQUESTS[index]=new XDomainRequest(); // <IE10
	} else
	{
		window.AJAX_REQUESTS[index]=new XMLHttpRequest();
	}
	if (!synchronous) window.AJAX_REQUESTS[index].onreadystatechange=process_request_changes;
	if (typeof post!='undefined' && post!==null)
	{
		if (post.indexOf('&csrf_token')==-1)
			post+='&csrf_token='+window.encodeURIComponent(get_csrf_token()); // For CSRF prevention

		window.AJAX_REQUESTS[index].open('POST',url,!synchronous);
		window.AJAX_REQUESTS[index].setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		if (!synchronous) window.AJAX_REQUESTS[index].timeout=timeout;

		if (('{$VALUE_OPTION,http2_post_fix}'=='1') && (!synchronous)) {
			var img_tmp=new Image();
			img_tmp.onload = function() {
				window.AJAX_REQUESTS[index].send(post);
			};
			img_tmp.src=get_base_url()+'/themes/default/images/blank.gif?rand='+Math.random();
		} else {
			window.AJAX_REQUESTS[index].send(post);
		}
	} else
	{
		window.AJAX_REQUESTS[index].open('GET',url,!synchronous);
		window.AJAX_REQUESTS[index].send(null);
	}

	if ((typeof window.AJAX_REQUESTS=='undefined') || (!window.AJAX_REQUESTS)) return null; // Probably the page is in process of being navigated away so window object is gone
	var result=window.AJAX_REQUESTS[index];
	if (synchronous)
	{
		window.AJAX_REQUESTS[index]=null;
	}
	return result;
}

function process_request_changes()
{
	if ((typeof window.AJAX_REQUESTS=='undefined') || (!window.AJAX_REQUESTS)) return null; // Probably the page is in process of being navigated away so window object is gone

	// If any AJAX_REQUESTS are 'complete'
	var i,result;
	for (i=0;i<window.AJAX_REQUESTS.length;i++)
	{
		result=window.AJAX_REQUESTS[i];
		if ((result!=null) && (result.readyState) && (result.readyState==4/*done*/))
		{
			window.AJAX_REQUESTS[i]=null;

			// If status is 'OK'
			var result_status=result.status;
			if (result_status>10000) result_status=0; // Weird IE status, see http://stackoverflow.com/questions/872206/http-status-code-0-what-does-this-mean-in-ms-xmlhttp/905751#905751
			if ((result_status) && ((result_status==200) || (result_status==500) || (result_status==400) || (result_status==401)))
			{
				// Process the result
				if (window.AJAX_METHODS[i])
				{
					// Text result? Handle with a very simple call
					if ((!result.responseXML/*Not payload handler and not stack trace*/ || result.responseXML.childNodes.length==0))
					{
						return call_ajax_method(window.AJAX_METHODS[i],result,null);
					}
				}

				var xml=handle_errors_in_result(result);
				if (xml)
				{
					// XML result. Handle with a potentially complex call
					xml.validateOnParse=false;
					var ajax_result_frame=xml.documentElement;
					if (!ajax_result_frame) ajax_result_frame=xml;
					process_request_change(ajax_result_frame,i);
				} else
				{
					// Error parsing
					if (window.AJAX_METHODS[i])
					{
						call_ajax_method(window.AJAX_METHODS[i],null,null);
					}
				}
			}
			else
			{
				// HTTP error...

				if (window.AJAX_METHODS[i])
				{
					call_ajax_method(window.AJAX_METHODS[i],null,null);
				}

				try
				{
					if (result_status==0) // 0 implies site down, or network down
					{
						if (!window.network_down)
						{
							//window.fauxmodal_alert('{!NETWORK_DOWN;^}');	Annoying because it happens when unsleeping a laptop (for example)
							window.network_down=true;
						}
					} else
					{
						if (typeof console.log!='undefined') console.log('{!PROBLEM_RETRIEVING_XML;^}\n'+result_status+': '+result.statusText+'.');
					}
				}
				catch (e)
				{
					if (typeof console.log!='undefined') console.log('{!PROBLEM_RETRIEVING_XML;^}');		// This is probably clicking back
				}
			}
		}
	}
}

function handle_errors_in_result(result)
{
	if ((result.responseXML==null) || (result.responseXML.childNodes.length==0))
	{
		// Try and parse again. Firefox can be weird.
		var xml;
		if (typeof DOMParser!='undefined')
		{
			try { xml=(new DOMParser()).parseFromString(result.responseText,'application/xml'); }
			catch(e) {}
		} else
		{
			var ieDOM=['MSXML2.DOMDocument','MSXML.DOMDocument','Microsoft.XMLDOM'];
			for (var i=0;i<ieDOM.length && !xml;i++) {
				try { xml=new ActiveXObject(ieDOM[i]);xml.loadXML(result.responseText); }
				catch(e) {}
			}
		}
		if (xml) return xml;

		if ((result.responseText) && (result.responseText!='') && (result.responseText.indexOf('<html')!=-1))
		{
			if (typeof window.console!='undefined') console.log(result);

			fauxmodal_alert(result.responseText,null,'{!ERROR_OCCURRED;^}',true);
		}
		return false;
	}
	return result.responseXML;
}

function process_request_change(ajax_result_frame,i)
{
	if (!ajax_result_frame) return null; // Needed for Opera
	if ((typeof window.AJAX_REQUESTS=='undefined') || (!window.AJAX_REQUESTS)) return null; // Probably the page is in process of being navigated away so window object is gone

	var method=null;
	if ((ajax_result_frame.getElementsByTagName('method')[0]) || (window.AJAX_METHODS[i]))
	{
		method=(ajax_result_frame.getElementsByTagName('method')[0])?eval('return '+merge_text_nodes(ajax_result_frame.getElementsByTagName('method')[0])):window.AJAX_METHODS[i];
	}

	if (ajax_result_frame.getElementsByTagName('message')[0])
	{
		// Either an error or a message was returned. :(
		var message=ajax_result_frame.getElementsByTagName('message')[0].firstChild.data;

		call_ajax_method(method,null,null);

		if (ajax_result_frame.getElementsByTagName('error')[0])
		{
			// It's an error :|
			window.fauxmodal_alert('An error ('+ajax_result_frame.getElementsByTagName('error')[0].firstChild.data+') message was returned by the server: '+message);
			return null;
		}

		window.fauxmodal_alert('An informational message was returned by the server: '+message);
		return null;
	}

	var ajax_result=ajax_result_frame.getElementsByTagName('result')[0];
	if (!ajax_result)
	{
		call_ajax_method(method,null,null);
		return null;
	}

	call_ajax_method(method,ajax_result_frame,ajax_result);

	return null;
}

function call_ajax_method(method,ajax_result_frame,ajax_result)
{
	if (method instanceof Array)
	{
		if (ajax_result_frame!=null)
		{
			method=(typeof method[0]=='undefined')?null:method[0];
		} else
		{
			method=(typeof method[1]=='undefined')?null:method[1];
		}
	} else
	{
		if (ajax_result_frame==null) method=null; // No failure method given, so don't call
	}

	if (method!=null)
	{
		if (typeof method.response!='undefined')
		{
			method.response(ajax_result_frame,ajax_result);
		} else
		{
			method(ajax_result_frame,ajax_result);
		}
	}
}

function merge_text_nodes(childNodes)
{
	var i,text='';
	for (i=0;i<childNodes.length;i++)
	{
		if (childNodes[i].nodeName=='#text')
		{
			text+=childNodes[i].data;
		}
	}
	return text;
}

