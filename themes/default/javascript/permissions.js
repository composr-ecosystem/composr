"use strict";

function permissions_toggle(cell)
{
	var index=cell.cellIndex;
	var table=cell.parentNode.parentNode;
	if (table.nodeName.toLowerCase()!='table') table=table.parentNode;
	var state_list=null,state_checkbox=null;
	for (var i=0;i<table.rows.length;i++)
	{
		if (i>=1)
		{
			var cell2=table.rows[i].cells[index];
			var input=cell2.getElementsByTagName('input')[0];
			if (input)
			{
				if (!input.disabled)
				{
					if (state_checkbox==null) state_checkbox=input.checked;
					input.checked=!state_checkbox;
				}
			} else
			{
				input=cell2.getElementsByTagName('select')[0];
				if (state_list==null) state_list=input.selectedIndex;
				input.selectedIndex=((state_list!=input.options.length-1)?(input.options.length-1):(input.options.length-2));
				input.disabled=false;

				permissions_overridden(table.rows[i].id.replace(/_privilege_container$/,''));
			}
		}
	}
}

function fade_icons_out()
{
	var icons=get_elements_by_class_name(document,'perm_icon');
	for (var i=0;i<icons.length;i++)
	{
		set_opacity(icons[i],1.0);
		if (typeof window.fade_transition!='undefined') fade_transition(icons[i],20,50,-10);
	}
}

function fade_icons_in()
{
	var icons=get_elements_by_class_name(document,'perm_icon');
	for (var i=0;i<icons.length;i++)
	{
		if (typeof window.fade_transition!='undefined') fade_transition(icons[i],100,50,10);
	}
}

function show_permission_setting(ob,event)
{
	if (ob.disabled) return; // already showing default in disabled dropdown
	if (ob.done) return;
	ob.done=true;

	if (!ob.full_setting)
	{
		var serverid;

		if (typeof window.sitemap!='undefined')
		{
			var value=document.getElementById('tree_list').value;

			if (value.indexOf(',')!=-1) return; // Can't find any single value, as multiple resources are selected

			var node=window.sitemap.getElementByIdHack(value);
			serverid=node.getAttribute('serverid');
		} else
		{
			serverid=window.perm_serverid+':_new_';
		}

		var url='{$FIND_SCRIPT_NOHTTP;,find_permissions}?serverid='+window.encodeURIComponent(serverid)+'&x='+window.encodeURIComponent(ob.name);
		do_ajax_request(url+keep_stub(),function(ret) {
			if (!ret) return;
			ob.full_setting=ret.responseText;
			ob.title+=' [{!permissions:DEFAULT_PERMISSION;^} '+ob.full_setting+']';
		});
		return;
	}

	ob.title+=' [{!permissions:DEFAULT_PERMISSION;^} '+ob.full_setting+']';
}

function cleanup_permission_list(name)
{
	// We always try and cleanup the 'custom' option if we're choosing something else (because it's confusing for it to stay there)
	var custom_option=document.getElementById(name+'_custom_option');
	if (custom_option) custom_option.parentNode.removeChild(custom_option);
}

function permissions_overridden(select)
{
	var element=document.getElementById(select+'_presets');
	if (element.options[0].id!=select+'_custom_option')
	{
		var new_option=document.createElement('option');
		set_inner_html(new_option,'{!permissions:PINTERFACE_LEVEL_CUSTOM;^}');
		new_option.id=select+'_custom_option';
		new_option.value='';
		element.insertBefore(new_option,element.options[0]);
	}
	element.selectedIndex=0;
}

function copy_permission_presets(name,value,just_track)
{
	if (value=='') return false;

	var made_change=false;

	var usual_suspects=['bypass_validation_xrange_content','edit_xrange_content','edit_own_xrange_content','delete_xrange_content','delete_own_xrange_content','submit_xrange_content','edit_cat_xrange_content'];
	var access=[2,3,2,3,2,1,3]; // The minimum access level that turns on each of the above permissions   NB: Also defined in resource_fs.php, so keep that in-sync

	var holder=document.getElementById(name+'_privilege_container');
	var elements=holder.getElementsByTagName('select');
	var i,j,test,stub=name+'_privilege_',name2,x;

	var node=null;
	if (typeof window.sitemap!='undefined') node=window.sitemap.getElementByIdHack(document.getElementById('tree_list').value.split(',')[0]);

	if (value!='-1')
	{
		for (i=0;i<elements.length;i++)
		{
			if (elements[i].name.indexOf('presets')!=-1) continue;

			if (typeof window.sitemap=='undefined') elements[i].disabled=false;
			test=-1;
			name2=elements[i].name.substr(stub.length);
			x=name2.replace(/(high|mid|low)/,'x');

			for (j=0;j<usual_suspects.length;j++)
			{
				if (usual_suspects[j]==x)
				{
					test=(access[j]<=window.parseInt(value))?1:0;
					break;
				}
			}
			if ((test!=-1) || ((node) && (node.getAttribute('serverid')!='_root')))
			{
				if (elements[i].selectedIndex!=test+1)
				{
					made_change=true;
					if (elements[i].selectedIndex!=test+1)
					{
						made_change=true;
						if (!just_track) elements[i].selectedIndex=test+1; // -1 is at index 0
					}
				}
			}
		}
	} else
	{
		for (i=0;i<elements.length;i++)
		{
			if (elements[i].name.indexOf('presets')!=-1) continue;

			if (typeof window.sitemap=='undefined') elements[i].disabled=true;
			// Any disabled ones will be set to show the default permission rather than the "use-default" one, WHILST all-global is on
			elements[i].selectedIndex=eval(elements[i].name+';')+1; // -1 is at index 0
		}
	}

	if ((!just_track) && (elements.length==2) && (made_change))
	{
		window.fauxmodal_alert('{!permissions:JUST_PRESETS;^}');
	}

	return made_change;
}

function setup_privilege_override_selector(name,default_access,privilege,title,all_global)
{
	eval('window.'+name+'_privilege_'+privilege+'='+default_access);
	var select_element=document.getElementById(name+'_privilege_'+privilege);
	if (all_global)
	{
		// Any disabled ones will be set to show the default permission rather than the "use-default" one, WHILST all-global is on
		select_element.selectedIndex=eval(name+'_privilege_'+privilege)+1; // -1 is at index 0
		if (typeof window.sitemap=='undefined') select_element.disabled=true;
	}
}

function permission_repeating(button,name)
{
	var old_permission_copying=window.permission_copying;

	var tr=button.parentNode.parentNode;
	var trs=tr.parentNode.getElementsByTagName('tr');

	if (window.permission_copying) // Undo current copying
	{
		var prior_button=document.getElementById('copy_button_'+window.permission_copying);
		prior_button.className=prior_button.className.replace(/ active_repeating/g, '');
		window.permission_copying=null;
		for (var i=0;i<trs.length;i++)
		{
			trs[i].onclick=function() {};
		}
	}

	if (old_permission_copying!=name) // Starting a new copying session
	{
		button.className+=' active_repeating';
		window.permission_copying=name;
		window.fauxmodal_alert('{!permissions:REPEAT_PERMISSION_NOTICE;^}');
		for (var i=0;i<trs.length;i++)
		{
			if (trs[i]!=tr) trs[i].onclick=copy_permissions_function(trs[i],tr,name);
		}
	}
}

function copy_permissions_function(to_row,from_row,name)
{
	return function()
	{
		var inputs_to=to_row.getElementsByTagName('input');
		var inputs_from=from_row.getElementsByTagName('input');
		for (var i=0;i<inputs_to.length;i++)
		{
			inputs_to[i].checked=inputs_from[i].checked;
		}
		var selects_to=to_row.getElementsByTagName('select');
		var selects_from=from_row.getElementsByTagName('select');
		for (var i=0;i<selects_to.length;i++)
		{
			while (selects_to[i].options.length>0)
			{
				selects_to[i].remove(0);
			}
			for (var j=0;j<selects_from[i].options.length;j++)
			{
				selects_to[i].add(selects_from[i].options[j].cloneNode(true),null);
			}
			selects_to[i].selectedIndex=selects_from[i].selectedIndex;
			selects_to[i].disabled=selects_from[i].disabled;
		}
	}
}

// =========================================
// These are for the Permissions Tree Editor
// =========================================

// Selection changed, so update box
function update_permission_box(setting)
{
	if (typeof window.sitemap=='undefined') return;

	if (setting.value=='')
	{
		document.getElementById('selection_form_fields').style.display='none';
		document.getElementById('selection_button').disabled=true;
		set_inner_html(document.getElementById('selection_message'),'{!permissions:PERMISSIONS_TREE_EDITOR_NONE_SELECTED;^}');
	} else
	{
		// Go through and set maximum permissions/override from those selected
		var values=setting.value.split(',');
		var id,name,value,i,node,j,group,element,privilege,privilege_title,known_groups=[],known_privileges=[],k,html,new_option,num_privilege_default,num_privilege,ths,tds,cells,new_cell,row;
		var matrix=document.getElementById('enter_the_matrix').getElementsByTagName('table')[0];
		var num_privilege_total=0;
		var is_cms=null;
		var rows=matrix.getElementsByTagName('tr');
		var done_header=false;
		for (i=0;i<values.length;i++) // For all items that we are loading permissions for (we usually just do it for one, but sometimes we load whole sets if we are batch setting permissions)
		{
			node=window.sitemap.getElementByIdHack(values[i]);

			if (i==0) // On first iteration we do a cleanup
			{
				// Find usergroups
				for (j=0;j<node.attributes.length;j++)
				{
					if (node.attributes[j].name.substr(0,7)=='g_view_')
					{
						group=node.attributes[j].name.substr(7);
						known_groups.push(group);
					}
				}

				// Blank out everything in the permissions box, remove all privileges
				for (j=0;j<known_groups.length;j++)
				{
					group=known_groups[j];
					element=document.getElementById('access_'+group);
					element.checked=false;
					element=document.getElementById('access_'+group+'_presets');
					if (element.options[0].id!='access_'+group+'_custom_option')
					{
						new_option=document.createElement('option');
						set_inner_html(new_option,'{!permissions:PINTERFACE_LEVEL_CUSTOM;^}');
						new_option.id='access_'+group+'_custom_option';
						new_option.value='';
						element.insertBefore(new_option,element.options[0]);
					}
					element.selectedIndex=0;

					// Delete existing privilege nodes
					ths=matrix.getElementsByTagName('th');
					tds=matrix.getElementsByTagName('td');
					cells=[];
					for (k=0;k<ths.length;k++) cells.push(ths[k]);
					for (k=0;k<tds.length;k++) cells.push(tds[k]);
					for (k=0;k<cells.length;k++)
					{
						if ((cells[k].className.match(/(^|\s)privilege\_header($|\s)/)) || (cells[k].className.match(/(^|\s)privilege\_footer($|\s)/)) || (cells[k].className.match(/(^|\s)privilege\_cell($|\s)/)))
						{
							cells[k].parentNode.removeChild(cells[k]);
						}
					}
				}
			}

			if ((node.getAttribute('serverid').indexOf(':cms_')!=-1) && (is_cms!==false)) is_cms=true; else is_cms=false;

			// Set view access
			for (j=0;j<node.attributes.length;j++)
			{
				if (node.attributes[j].name.substr(0,7)=='g_view_')
				{
					group=node.attributes[j].name.substr(7);
					element=document.getElementById('access_'+group);
					if (!element.checked)
					{
						element.checked=(node.attributes[j].value=='true');
					}
					element=document.getElementById('access_'+group);
				}
			}
			var form=document.getElementById('permissions_form');
			var no_view_settings=(node.getAttribute('serverid')=='_root') || (node.getAttribute('serverid').substr(0,22)=='cms:cms_comcode_pages:');
			for (j=0;j<form.elements.length;j++)
			{
				element=form.elements[j];
				if (element.id.substr(0,7)=='access_')
				{
					element.style.display=((values.length==1) && (no_view_settings))?'none':'inline';
					element.disabled=(element.name=='_ignore') || ((values.length==1) && (no_view_settings));
				}
			}

			// Create privilege nodes
			num_privilege=0;
			known_privileges=[];
			id=node.getAttribute('id');
			if (typeof window.attributes_full=='undefined') window.attributes_full=[];
			if (typeof window.attributes_full[id]=='undefined') window.attributes_full[id]=node.attributes;
			for (name in window.attributes_full[id])
			{
				value=window.attributes_full[id][name];
				if (name.substr(0,'privilege_'.length)=='privilege_')
				{
					privilege=name.substr('privilege_'.length);
					privilege_title=value;
					done_header=false;
					for (k=0;k<rows.length;k++)
					{
						if (rows[k].id.substr(0,7)!='access_') continue;

						group=rows[k].id.substring(7,rows[k].id.indexOf('_privilege_container'));

						element=document.getElementById('access_'+group+'_privilege_'+privilege);
						if ((!element) && (!document.getElementById('privilege_cell_'+group+'_'+privilege))) // We haven't added it yet for one of the resources we're doing permissions for
						{
							if ((!done_header) && (!document.getElementById('privilege_header_'+privilege)))
							{
								row=rows[0];
								new_cell=row.insertBefore(document.createElement('th'),row.cells[row.cells.length]);
								new_cell.className='privilege_header';
								new_cell.id='privilege_header_'+privilege;
								set_inner_html(new_cell,'<img class="gd_text" src="'+'{$BASE_URL*;,0}'.replace(/^https?:/,window.location.protocol)+'/data/gd_text.php?color='+window.column_color+'&amp;text='+window.encodeURIComponent(privilege_title)+escape_html(keep_stub())+'" title="'+escape_html(privilege_title)+'" alt="'+escape_html(privilege_title)+'" />');

								rows[rows.length-1].appendChild(document.createElement('td')).className='form_table_field_input privilege_footer'; // Footer cell

								num_privilege_total++;

								done_header=true;
							}

							// Manually build up cell
							row=document.getElementById('access_'+group+'_privilege_container');
							new_cell=row.insertBefore(document.createElement('td'),row.cells[row.cells.length-1]);
							new_cell.className='form_table_field_input privilege_cell';
							new_cell.id='privilege_cell_'+group+'_'+privilege;
							if (document.getElementById('access_'+group).name!='_ignore')
							{
								set_inner_html(new_cell,'<div class="accessibility_hidden"><label for="access_'+group+'_privilege_'+privilege+'">{!permissions:OVERRIDE;^}</label></div><select title="'+escape_html(privilege_title)+'" onmouseover="if (this.options[this.selectedIndex].value==\'-1\') show_permission_setting(this,event);" id="access_'+group+'_privilege_'+privilege+'" name="access_'+group+'_privilege_'+privilege+'"><option selected="selected" value="-1">/</option><option value="0">{!permissions:NO_COMPACT;^}</option><option value="1">{!permissions:YES_COMPACT;^}</option></select>');

								element=document.getElementById('access_'+group+'_privilege_'+privilege);

								setup_privilege_override_selector('access_'+group,'-1',privilege,privilege_title,false);
							}
						}
						if (element)
							element.options[0].disabled=((values.length==1) && (node.getAttribute('serverid')=='_root'));
					}
					known_privileges.push(privilege);
					num_privilege++;
				}
			}

			// Set privileges for all usergroups (to highest permissions from all usergroups selected)
			for (name in window.attributes_full[id])
			{
				value=window.attributes_full[id][name];
				if (name.substr(0,'group_privileges_'.length)=='group_privileges_')
				{
					group=name.substr(name.lastIndexOf('_')+1);
					privilege=name.substr('group_privileges_'.length,name.length-group.length-1-('group_privileges_'.length));
					element=document.getElementById('access_'+group+'_privilege_'+privilege);
					if (element.selectedIndex<window.parseInt(value)+1)
						element.selectedIndex=window.parseInt(value)+1; // -1 corresponds to 0.
				}
			}

			// Blank out any rows of defaults
			for (k=0;k<known_groups.length;k++)
			{
				group=known_groups[k];
				num_privilege_default=0;
				for (j=0;j<known_privileges.length;j++)
				{
					element=document.getElementById('access_'+group+'_privilege_'+known_privileges[j]);
					if (element.selectedIndex==0) num_privilege_default++;
				}
				if (num_privilege_default==num_privilege)
				{
					element=document.getElementById('access_'+group+'_presets');
					element.selectedIndex=1;
					cleanup_permission_list('access_'+group);
					for (j=0;j<known_privileges.length;j++)
					{
						element=document.getElementById('access_'+group+'_privilege_'+known_privileges[j]);
						if (typeof window.sitemap=='undefined') element.disabled=true;
					}
				}
			}

			// Hide certain things if we only have view settings here, else show them
			if (num_privilege_total==0)
			{
				set_inner_html(matrix.getElementsByTagName('tr')[0].cells[0],'{!USERGROUP;^}');
				for (k=0;k<known_groups.length;k++)
				{
					document.getElementById('access_'+known_groups[k]+'_presets').style.display='none';
					var button=document.getElementById('access_'+known_groups[k]+'_privilege_container').getElementsByTagName('button')[0];
					if (typeof button!='undefined') button.disabled=true;
				}
			} else
			{
				set_inner_html(matrix.getElementsByTagName('tr')[0].cells[0],'<span class="heading_group">{!USERGROUP;^}</span> <span class="heading_presets">{!permissions:PINTERFACE_PRESETS;^}</span>');
				for (k=0;k<known_groups.length;k++)
				{
					document.getElementById('access_'+known_groups[k]+'_presets').style.display='block';
					var button=document.getElementById('access_'+known_groups[k]+'_privilege_container').getElementsByTagName('button')[0];
					if (typeof button!='undefined') button.disabled=false;
				}
			}

			// Test to see what we wouldn't have to make a change to get - and that is what we're set at
			for (k=0;k<known_groups.length;k++)
			{
				group=known_groups[k];
				var list=document.getElementById('access_'+group+'_presets');
				if (!copy_permission_presets('access_'+group,'0',true)) list.selectedIndex=list.options.length-4;
				else if (!copy_permission_presets('access_'+group,'1',true)) list.selectedIndex=list.options.length-3;
				else if (!copy_permission_presets('access_'+group,'2',true)) list.selectedIndex=list.options.length-2;
				else if (!copy_permission_presets('access_'+group,'3',true)) list.selectedIndex=list.options.length-1;
			}
		}

		// Set correct admin colspan
		for (var i=0;i<matrix.rows.length;i++)
		{
			if (matrix.rows[i].cells.length==3) {
				matrix.rows[i].cells[2].colSpan=num_privilege_total+1;
			}
		}

		document.getElementById('selection_form_fields').style.display='block';
		document.getElementById('selection_button').disabled=false;
		set_inner_html(document.getElementById('selection_message'),(values.length<=1)?'{!permissions:PERMISSIONS_TREE_EDITOR_ONE_SELECTED;^}':'{!permissions:PERMISSIONS_TREE_EDITOR_MULTI_SELECTED;^}');
	}
}

// Saving
function set_permissions(setting)
{
	if (typeof window.sitemap=='undefined') return;
	if (typeof window.do_ajax_request=='undefined') return;

	if (setting.value=='')
	{
		return; // Shouldn't get here
	} else
	{
		// Go through and set maximum permissions/override from those selected
		var values=setting.value.split(',');
		var id,i,node,j,group,element,privilege,known_groups=[],k,serverid,set_request='',set_request_b,new_value;
		for (i=0;i<values.length;i++)
		{
			node=window.sitemap.getElementByIdHack(values[i]);
			serverid=node.getAttribute('serverid');

			// Find usergroups
			for (j=0;j<node.attributes.length;j++)
			{
				if (node.attributes[j].name.substr(0,7)=='g_view_')
				{
					group=node.attributes[j].name.substr(7);
					known_groups.push(group);
				}
			}

			set_request_b='';

			for (j=0;j<known_groups.length;j++)
			{
				group=known_groups[j];

				// Set view access
				element=document.getElementById('access_'+group);
				new_value=element.checked?'true':'false';
				if (new_value!=node.getAttribute('g_view_'+group))
				{
					node.setAttribute('g_view_'+group,new_value);
					set_request_b=set_request_b+'&'+i+'g_view_'+group+'='+((new_value=='true')?1:0);
				}
			}

			// Set privileges for all usergroups
			id=node.getAttribute('id');
			if (typeof window.attributes_full=='undefined') window.attributes_full=[];
			if (typeof window.attributes_full[id]=='undefined') window.attributes_full[id]=node.attributes;
			for (var name in window.attributes_full[id])
			{
				var value=window.attributes_full[id][name];
				if (name.substr(0,'privilege_'.length)=='privilege_')
				{
					for (j=0;j<known_groups.length;j++)
					{
						group=known_groups[j];
						privilege=name.substr('privilege_'.length);
						value=window.attributes_full[id]['group_privileges_'+privilege+'_'+group];
						if (!value) value=-1;
						element=document.getElementById('access_'+group+'_privilege_'+privilege);
						if (element)
						{
							new_value=element.selectedIndex-1;
							if (new_value!=value)
							{
								window.attributes_full[id]['group_privileges_'+privilege+'_'+group]=new_value;
								set_request_b=set_request_b+'&'+i+'group_privileges_'+privilege+'_'+group+'='+new_value;
							}
						}
					}
				}

				// Update UI indicators
				set_inner_html(document.getElementById('tree_listextra_'+id),permissions_img_func_1(node,id)+permissions_img_func_2(node,id));
			}

			if (set_request_b!='') set_request=set_request+'&map_'+i+'='+window.encodeURIComponent(serverid)+set_request_b;
		}

		// Send AJAX request
		if (set_request!='')
		{
			set_request=modsecurity_workaround_ajax(set_request);
			do_ajax_request('{$BASE_URL_NOHTTP;}/data/sitemap.php?set_perms=1'+keep_stub(),function() {
				window.fauxmodal_alert('{!permissions:PERMISSIONS_TREE_EDITOR_SAVED;^}');
			},set_request);
			return;
		}
	}
	window.fauxmodal_alert('{!permissions:PERMISSIONS_TREE_EDITOR_SAVED;^}');
}

function permissions_img_func_1(node,id)
{
	var temp=permissions_img_func_1_b(node,id);
	var url=temp[0];
	var title=temp[1];
	return '<img class="vertical_alignment perm_icon" src="'+url+'" alt="'+title+'" title="'+title+'" />&nbsp;';
}

function permissions_img_func_1_b(node,id)
{
	var group=document.getElementById('group').value;

	if (typeof id=='undefined') id=node.getAttribute('id');

	if (typeof window.attributes_full=='undefined') window.attributes_full=[];
	if (typeof window.attributes_full[id]=='undefined') window.attributes_full[id]=node.attributes;

	if (((window.attributes_full[id]['group_privileges_delete_highrange_content_'+group]) && (window.attributes_full[id]['group_privileges_delete_highrange_content_'+group]=='1')) ||
		 ((window.attributes_full[id]['group_privileges_delete_midrange_content_'+group]) && (window.attributes_full[id]['group_privileges_delete_midrange_content_'+group]=='1')) ||
		 ((window.attributes_full[id]['group_privileges_delete_lowrange_content_'+group]) && (window.attributes_full[id]['group_privileges_delete_lowrange_content_'+group]=='1')))
			return Array('{$IMG;,permlevels/3}'.replace(/^https?:/,window.location.protocol),'{!PINTERFACE_LEVEL_3;^}');
	else
	if (((window.attributes_full[id]['group_privileges_bypass_validation_highrange_content_'+group]) && (window.attributes_full[id]['group_privileges_bypass_validation_highrange_content_'+group]=='1')) ||
		 ((window.attributes_full[id]['group_privileges_bypass_validation_midrange_content_'+group]) && (window.attributes_full[id]['group_privileges_bypass_validation_midrange_content_'+group]=='1')) ||
		 ((window.attributes_full[id]['group_privileges_bypass_validation_lowrange_content_'+group]) && (window.attributes_full[id]['group_privileges_bypass_validation_lowrange_content_'+group]=='1')))
			return Array('{$IMG;,permlevels/2}'.replace(/^https?:/,window.location.protocol),'{!PINTERFACE_LEVEL_2;^}');
	else
	if (((window.attributes_full[id]['group_privileges_submit_highrange_content_'+group]) && (window.attributes_full[id]['group_privileges_submit_highrange_content_'+group]=='1')) ||
		 ((window.attributes_full[id]['group_privileges_submit_midrange_content_'+group]) && (window.attributes_full[id]['group_privileges_submit_midrange_content_'+group]=='1')) ||
		 ((window.attributes_full[id]['group_privileges_submit_lowrange_content_'+group]) && (window.attributes_full[id]['group_privileges_submit_lowrange_content_'+group]=='1')))
			return Array('{$IMG;,permlevels/1}'.replace(/^https?:/,window.location.protocol),'{!PINTERFACE_LEVEL_1;^}');
	else
	if (window.attributes_full[id]['inherits_something'])
			return Array('{$IMG;,permlevels/inherit}'.replace(/^https?:/,window.location.protocol),'{!permissions:PINTERFACE_LEVEL_GLOBAL;^}');
	else
	if (window.attributes_full[id]['no_privileges']) return Array('{$IMG;,blank}'.replace(/^https?:/,window.location.protocol),'');

	return Array('{$IMG;,permlevels/0}'.replace(/^https?:/,window.location.protocol),'{!permissions:PINTERFACE_LEVEL_0;^}');
}

function permissions_img_func_2(node,id)
{
	var temp=permissions_img_func_2_b(node,id);
	var url=temp[0];
	var title=temp[1];
	return '<img class="vertical_alignment" src="'+url+'" alt="'+title+'" title="'+title+'" />';
}

function permissions_img_func_2_b(node,id)
{
	if (typeof id=='undefined') id=node.getAttribute('id');

	var group=document.getElementById('group').value;

	if (node.getAttribute('g_view_'+group)=='true')
		return Array('{$IMG;,led_on}'.replace(/^https?:/,window.location.protocol),'{!permissions:PINTERFACE_VIEW;^}');
	return Array('{$IMG;,led_off}'.replace(/^https?:/,window.location.protocol),'{!permissions:PINTERFACE_VIEW_NO;^}');
}

function update_group_displayer(setting)
{
	if (typeof window.sitemap=='undefined') return;

	set_inner_html(document.getElementById('group_name'),escape_html(window.usergroup_titles[setting.options[setting.selectedIndex].value]));
	var html=document.getElementById('tree_list__root_tree_list');
	set_inner_html(html,'');
	window.sitemap.render_tree(window.sitemap.tree_list_data,html);
}

