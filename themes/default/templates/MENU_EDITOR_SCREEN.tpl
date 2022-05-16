{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

<div class="menu_editor_page docked" id="menu_editor_wrap">
	<div class="menu_editor_page_inner">
		<form title="" action="{URL*}" method="post" autocomplete="off">
			<!-- In separate form due to mod_security -->
			<textarea aria-hidden="true" cols="30" rows="3" style="display: none" name="template" id="template">{CHILD_BRANCH_TEMPLATE*}</textarea>
		</form>

		<form title="{!PRIMARY_PAGE_FORM}" id="edit_form" action="{URL*}" method="post" autocomplete="off" onsubmit="return modsecurity_workaround(this);" data-modsecurity-workaround="1">
			{$INSERT_SPAMMER_BLACKHOLE}

			<div class="float_surrounder menu_edit_main">
				<div class="menu_editor_rh_side">
					<h2>{!HELP}</h2>

					<p>{!BRANCHES_DESCRIPTION,{$PAGE_LINK*,_SEARCH:admin_sitemap:sitemap}}</p>

					<p>{!ENTRY_POINTS_DESCRIPTION}</p>
				</div>

				<div class="menu_editor_lh_side">
					<h2>{!BRANCHES}</h2>

					<input type="hidden" name="highest_order" id="highest_order" value="{HIGHEST_ORDER*}" />

					<div class="menu_editor_root">
						{ROOT_BRANCH}
					</div>
				</div>

				<p class="proceed_button">
					<input id="preview_button" accesskey="p" class="button_screen tabs__preview" type="submit" value="{!PREVIEW}" onclick="return do_menu_preview(this,'{$_GET;*,menu_type}');" />
					<input accesskey="u" class="button_screen buttons__save" type="submit" value="{!SAVE}" onclick="return do_menu_save(this);" />
				</p>
			</div>

			<div id="mini_form_hider" style="display: none" class="float_surrounder">
				<div class="menu_editor_rh_side">
					<img onkeypress="this.onclick(event);" onclick="var e=document.getElementById('menu_editor_wrap'); if (e.className.indexOf(' docked')==-1) { e.className='menu_editor_page docked'; this.src='{$IMG;*,1x/arrow_box_hover}'; if (typeof this.srcset!='undefined') this.srcset='{$IMG;*,2x/arrow_box_hover} 2x'; } else { e.className='menu_editor_page'; this.src='{$IMG;*,1x/arrow_box}'; if (typeof this.srcset!='undefined') this.srcset='{$IMG;*,2x/arrow_box} 2x'; } adjust_pane_heights();" class="dock_button" alt="" title="{!TOGGLE_DOCKED_FIELD_EDITING}" src="{$IMG*,1x/arrow_box_hover}" srcset="{$IMG*,2x/arrow_box_hover} 2x" />

					<h2>{!CHOOSE_ENTRY_POINT}</h2>

					<div class="accessibility_hidden"><label for="tree_list">{!ENTRY}</label></div>
					<input onchange="var e=document.getElementById('url_'+current_selection); if (!e) return; e.value=this.value; e=document.getElementById('edit_form').elements['url']; e.value=this.value; e=document.getElementById('edit_form').elements['caption_'+window.current_selection]; if (e.value=='' &amp;&amp; this.selected_title) e.value=this.selected_title.replace(/^.*:\s*/,'');" style="display: none" type="text" id="tree_list" name="tree_list" value="" />
					<div id="tree_list__root_tree_list">
						<!-- List put in here -->
					</div>
					<script>// <![CDATA[
						add_event_listener_abstract(window,'load',function() {
							window.current_selection='';
							window.sitemap=new tree_list('tree_list','data/sitemap.php?get_perms=0{$KEEP;/}&start_links=1',null,'',false,null,false,true);
							// We will not use &use_urls=1 because this cannot be so easily reliably turned back into the canonical page-links (due to monikers)
						});
					//]]></script>

					<p class="associated_details">
						{!CLICK_ENTRY_POINT_TO_USE}
					</p>

					<nav>
						<ul class="actions_list">
							<li><a href="#" onclick="return menu_editor_add_new_page();">{!SPECIFY_NEW_PAGE}</a></li>
						</ul>
					</nav>
				</div>

				<div class="menu_editor_lh_side">
					<h2>{!EDIT_SELECTED_FIELD}</h2>

					<div class="wide_table_wrap"><table class="map_table form_table wide_table">
						{+START,IF,{$NOT,{$MOBILE}}}
							<colgroup>
								<col class="field_name_column" />
								<col class="field_input_column" />
							</colgroup>
						{+END}

						<tbody>
							{FIELDS_TEMPLATE}
						</tbody>
					</table></div>
				</div>
			</div>

			<input type="hidden" name="confirm" value="1" />
		</form>
	</div>

	<div class="box box___menu_editor_screen">
		<h2 class="toggleable_tray_title">
			<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: {!DELETE_MENU}" title="{!EXPAND}" src="{$IMG*,1x/trays/expand2}" srcset="{$IMG*,2x/trays/expand2} 2x" /></a>
			<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">{!DELETE_MENU}</a>
		</h2>

		<div class="toggleable_tray" id="delete_menu" style="{$JS_ON,display: none,}" aria-expanded="false">
			<p>{!ABOUT_DELETE_MENU}</p>

			<form title="{!DELETE}" action="{DELETE_URL*}" method="post" autocomplete="off">
				{$INSERT_SPAMMER_BLACKHOLE}

				<p class="proceed_button">
					<input type="hidden" name="confirm" value="1" />
					<input type="hidden" name="delete_confirm" value="1" />

					<input class="button_screen_item menu___generic_admin__delete" type="submit" value="{!DELETE}" onclick="var form=this.form; window.fauxmodal_confirm('{!CONFIRM_DELETE;,{MENU_NAME*}}',function(answer) { if (answer) form.submit(); }); return false;" />
				</p>
			</form>
		</div>
	</div>
</div>

<script>// <![CDATA[
	var all_menus=[];
	{+START,LOOP,ALL_MENUS}
		all_menus.push('{_loop_var;/}');
	{+END}

	var cf=function() { var e=document.getElementById('menu_editor_wrap'); if (e.className.indexOf(' docked')==-1) smooth_scroll(find_pos_y(document.getElementById('caption_'+window.current_selection))); };
	document.getElementById('url').ondblclick=cf;
	document.getElementById('caption_long').ondblclick=cf;
	document.getElementById('page_only').ondblclick=cf;

	add_event_listener_abstract(window,'load',function() {
		var footers=document.getElementsByTagName('footer');
		for (var i=0;i<footers.length;i++)
		{
			footers[i].parentNode.removeChild(footers[i]);
		}

		adjust_pane_heights();
		add_event_listener_abstract(window,'resize',function() {
			adjust_pane_heights();
		});
	});
//]]></script>
