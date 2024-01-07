/* Validation code and other general code relating to forms */

"use strict";

new Image().src='{$IMG;,loading}'.replace(/^https?:/,window.location.protocol);

function password_strength(ob)
{
	if (ob.name.indexOf('2')!=-1) return;
	if (ob.name.indexOf('confirm')!=-1) return;

	var _ind=document.getElementById('password_strength_'+ob.id);
	if (!_ind) return;
	var ind=_ind.getElementsByTagName('div')[0];
	var post='password='+window.encodeURIComponent(ob.value);
	if (ob.form && typeof ob.form.elements['username']!='undefined')
	{
		post+='&username='+ob.form.elements['username'].value;
	} else
	{
		if (ob.form && typeof ob.form.elements['edit_username']!='undefined')
		{
			post+='&username='+ob.form.elements['edit_username'].value;
		}
	}
	var strength=load_snippet('password_strength',post);
	strength*=2; if (strength>10) strength=10; // Normally too harsh!
	ind.style.width=(strength*10)+'px';
	if (strength>=6)
		ind.style.backgroundColor='green';
	else if (strength<4)
		ind.style.backgroundColor='red';
	else
		ind.style.backgroundColor='orange';
	ind.parentNode.style.display=(ob.value.length==0)?'none':'block';
}

function fix_form_enter_key(form)
{
	var submit=document.getElementById('submit_button');
	var inputs=form.getElementsByTagName('input');
	var type;
	for (var i=0;i<inputs.length;i++)
	{
		type=inputs[i].type;
		if (((type=='text') || (type=='password') || (type=='color') || (type=='email') || (type=='number') || (type=='range') || (type=='search') || (type=='tel') || (type=='url'))
		 && (typeof submit.onclick!='undefined') && (submit.onclick)
		 && ((typeof inputs[i].onkeypress=='undefined') || (!inputs[i].onkeypress)))
			inputs[i].onkeypress=function(event) { if (typeof event=='undefined') event=window.event; if (enter_pressed(event)) submit.onclick(event); };
	}
}

function radio_value(radios)
{
	for (var i=0;i<radios.length;i++)
	{
		if (radios[i].checked) return radios[i].value;
	}
	return '';
}

function get_errormsg_element(id)
{
	var errormsg_element=document.getElementById('error_'+id);
	if (!errormsg_element)
	{
		errormsg_element=document.getElementById('error_'+id.replace(/\_day$/,'').replace(/\_month$/,'').replace(/\_year$/,'').replace(/\_hour$/,'').replace(/\_minute$/,''));
	}
	return errormsg_element;
}

function set_field_error(the_element,error_msg)
{
	if (typeof the_element.name!='undefined')
	{
		var id=the_element.name;
		var errormsg_element=get_errormsg_element(id);
		if ((error_msg=='') && (id.indexOf('_hour')!=-1) || (id.indexOf('_minute')!=-1)) return; // Do not blank out as day/month/year (which comes first) would have already done it
		if (errormsg_element)
		{
			// Make error message visible, if there's an error
			errormsg_element.style.display=(error_msg=='')?'none':'block';

			// Changed error message
			if (get_inner_html(errormsg_element)!=escape_html(error_msg))
			{
				set_inner_html(errormsg_element,'');
				if (error_msg!='') // If there actually an error
				{
					the_element.setAttribute('aria-invalid','true');

					// Need to switch tab?
					var p=errormsg_element;
					while (p!==null)
					{
						p=p.parentNode;
						if ((error_msg.substr(0,5)!='{!DISABLED_FORM_FIELD;^}'.substr(0,5)) && (p) && (typeof p.getAttribute!='undefined') && (p.getAttribute('id')) && (p.getAttribute('id').substr(0,2)=='g_') && (p.style.display=='none'))
						{
							select_tab('g',p.getAttribute('id').substr(2,p.id.length-2),false,true);
							break;
						}
					}

					// Set error message
					var msg_node=document.createTextNode(error_msg);
					errormsg_element.appendChild(msg_node);
					errormsg_element.setAttribute('role','alert');

					// Fade in
					if (typeof window.fade_transition!='undefined')
					{
						set_opacity(errormsg_element,0.0);
						fade_transition(errormsg_element,100,30,4);
					}
				} else
				{
					the_element.setAttribute('aria-invalid','false');
					errormsg_element.setAttribute('role','');
				}
			}
		}
	}
	if ((typeof window.is_wysiwyg_field!='undefined') && (is_wysiwyg_field(the_element))) the_element=the_element.parentNode;
	the_element.className=the_element.className.replace(/( input_erroneous($| ))+/g,' ');
	if (error_msg!='')
	{
		the_element.className=the_element.className+' input_erroneous';
	}
}

function try_to_simplify_iframe_form()
{
	var form_cat_selector=document.getElementById('main_form'),i,element,count=0,found,foundButton;
	if (!form_cat_selector) return;
	var inputs=form_cat_selector.getElementsByTagName('input');
	var buttons=form_cat_selector.getElementsByTagName('button');
	var selects=form_cat_selector.getElementsByTagName('select');
	var textareas=form_cat_selector.getElementsByTagName('select');
	var elements=[];
	for (i=0;i<inputs.length;i++) elements.push(inputs[i]);
	for (i=0;i<buttons.length;i++) elements.push(buttons[i]);
	for (i=0;i<selects.length;i++) elements.push(selects[i]);
	for (i=0;i<textareas.length;i++) elements.push(textareas[i]);
	for (i=0;i<elements.length;i++)
	{
		element=elements[i];
		if (((element.nodeName.toLowerCase()=='input') && (element.getAttribute('type')!='hidden') && (element.getAttribute('type')!='button') && (element.getAttribute('type')!='image') && (element.getAttribute('type')!='submit')) || (element.nodeName.toLowerCase()=='select') || (element.nodeName.toLowerCase()=='textarea'))
		{
			found=element;
			count++;
		}
		if (((element.nodeName.toLowerCase()=='input') && ((element.getAttribute('type')=='button') || (element.getAttribute('type')=='image') || (element.getAttribute('type')=='submit'))) || (element.nodeName.toLowerCase()=='button'))
		{
			foundButton=element;
		}
	}

	if ((count==1) && (found.nodeName.toLowerCase()=='select'))
	{
		var iframe=document.getElementById('iframe_under');
		found.onchange=function() {
			if (iframe)
			{
				if ((iframe.contentDocument) && (iframe.contentDocument.getElementsByTagName('form').length!=0))
				{
					window.fauxmodal_confirm(
						'{!Q_SURE_LOSE;^}',
						function(result)
						{
							if (result)
							{
								_simplified_form_continue_submit(iframe,form_cat_selector);
							}
						}
					);

					return null;
				}
			}

			_simplified_form_continue_submit(iframe,form_cat_selector);

			return null;
		};
		if ((found.getAttribute('size')>1) || (found.multiple)) found.onclick=found.onchange;
		if (iframe)
		{
			foundButton.style.display='none';
		}
	}
}

function _simplified_form_continue_submit(iframe,form_cat_selector)
{
	if (check_form(form_cat_selector))
	{
		if (iframe) animate_frame_load(iframe,'iframe_under');
		form_cat_selector.submit();
	}
}

function do_form_submit(form,event)
{
	if (!check_form(form,false)) return false;

	if ((typeof form.old_action!='undefined') && (form.old_action)) form.setAttribute('action',form.old_action);
	if ((typeof form.old_target!='undefined') && (form.old_target)) form.setAttribute('target',form.old_target);
	if (!form.getAttribute('target')) form.setAttribute('target','_top');

	/* Remove any stuff that is only in the form for previews if doing a GET request */
	if (form.method.toLowerCase()=='get')
	{
		var i=0,name,elements=[];
		for (i=0;i<form.elements.length;i++)
		{
			elements.push(form.elements[i]);
		}
		for (i=0;i<elements.length;i++)
		{
			name=elements[i].name;
			if (name && ((name.substr(0,11)=='label_for__') || (name.substr(0,14)=='tick_on_form__') || (name.substr(0,9)=='comcode__') || (name.substr(0,9)=='require__')))
			{
				elements[i].parentNode.removeChild(elements[i]);
			}
		}
	}

	var visual_commit=function() {
		disable_buttons_just_clicked(document.getElementsByTagName('input'));
		disable_buttons_just_clicked(document.getElementsByTagName('button'));

		if (typeof window.detect_interval!='undefined')
		{
			window.clearInterval(window.detect_interval);
			window.detect_interval=null;
		}
	};

	if (form.onsubmit)
	{
		var ret=form.onsubmit.call(form,event);
		if (!ret)
		{
			if (form.getAttribute('data-modsecurity-workaround')=='1') visual_commit();

			return false;
		}
	}

	if ((typeof window.just_checking_requirements=='undefined') || (!window.just_checking_requirements)) form.submit();

	visual_commit();

	return true;
}

function disable_buttons_just_clicked(inputs,permanent)
{
	if (typeof permanent=='undefined') permanent=false;

	for (var i=0;i<inputs.length;i++)
	{
		if ((inputs[i].nodeName.toLowerCase()=='button') || (inputs[i].type=='image') || (inputs[i].type=='submit') || (inputs[i].type=='button'))
		{
			if ((inputs[i].getAttribute('accesskey')=='u' /* Identifies submit button */) || (inputs[i].getAttribute('accesskey')=='p' /* Identifies preview button */))
			{
				if ((!inputs[i].disabled) && (!inputs[i].under_timer)) /* We do not want to interfere with other code potentially operating */
				{
					disable_button_just_clicked(inputs[i],permanent);
				}
			}
		}
	}
}

function do_form_preview(event,form,preview_url,has_separate_preview)
{
	if (typeof has_separate_preview=='undefined') has_separate_preview=false;

	if (!document.getElementById('preview_iframe'))
	{
		fauxmodal_alert('{!ADBLOCKER;^}');
		return false;
	}

	preview_url+=((typeof window.mobile_version_for_preview=='undefined')?'':('&keep_mobile='+(window.mobile_version_for_preview?'1':'0')));

	var old_action=form.getAttribute('action');

	if (!form.old_action) form.old_action=old_action;
	form.setAttribute('action',/*maintain_theme_in_link - no, we want correct theme images to work*/(preview_url)+((form.old_action.indexOf('&uploading=1')!=-1)?'&uploading=1':''));
	var old_target=form.getAttribute('target');
	if (!old_target) old_target='_top'; /* not _self due to edit screen being a frame itself */
	if (!form.old_target) form.old_target=old_target;
	form.setAttribute('target','preview_iframe');

	if ((window.check_form) && (!check_form(form,true))) return false;

	var visual_commit=function() {
		document.getElementById('submit_button').style.display='inline';
		//window.setInterval(function() { resize_frame('preview_iframe',window.top.scrollY+window.top.get_window_height()); },1500);
		var pf=document.getElementById('preview_iframe');

		/* Do our loading-animation */
		if ((typeof window.just_checking_requirements=='undefined') || (!window.just_checking_requirements))
		{
			window.setInterval(window.trigger_resize,500);  /* In case its running in an iframe itself */
			illustrate_frame_load(pf,'preview_iframe',50);
		}

		disable_buttons_just_clicked(document.getElementsByTagName('input'));
		disable_buttons_just_clicked(document.getElementsByTagName('button'));

		/* input.value not readable on most modern web browsers, and this code is not maintained
		var inputs=form.elements,input;
		for (var i=0;i<inputs.length-1;i++)
		{
			input=inputs[i];
			if ((input.type=='file') && (!input.name.match(/file\d*$/)) && (input.className.indexOf('previews')==-1) && (!input.disabled) && (input.value!=''))
			{
				input.disabled=true;
				window.setTimeout(function() { document.getElementById(input.id).disabled=false; },500);
			}
		}*/

		// Turn main post editing back off
		if (typeof wysiwyg_set_readonly!='undefined') wysiwyg_set_readonly('post',true);
	};

	if (form.onsubmit)
	{
		var test=form.onsubmit.call(form,event,true);
		if (!test)
		{
			if (form.getAttribute('data-modsecurity-workaround')=='1') visual_commit();

			return false;
		}
	}

	if ((has_separate_preview) || (window.has_separate_preview))
	{
		form.setAttribute('action',form.old_action+((form.old_action.indexOf('?')==-1)?'?':'&')+'preview=1');
		return true;
	}

	visual_commit();

	return true;
}

function clever_find_value(form,element)
{
	if ((typeof element.length!='undefined') && (typeof element.nodeName=='undefined'))
	{
		// Radio button
		element=element[0];
	}

	var value;
	switch (element.nodeName.toLowerCase())
	{
		case 'textarea':
			value=(typeof window.get_textbox=='undefined')?element.value:get_textbox(element);
			break;
		case 'select':
			value='';
			if (element.selectedIndex>=0)
			{
				if (element.multiple)
				{
					for (var i=0;i<element.options.length;i++)
					{
						if (element.options[i].selected)
						{
							if (value!='') value+=',';
							value+=element.options[i].value;
						}
					}
				} else if (element.selectedIndex>=0)
				{
					value=element.options[element.selectedIndex].value;
					if ((value=='') && (element.getAttribute('size')>1)) value='-1'; // Fudge, as we have selected something explicitly that is blank
				}
			}
			break;
		case 'input':
			switch (element.type)
			{
				case 'checkbox':
					value=(element.checked)?element.value:'';
					break;

				case 'radio':
					value='';
					for (var i=0;i<form.elements.length;i++)
					{
						if ((form.elements[i].name==element.name) && (form.elements[i].checked))
							value=form.elements[i].value;
					}
					break;

				case 'hidden':
				case 'text':
				case 'color':
				case 'date':
				case 'datetime':
				case 'datetime-local':
				case 'email':
				case 'month':
				case 'number':
				case 'range':
				case 'search':
				case 'tel':
				case 'time':
				case 'url':
				case 'week':
				case 'password':
				default:
					value=element.value;
					break;
			}
	}
	return value;
}

function check_field(the_element,the_form,for_preview)
{
	var i,the_class,required,my_value,erroneous=false,error_msg='',regexp,total_file_size=0,alerted=false;

	// No checking for hidden elements
	if (((the_element.type=='hidden') || (((the_element.style.display=='none') || (the_element.parentNode.style.display=='none') || (the_element.parentNode.parentNode.style.display=='none') || (the_element.parentNode.parentNode.parentNode.style.display=='none')) && ((typeof window.is_wysiwyg_field=='undefined') || (!is_wysiwyg_field(the_element))))) && ((!the_element.className) || (element_has_class(the_element,'hidden_but_needed'))==null))
	{
		return null;
	}
	if (the_element.disabled) return null;

	// Test file sizes
	try {
		if ((the_element.type=='file') && (the_element.files) && (the_element.files.item) && (the_element.files.item(0)) && (the_element.files.item(0).fileSize))
			total_file_size+=the_element.files.item(0).fileSize;
	}
	catch (e) { }; // May produce 'invalid argument' if no files selected, in Microsoft Edge

	// Test file types
	if ((the_element.type=='file') && (the_element.value) && (the_element.name!='file_anytype'))
	{
		var allowed_types='{$VALID_FILE_TYPES;^}'.split(/,/);
		var type_ok=false;
		var theFileType=the_element.value.indexOf('.')?the_element.value.substr(the_element.value.lastIndexOf('.')+1):'{!NONE;^}';
		for (var k=0;k<allowed_types.length;k++)
		{
			if (allowed_types[k].toLowerCase()==theFileType.toLowerCase()) type_ok=true;
		}
		if (!type_ok)
		{
			error_msg='{!INVALID_FILE_TYPE;^,xx1xx,{$VALID_FILE_TYPES}}'.replace(/xx1xx/g,theFileType).replace(/<[^>]*>/g,'').replace(/&[lr][sd]quo;/g,'\'').replace(/,/g,', ');
			if (!alerted) window.fauxmodal_alert(error_msg);
			alerted=true;
		}
	}

	// Fix up bad characters
	if ((browser_matches('ie')) && (the_element.value) && (the_element.nodeName.toLowerCase()!='select'))
	{
		var bad_word_chars=[8216,8217,8220,8221];
		var fixed_word_chars=['\'','\'','"','"'];
		for (i=0;i<bad_word_chars.length;i++)
		{
			regexp=new RegExp(String.fromCharCode(bad_word_chars[i]),'gm');
			the_element.value=the_element.value.replace(regexp,fixed_word_chars[i]);
		}
	}

	// Class name
	the_class=first_class_name(the_element.className);

	// Find whether field is required and value of it
	if (the_element.type=='radio')
	{
		required=(typeof the_form.elements['require__'+the_element.name]!='undefined') && (the_form.elements['require__'+the_element.name].value=='1');
	} else
	{
		required=the_element.className.indexOf('_required')!=-1;
	}
	my_value=clever_find_value(the_form,the_element);

	// Prepare for custom error messages, stored as HTML5 data on the error message display element
	var errormsg_element=(typeof the_element.name=='undefined')?null:get_errormsg_element(the_element.name);

	// Blank?
	if ((required) && ((my_value.replace(/&nbsp;/g,' ').replace(/<br\s*\/?>/g,' ').replace(/\s/g,'')=='') || (my_value=='****') || (my_value==the_element.alt)))
	{
		error_msg='{!REQUIRED_NOT_FILLED_IN;^}';
		if ((errormsg_element) && (errormsg_element.getAttribute('data-errorUnfilled')!=null) && (errormsg_element.getAttribute('data-errorUnfilled')!=''))
			error_msg=errormsg_element.getAttribute('data-errorUnfilled');
	} else
	{
		// Standard field-type checks
		if ((the_element.className.indexOf('date')!=-1) && (the_element.name.match(/\_(day|month|year)$/)) && (my_value!=''))
		{
			var day=the_form.elements[the_element.name.replace(/\_(day|month|year)$/,'_day')].options[the_form.elements[the_element.name.replace(/\_(day|month|year)$/,'_day')].selectedIndex].value;
			var month=the_form.elements[the_element.name.replace(/\_(day|month|year)$/,'_month')].options[the_form.elements[the_element.name.replace(/\_(day|month|year)$/,'_month')].selectedIndex].value;
			var year=the_form.elements[the_element.name.replace(/\_(day|month|year)$/,'_year')].options[the_form.elements[the_element.name.replace(/\_(day|month|year)$/,'_year')].selectedIndex].value;
			var source_date=new Date(year,month-1,day);
			if (year!=source_date.getFullYear()) error_msg='{!javascript:NOT_A_DATE;^}';
			if (month!=source_date.getMonth()+1) error_msg='{!javascript:NOT_A_DATE;^}';
			if (day!=source_date.getDate()) error_msg='{!javascript:NOT_A_DATE;^}';
		}
		if (((the_class=='input_email') || (the_class=='input_email_required')) && (my_value!='') && (!my_value.match(/^[a-zA-Z0-9\._\-\+]+@[a-zA-Z0-9\._\-]+$/)))
		{
			error_msg='{!javascript:NOT_A_EMAIL;^}'.replace('\{1}',my_value);
		}
		if (((the_class=='input_username') || (the_class=='input_username_required')) && (my_value!='') && (window.do_ajax_field_test) && (!do_ajax_field_test('{$FIND_SCRIPT_NOHTTP;,username_exists}?username='+encodeURIComponent(my_value))))
		{
			error_msg='{!javascript:NOT_USERNAME;^}'.replace('\{1}',my_value);
		}
		if (((the_class=='input_codename') || (the_class=='input_codename_required')) && (my_value!='') && (!my_value.match(/^[{$URL_CONTENT_REGEXP_JS}]*$/)))
		{
			error_msg='{!javascript:NOT_CODENAME;^}'.replace('\{1}',my_value);
		}
		if (((the_class=='input_integer') || (the_class=='input_integer_required')) && (my_value!='') && (parseInt(my_value,10)!=my_value-0))
		{
			error_msg='{!javascript:NOT_INTEGER;^}'.replace('\{1}',my_value);
		}
		if (((the_class=='input_float') || (the_class=='input_float_required')) && (my_value!='') && (parseFloat(my_value)!=my_value-0))
		{
			error_msg='{!javascript:NOT_FLOAT;^}'.replace('\{1}',my_value);
		}

		// Shim for HTML5 regexp patterns
		if (the_element.getAttribute('pattern'))
		{
			if ((my_value!='') && (!my_value.match(new RegExp('^'+the_element.getAttribute('pattern')+'$'))))
			{
				error_msg='{!javascript:PATTERN_NOT_MATCHED;^}'.replace('\{1}',my_value);
			}
		}

		// Custom error messages
		if (error_msg!='' && errormsg_element!=null)
		{
			var custom_msg=errormsg_element.getAttribute('data-errorRegexp');
			if ((custom_msg!=null) && (custom_msg!=''))
				error_msg=custom_msg;
		}
	}

	// Show error?
	set_field_error(the_element,error_msg);

	if ((error_msg!='') && (!erroneous))
	{
		erroneous=true;
	}

	return [erroneous,total_file_size,alerted];
}

function check_form(the_form,for_preview)
{
	var delete_element=document.getElementById('delete');
	if ((!for_preview) && (delete_element!=null) && (((first_class_name(delete_element.className)=='input_radio') && (delete_element.value!='0')) || (first_class_name(delete_element.className)=='input_tick')) && (delete_element.checked))
	{
		return true;
	}

	var j,the_element,erroneous=false,total_file_size=0,alerted=false,error_element=null,check_result;
	for (j=0;j<the_form.elements.length;j++)
	{
		if (!the_form.elements[j]) continue;

		if (the_form.elements[j].nodeName.toLowerCase()=='object') continue; // IE9 being weird!

		the_element=the_form.elements[j];

		check_result=check_field(the_element,the_form,for_preview);
		if (check_result!=null)
		{
			erroneous=check_result[0] || erroneous;
			if (!error_element && erroneous) error_element=the_element;
			total_file_size+=check_result[1];
			alerted=check_result[2] || alerted;

			if (check_result[0])
			{
				var auto_reset_error=function(the_element) { return function(event,no_recurse) {
					var check_result=check_field(the_element,the_form,for_preview);
					if ((check_result!=null) && (!check_result[0]))
					{
						set_field_error(the_element,'');
					}

					if ((!no_recurse) && (the_element.className.indexOf('date')!=-1) && (the_element.name.match(/\_(day|month|year)$/)))
					{
						var e=document.getElementById(the_element.id.replace(/\_(day|month|year)$/,'_day'));
						if (e!=the_element) e.onblur(event,true);
						var e=document.getElementById(the_element.id.replace(/\_(day|month|year)$/,'_month'));
						if (e!=the_element) e.onblur(event,true);
						var e=document.getElementById(the_element.id.replace(/\_(day|month|year)$/,'_year'));
						if (e!=the_element) e.onblur(event,true);
					}
				}; };

				if (the_element.getAttribute('type')=='radio')
				{
					for (var i=0;i<the_form.elements.length;i++)
					{
						the_form.elements[i].onchange=auto_reset_error(the_form.elements[i]);
					}
				} else
				{
					the_element.onblur=auto_reset_error(the_element);
				}
			}
		}
	}

	if ((total_file_size>0) && (the_form.elements['MAX_FILE_SIZE']))
	{
		if (total_file_size>the_form.elements['MAX_FILE_SIZE'].value)
		{
			if (!erroneous)
			{
				error_element=the_element;
				erroneous=true;
			}
			if (!alerted)
			{
				window.fauxmodal_alert('{!javascript:TOO_MUCH_FILE_DATA;^}'.replace(new RegExp('\\\\{'+'1'+'\\\\}','g'),Math.round(total_file_size/1024)).replace(new RegExp('\\\\{'+'2'+'\\\\}','g'),Math.round(the_form.elements['MAX_FILE_SIZE'].value/1024)));
			}
			alerted=true;
		}
	}

	if (!the_form.checkValidity())
	{
		erroneous=true;
	}

	if (erroneous && error_element!==null)
	{
		if (!alerted) window.fauxmodal_alert('{!IMPROPERLY_FILLED_IN;^}');
		var posy=find_pos_y(error_element,true);
		if (posy==0)
		{
			posy=find_pos_y(error_element.parentNode,true);
		}
		if (posy!=0)
			smooth_scroll(posy-50,null,null,function() { try { error_element.focus(); } catch(e) {} /* Can have exception giving focus on IE for invisible fields */ });
	}

	// Try and workaround max_input_vars problem if lots of usergroups
	if (!erroneous)
	{
		var delete_e=document.getElementById('delete');
		var is_delete=delete_e && delete_e.type=='checkbox' && delete_e.checked;
		var es=document.getElementsByTagName('select'),e;
		for (var i=0;i<es.length;i++)
		{
			e=es[i];
			if ((e.name.match(/^access_\d+_privilege_/)) && ((is_delete) || (e.options[e.selectedIndex].value=='-1')))
			{
				e.disabled=true;
			}
		}
	}

	return !erroneous;
}

function standard_alternate_fields_within(set_name,something_required)
{
	var form=document.getElementById('set_wrapper_'+set_name);
	while (form.nodeName.toLowerCase()!='form')
	{
		form=form.parentNode;
	}
	var fields=form.elements[set_name];
	var field_names=[];
	for (var i=0;i<fields.length;i++)
	{
		if (typeof fields[i][0]=='undefined')
		{
			if (fields[i].id.match(/^choose\_/))
				field_names.push(fields[i].id.replace(/^choose\_/,''));
		} else
		{
			if (fields[i][0].id.match(/^choose\_/))
				field_names.push(fields[i][0].id.replace(/^choose\_/,''));
		}
	}
	standard_alternate_fields(field_names,something_required);
}

// Do dynamic set_locked/set_required such that one of these must be set, but only one may be
function standard_alternate_fields(field_names,something_required,second_run)
{
	if (typeof second_run=='undefined') second_run=false;

	// Look up field objects
	var fields=[];

	for (var i=0;i<field_names.length;i++)
	{
		var field=_standard_alternate_fields_get_object(field_names[i]);
		fields.push(field);
	}

	// Set up listeners...
	for (var i=0;i<field_names.length;i++)
	{
		var field=fields[i];
		if ((!field) || (typeof field.alternating=='undefined')) // ... but only if not already set
		{
			var self_function=function (e) { standard_alternate_fields(field_names,something_required,true); } ; // We'll re-call ourself on change
			_standard_alternate_field_create_listeners(field,self_function);
		}
	}

	// Update things
	for (var i=0;i<field_names.length;i++)
	{
		var field=fields[i];
		if (_standard_alternate_field_is_filled_in(field,second_run,false))
			return _standard_alternate_field_update_editability(field,fields,something_required);
	}

	// Hmm, force first one chosen then
	for (var i=0;i<field_names.length;i++)
	{
		if (field_names[i]=='')
		{
			var radio_button=document.getElementById('choose_'); // Radio button handles field alternation
			radio_button.checked=true;
			return _standard_alternate_field_update_editability(null,fields,something_required);
		}

		var field=fields[i];
		if ((field) && (_standard_alternate_field_is_filled_in(field,second_run,true)))
			return _standard_alternate_field_update_editability(field,fields,something_required);
	}
}

function _standard_alternate_field_is_filled_in(field,second_run,force)
{
	if (!field) return false; // N/A input is considered unset

	var is_set=force || ((field.value!='') && (field.value!='-1')) || ((typeof field.virtual_value!='undefined') && (field.virtual_value!='') && (field.virtual_value!='-1'));

	var radio_button=document.getElementById('choose_'+(field?field.name:'').replace(/\[\]$/,'')); // Radio button handles field alternation
	if (!radio_button) radio_button=document.getElementById('choose_'+field.name.replace(/\_\d+$/,'_'));
	if (second_run)
	{
		if (radio_button) return radio_button.checked;
	} else
	{
		if (radio_button) radio_button.checked=is_set;
	}
	return is_set;
}

function _standard_alternate_field_create_listeners(field,refreshFunction)
{
	if ((!field) || (typeof field.nodeName!='undefined'))
	{
		__standard_alternate_field_create_listeners(field,refreshFunction);
	} else
	{
		var i;
		for (i=0;i<field.length;i++)
		{
			if (typeof field[i].name!='undefined')
				__standard_alternate_field_create_listeners(field[i],refreshFunction);
		}
		field.alternating=true;
	}
	return null;
}

function __standard_alternate_field_create_listeners(field,refreshFunction)
{
	var radio_button=document.getElementById('choose_'+(field?field.name:'').replace(/\[\]$/,''));
	if (!radio_button) radio_button=document.getElementById('choose_'+field.name.replace(/\_\d+$/,'_'));
	if (radio_button) // Radio button handles field alternation
	{
		add_event_listener_abstract(radio_button,'change',refreshFunction);
	} else // Filling/blanking out handles field alternation
	{
		if (field)
		{
			add_event_listener_abstract(field,'keyup',refreshFunction);
			add_event_listener_abstract(field,'change',refreshFunction);
			field.fakeonchange=refreshFunction;
		}
	}
	if (field) field.alternating=true;
}

function _standard_alternate_fields_get_object(field_name)
{
	// Maybe it's an N/A so no actual field
	if (field_name=='') return null;

	// Try and get direct field
	var field=document.getElementById(field_name);
	if (field) return field;

	// A radio field, so we need to create a virtual field object to return that will hold our value
	var radio_buttons=[],i,j,e; /*JSLINT: Ignore errors*/
	radio_buttons['name']=field_name;
	radio_buttons['value']='';
	for (i=0;i<document.forms.length;i++)
	{
		for (j=0;j<document.forms[i].elements.length;j++)
		{
			e=document.forms[i].elements[j];
			if (!e.name) continue;

			if ((e.name.replace(/\[\]$/,'')==field_name) || (e.name.replace(/\_\d+$/,'_')==field_name))
			{
				radio_buttons.push(e);
				if (e.checked) // This is the checked radio equivalent to our text field, copy the value through to the text field
				{
					radio_buttons['value']=e.value;
				}
				if (e.alternating) radio_buttons.alternating=true;
			}
		}
	}

	if (radio_buttons.length==0) return null;

	return radio_buttons;
}

function _standard_alternate_field_update_editability(chosen,choices,something_required)
{
	for (var i=0;i<choices.length;i++)
	{
		__standard_alternate_field_update_editability(choices[i],chosen,choices[i]!=chosen,choices[i]==chosen,something_required);
	}
}
// NB: is_chosen may only be null if is_locked is false
function __standard_alternate_field_update_editability(field,chosen_field,is_locked,is_chosen,something_required)
{
	if ((!field) || (typeof field.nodeName!='undefined'))
	{
		___standard_alternate_field_update_editability(field,chosen_field,is_locked,is_chosen,something_required);
	} else // List of fields (e.g. radio list, or just because standard_alternate_fields_within was used)
	{
		for (var i=0;i<field.length;i++)
		{
			if (typeof field[i].name!='undefined') // If it is an object, as opposed to some string in the collection
			{
				___standard_alternate_field_update_editability(field[i],chosen_field,is_locked,is_chosen,something_required);
				something_required=false; // Only the first will be required
			}
		}
	}
}
function ___standard_alternate_field_update_editability(field,chosen_field,is_locked,is_chosen,something_required)
{
	if (!field) return;

	var radio_button=document.getElementById('choose_'+field.name.replace(/\[\]$/,''));
	if (!radio_button) radio_button=document.getElementById('choose_'+field.name.replace(/\_\d+$/,'_'));

	set_locked(field,is_locked,chosen_field);
	if (something_required)
	{
		set_required(field.name.replace(/\[\]$/,''),is_chosen);
	}
}

function set_locked(field,is_locked,chosen_ob)
{
	var radio_button=document.getElementById('choose_'+field.name.replace(/\[\]$/,''));
	if (!radio_button) radio_button=document.getElementById('choose_'+field.name.replace(/\_\d+$/,'_'));

	// For All-and-not,Line-multi,Compound-Tick,Radio-List,Date/Time: set_locked assumes that the calling code is clever
	// special input types are coded to observe their master input field readonly status)
	var button=document.getElementById('uploadButton_'+field.name.replace(/\[\]$/,''));

	if (is_locked)
	{
		var labels=document.getElementsByTagName('label'),label=null;
		for (var i=0;i<labels.length;i++)
		{
			if ((chosen_ob) && (labels[i].getAttribute('for')==chosen_ob.id))
			{
				label=labels[i];
				break;
			}
		}
		if (!radio_button)
		{
			if (label)
			{
				var label_nice=get_inner_html(label).replace('&raquo;','').replace(/^\s*/,'').replace(/\s*$/,'');
				if (field.type=='file')
				{
					set_field_error(field,'{!DISABLED_FORM_FIELD_ENCHANCEDMSG_UPLOAD;^}'.replace(/\\{1\\}/,label_nice));
				} else
				{
					set_field_error(field,'{!DISABLED_FORM_FIELD_ENCHANCEDMSG;^}'.replace(/\\{1\\}/,label_nice));
				}
			} else
			{
				set_field_error(field,'{!DISABLED_FORM_FIELD;^}');
			}
		}
		field.className=field.className.replace(/( input_erroneous($| ))+/g,' ');
	} else
	{
		if (!radio_button)
		{
			set_field_error(field,'');
		}
	}
	field.disabled=is_locked;
	if (button)
	{
		button.disabled=is_locked;
		button.style.pointerEvents='none'; // Allows clicking even when disabled
	}
}

function set_required(field_name,is_required)
{
	var radio_button=document.getElementById('choose_'+field_name);

	if (radio_button)
	{
		if (is_required) radio_button.checked=true;
	} else
	{
		var required_a=document.getElementById('form_table_field_name__'+field_name);
		var required_b=document.getElementById('required_readable_marker__'+field_name);
		var required_c=document.getElementById('required_posted__'+field_name);
		var required_d=document.getElementById('form_table_field_input__'+field_name);
		if (is_required)
		{
			if (required_a) required_a.className='form_table_field_name required';
			if (required_d) required_d.className='form_table_field_input';
			if (required_b) required_b.style.display='inline';
			if (required_c) required_c.value=1;
		} else
		{
			if (required_a) required_a.className='form_table_field_name';
			if (required_d) required_d.className='form_table_field_input';
			if (required_b) required_b.style.display='none';
			if (required_c) required_c.value=0;
		}
	}

	var element=document.getElementById(field_name);

	if (element)
	{
		element.className=element.className.replace(/(input\_[a-z\_]+)_required/g,'$1');

		if (typeof element.plupload_object!='undefined')
		{
			element.plupload_object.settings.required=is_required;
		}

		if (is_required) element.className=element.className.replace(/(input\_[a-z\_]+)/g,'$1_required');
	}

	if (!is_required)
	{
		var error=document.getElementById('error__'+field_name);
		if (error) error.style.display='none';
	}
}

// Hide a 'tray' of trs in a form
function toggle_subordinate_fields(pic,help_id)
{
	var new_state,new_state_2,new_state_3,i;
	var field_input=pic.parentNode.parentNode.parentNode;

	var next=field_input.nextSibling;
	if (!next) return;
	while (element_has_class(next,'field_input')!==null) // Sometimes divs or whatever may have erroneously been put in a table by a programmer, skip past them
	{
		next=next.nextSibling;
		if (!next) break;
		if (element_has_class(next,'form_table_field_spacer')) // End of section, so no need to keep going
		{
			next=null;
			break;
		}
	}

	if (((!next) && (pic.src.indexOf('expand')!=-1)) || ((next) && (next.style.display=='none'))) /* Expanding now */
	{
		pic.src=((pic.src.indexOf('themewizard.php')!=-1)?pic.src.replace('expand','contract'):'{$IMG;,1x/trays/contract}').replace(/^https?:/,window.location.protocol);
		if (typeof pic.srcset!='undefined')
			pic.srcset=((pic.srcset.indexOf('themewizard.php')!=-1)?pic.srcset.replace('expand','contract'):'{$IMG;,2x/trays/contract} 2x').replace(/^https?:/,window.location.protocol);
		pic.setAttribute('alt','{!CONTRACT;^}');
		pic.setAttribute('title','{!CONTRACT;^}');
		new_state=(field_input.nodeName.toLowerCase()=='tr')?'table-row':'block';
		new_state_2='block';
		new_state_3='1px dashed';
	} else /* Contracting now */
	{
		pic.src=((pic.src.indexOf('themewizard.php')!=-1)?pic.src.replace('contract','expand'):'{$IMG;,1x/trays/expand}').replace(/^https?:/,window.location.protocol);
		if (typeof pic.srcset!='undefined')
			pic.srcset=((pic.src.indexOf('themewizard.php')!=-1)?pic.srcset.replace('contract','expand'):'{$IMG;,2x/trays/expand} 2x').replace(/^https?:/,window.location.protocol);
		pic.setAttribute('alt','{!EXPAND;^}');
		pic.setAttribute('title','{!EXPAND;^}');
		new_state='none';
		new_state_2='none';
		new_state_3='0';
	}

	// Hide everything until we hit end of section
	var count=0;
	while (field_input.nextSibling!==null)
	{
		field_input=field_input.nextSibling;
		if (typeof field_input.className=='undefined') continue; // E.g. a #text node

		/* Start of next section? */
		if (element_has_class(field_input,'form_table_field_spacer')) break; // End of section

		/* Ok to proceed */
		field_input.style.display=new_state;

		if ((typeof window.fade_transition!='undefined') && (new_state_2!='none') && (count<50/*Performance*/))
		{
			set_opacity(field_input,0.0);
			fade_transition(field_input,100,30,20);
			count++;
		}
	}
	if (typeof help_id=='undefined') help_id=pic.parentNode.id+'_help';
	var help=document.getElementById(help_id);
	while (help!==null)
	{
		help.style.display=new_state_2;
		help=help.nextSibling;
		if ((help) && (help.nodeName.toLowerCase()!='p')) break;
	}

	trigger_resize();
}

function initialise_input_theme_image_entry(name,code)
{
	var stem=name+'_'+code;

	var e=document.getElementById('w_'+stem);
	var img=e.getElementsByTagName('img')[0];
	var input=document.getElementById('j_'+stem);
	var label=e.getElementsByTagName('label')[0];
	var form=input.form;

	e.onkeypress=function(event) {
		if (!event) event=window.event;

		if (entered_pressed(event))
			return e.onclick.call([event]);
		return null;
	};

	var click_func=function(event) {
		if (!event) event=window.event;

		choose_picture('j_'+stem,img,name,event);

		if (typeof window.main_form_very_simple!='undefined') form.submit();

		cancel_bubbling(event);
	}
	img.onkeypress=click_func;
	img.onclick=click_func;
	e.onclick=click_func;

	label.className='js_widget';

	input.onclick=function() {
		if (this.disabled) return;
		if (typeof window.deselect_alt_url!='undefined') deselect_alt_url(this.form);
		if (typeof window.main_form_very_simple!='undefined') this.form.submit();
		cancel_bubbling(event);
	}
}

function choose_picture(j_id,img_ob,name,event)
{
	var j=document.getElementById(j_id);
	if (!j) return;

	if (!img_ob)
	{
		img_ob=document.getElementById('w_'+j_id.substring(2,j_id.length)).getElementsByTagName('img')[0];
		if (typeof img_ob=='undefined') return;
	}

	var e=j.form.elements[name];
	for (var i=0;i<e.length;i++)
	{
		if (e[i].disabled) continue;
		var img=e[i].parentNode.parentNode.getElementsByTagName('img')[0];
		if ((img) && (img!=img_ob))
		{
			if (img.parentNode.className.indexOf(' selected')!=-1)
			{
				img.parentNode.className=img.parentNode.className.replace(/ selected/g,'');
				img.style.outline='0';
				if (!browser_matches('ie8+')) img.style.background='none';
			}
		}
	}

	if (j.disabled) return;
	j.checked=true;
	//if (j.onclick) j.onclick(); causes loop
	if (typeof j.fakeonchange!='undefined' && j.fakeonchange) j.fakeonchange(event);
	img_ob.parentNode.className+=' selected';
	img_ob.style.outline='1px dotted';
}

function preview_mobile_button(ob,event)
{
	if (!event) event=window.event;

	ob.form.action=ob.form.action.replace(/keep_mobile=\d/g,'keep_mobile='+(ob.checked?'1':'0'));
	if (window.parent)
	{
		try {
			window.parent.scrollTo(0,find_pos_y(window.parent.document.getElementById('preview_iframe')));
		}
		catch(e) {}
		window.parent.mobile_version_for_preview=ob.checked;
		window.parent.document.getElementById('preview_button').onclick(event);
		return false;
	}
	ob.form.submit();
	return false;
}

function disable_preview_scripts(under)
{
	if (typeof under=='undefined') under=document;

	var elements,i;
	var no_go=function() {
		window.fauxmodal_alert('{!NOT_IN_PREVIEW_MODE;^}');
		return false;
	};
	elements=under.getElementsByTagName('button');
	for (i=0;i<elements.length;i++)
		elements[i].onclick=no_go;
	elements=under.getElementsByTagName('input');
	for (i=0;i<elements.length;i++)
		if ((elements[i].getAttribute('type')=='button') || (elements[i].getAttribute('type')=='image')) elements[i].onclick=no_go;
	// Make sure links in the preview don't break it - put in a new window
	elements=under.getElementsByTagName('a');
	for (i=0;i<elements.length;i++)
	{
		if (elements[i].href.indexOf('://')!=-1)
		{
			try
			{
				if (((!elements[i].href) || (elements[i].href.toLowerCase().indexOf('javascript:')!=0)) && (elements[i].target!=='_self') && (elements[i].target!=='_blank')) // guard due to weird Firefox bug, JS actions still opening new window
				{
					elements[i].target='false_blank'; // Real _blank would trigger annoying CSS. This is better anyway.
				}
			}
			catch (e) {}; // IE can have security exceptions
		}
	}
}

function _set_up_change_monitor(container,input,container2)
{
	var elements=[];
	if (input)
	{
		elements=[document.getElementById(input)];
	} else
	{
		elements=get_all_form_elements(container);
	}
	if (elements.length>300) return;
	for (var i=0;i<elements.length;i++)
	{
		if (!elements[i]) continue;
		if ((typeof elements[0]=='undefined') || (elements[0].id.indexOf('choose_')!=-1)) continue;
		var func=function() {
			if (find_if_children_set(input?document.getElementById(input).parentNode:container))
			{
				if (container.className.indexOf(' filledin')==-1) container.className+=' filledin';
				if (container2) if (container2.className.indexOf(' filledin')==-1) container2.className+=' filledin';
			} else
			{
				container.className=container.className.replace(/ filledin$/,'');
				if (container2) container2.className=container2.className.replace(/ filledin$/,'');
			}
		};
		add_event_listener_abstract(elements[i],'blur',func );
		add_event_listener_abstract(elements[i],'change',func );
	}
}

function find_if_children_set(container)
{
	var value,blank=true,the_element;
	var elements=get_all_form_elements(container);
	for (var i=0;i<elements.length;i++)
	{
		if (!elements[i]) continue;
		the_element=elements[i];
		if (((the_element.type=='hidden') || ((the_element.style.display=='none') && ((typeof window.is_wysiwyg_field=='undefined') || (!is_wysiwyg_field(the_element))))) && ((!the_element.className) || (!element_has_class(the_element,'hidden_but_needed')))) continue;
		value=clever_find_value(the_element.form,the_element);
		blank=blank && (value=='');
	}
	return !blank;
}

function get_all_form_elements(container)
{
	var i;
	var elements1=container.getElementsByTagName('input');
	var elements2=container.getElementsByTagName('select');
	var elements3=container.getElementsByTagName('textarea');
	var elements=[];
	for (i=0;i<elements1.length;i++) elements.push(elements1[i]);
	for (i=0;i<elements2.length;i++) elements.push(elements2[i]);
	for (i=0;i<elements3.length;i++) elements.push(elements3[i]);
	return elements;
}

function assign_tick_deletion_confirm(name)
{
	document.getElementById(name).onchange=function()
	{
		if (this.checked)
		{
			window.fauxmodal_confirm(
				'{!ARE_YOU_SURE_DELETE;^}',
				function(result)
				{
					var e=document.getElementById(name);
					if (e)
					{
						if (result)
						{
							var form=e.form;
							if (form.action.indexOf('_post')==-1) // Remove redirect if redirecting back, IF it's not just deleting an on-page post (Wiki+)
								form.action=form.action.replace(/([&\?])redirect=[^&]*/,'$1');
						} else
						{
							e.checked=false;
						}
					}
				}
			);
		}
	}
}

function assign_radio_deletion_confirm(name)
{
	for (var i=1;i<3;i++)
	{
		var e=document.getElementById('j_'+name+'_'+i);
		if (e)
		{
			e.onchange=function()
			{
				if (this.checked)
				{
					window.fauxmodal_confirm(
						'{!ARE_YOU_SURE_DELETE;^}',
						function(result)
						{
							var e=document.getElementById('j_'+name+'_0');
							if (e)
							{
								if (result)
								{
									var form=e.form;
									form.action=form.action.replace(/([&\?])redirect=[^&]*/,'$1');
								} else
								{
									e.checked=true; // Check first radio
								}
							}
						}
					);
				}
			}
		}
	}
}

/* Geolocation for address fields */
function geolocate_address_fields()
{
	if (typeof navigator.geolocation!='undefined')
	{
		try
		{
			navigator.geolocation.getCurrentPosition(function(position) {
				var fields=[
					'{!cns_special_cpf:SPECIAL_CPF__cms_street_address;^}',
					'{!cns_special_cpf:SPECIAL_CPF__cms_city;^}',
					'{!cns_special_cpf:SPECIAL_CPF__cms_county;^}',
					'{!cns_special_cpf:SPECIAL_CPF__cms_state;^}',
					'{!cns_special_cpf:SPECIAL_CPF__cms_post_code;^}',
					'{!cns_special_cpf:SPECIAL_CPF__cms_country;^}'
				];

				var geocode_url='{$FIND_SCRIPT;,geocode}';
				geocode_url+='?latitude='+window.encodeURIComponent(position.coords.latitude)+'&longitude='+window.encodeURIComponent(position.coords.longitude);
				geocode_url+=keep_stub();

				do_ajax_request(geocode_url,function(ajax_result) {
					var parsed=JSON.parse(ajax_result.responseText);
					if (parsed===null) return;
					var labels=document.getElementsByTagName('label'),label,field_name,field;
					for (var i=0;i<labels.length;i++)
					{
						label=get_inner_html(labels[i]);
						for (var j=0;j<fields.length;j++)
						{
							if (fields[j].replace(/^.*: /,'')==label)
							{
								if (parsed[j+1]===null) parsed[j+1]='';

								field_name=labels[i].getAttribute('for');
								field=document.getElementById(field_name);
								if (field.nodeName.toLowerCase()=='select')
								{
									field.value=parsed[j+1];
									if (typeof $(field).select2!='undefined') {
										$(field).trigger('change');
									}
								} else
								{
									field.value=parsed[j+1];
								}
							}
						}
					}
				});
			});
		}
		catch (e) {}
	}
}
