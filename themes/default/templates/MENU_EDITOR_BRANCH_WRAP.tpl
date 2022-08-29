<div class="menu-editor-branch" id="branch-wrap-{I*}" data-tpl="menuEditorBranchWrap" data-tpl-params="{+START,PARAMS_JSON,CLICKABLE_SECTIONS,BRANCH_TYPE,I}{_*}{+END}">
	<div class="menu-editor-branch-inner" id="branch-{I*}">
		<label id="label-caption-{I*}" for="caption_{I*}">{!CAPTION}: </label>
		<input maxlength="255" class="form-control js-focus-make-caption-field-selected js-dblclick-scroll-to-heading"type="text" value="{CAPTION*}" id="caption_{I*}" name="caption_{I*}" />

		<input type="hidden" id="caption_long_{I*}" name="caption_long_{I*}" value="{CAPTION_LONG*}"{+START,IF_EMPTY,{CAPTION_LONG}} disabled="disabled"{+END} />
		<input type="hidden" id="url_{I*}" name="url_{I*}" value="{URL*}" />
		<input type="hidden" id="page_only_{I*}" name="page_only_{I*}" value="{PAGE_ONLY*}"{+START,IF_EMPTY,{PAGE_ONLY}} disabled="disabled"{+END} />
		<input type="hidden" id="theme_img_code_{I*}" name="theme_img_code_{I*}" value="{THEME_IMG_CODE*}"{+START,IF_EMPTY,{THEME_IMG_CODE}} disabled="disabled"{+END} />
		<input type="hidden" id="parent_{I*}" name="parent_{I*}" value="{PARENT*}" />
		<input type="hidden" id="order_{I*}" name="order_{I*}" value="{ORDER*}" />
		<input type="hidden" id="new_window_{I*}" name="new_window_{I*}" value="{NEW_WINDOW*}"{+START,IF,{$NOT,{NEW_WINDOW}}} disabled="disabled"{+END} />
		<input type="hidden" id="check_perms_{I*}" name="check_perms_{I*}" value="{CHECK_PERMS*}"{+START,IF,{$NOT,{CHECK_PERMS}}} disabled="disabled"{+END} />
		<input type="hidden" id="include_sitemap_{I*}" name="include_sitemap_{I*}" value="{INCLUDE_SITEMAP*}"{+START,IF,{$NOT,{INCLUDE_SITEMAP}}} disabled="disabled"{+END} />
		<div class="accessibility-hidden"><label id="label-branch-type-{I*}" for="branch_type_{I*}">{!MENU_ENTRY_BRANCH}</label></div>
		<select style="display: none" class="form-control js-click-menu-editor-branch-type-change js-change-menu-editor-branch-type-change" title="{$STRIP_TAGS,{!MENU_ENTRY_BRANCH}}" id="branch_type_{I*}" name="branch_type_{I*}">
			{+START,IF,{$NOT,{CLICKABLE_SECTIONS}}}
				<option value="page">{!PAGE}</option>
			{+END}
			<option value="branch_minus">{!CONTRACTED_BRANCH}</option>
			<option value="branch_plus">{!EXPANDED_BRANCH}</option>
		</select>

		<a href="#!" id="down_{I*}" class="horiz-field-sep js-click-btn-move-down-handle-ordering">{+START,INCLUDE,ICON}
			NAME=results/sortablefield_desc
			ICON_SIZE=12
			ICON_DESCRIPTION={!MOVE_DOWN}
		{+END}</a>

		<a href="#!" id="up_{I*}" class="js-click-btn-move-up-handle-ordering">{+START,INCLUDE,ICON}
			NAME=results/sortablefield_asc
			ICON_SIZE=12
			ICON_DESCRIPTION={!MOVE_UP}
		{+END}</a>

		<button class="btn btn-danger btn-sm horiz-field-sep js-click-delete-menu-branch" type="button" id="del_{I*}" name="del_{I*}">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} <span>{!DELETE}</span></button>
	</div>

	<div class="menu-editor-branch-indent" id="branch-{I*}-follow-1" style="{DISPLAY*}">
		{BRANCH}
	</div>
	<div class="menu-editor-branch-indent" id="branch-{I*}-follow-2" style="{DISPLAY*}"></div>
</div>
