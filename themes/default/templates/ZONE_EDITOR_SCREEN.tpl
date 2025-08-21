{$SET,default_zone_page_name,{$DEFAULT_ZONE_PAGE_NAME}}

{$SET,preview_url,{$PREVIEW_URL}{$KEEP}}

<div data-view="ZoneEditorScreen" data-view-params="{+START,PARAMS_JSON,default_zone_page_name,preview_url}{_*}{+END}">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<p class="vertical-alignment">
		{+START,INCLUDE,ICON}
			NAME=help
			ICON_SIZE=24
		{+END}
		<span>{!ZE_HOW_TO_SAVE}</span>
	</p>

	<div class="clearfix" id="ze-panels-wrap">
		<div id="p-panel-left" class="ze-panel" data-mouseover-class="{ 'ze-panel-expanded': 1 }" data-mouseout-class="{ 'ze-panel-expanded': 0 }">
			{LEFT_EDITOR}
		</div>

		<div id="p-panel-right" class="ze-panel" data-mouseover-class="{ 'ze-panel-expanded': 1 }" data-mouseout-class="{ 'ze-panel-expanded': 0 }">
			{RIGHT_EDITOR}
		</div>

		<div class="ze-middle">
			{MIDDLE_EDITOR}
		</div>
	</div>

	<hr class="spaced-rule" />

	<form title="{!SAVE}" action="{URL*}" method="post" target="_self" class="zone-editor-form">
		{$INSERT_FORM_POST_SECURITY}

		<div id="edit-field-store" style="display: none">
		</div>

		<p class="proceed-button vertical-alignment">
			<button class="btn btn-primary btn-scr buttons--save js-btn-fetch-and-submit" type="button">{+START,INCLUDE,ICON}NAME=buttons/save{+END} <span>{!SAVE}</span></button> <span class="associated-details">{!ZE_CLICK_TO_EDIT}</span>
		</p>
	</form>

	<p class="vertical-alignment">
		{+START,INCLUDE,ICON}
			NAME=help
			ICON_SIZE=24
		{+END}
		<span>{!MANY_PANEL_TYPES,{PANEL_TOP_EDIT_URL*},{PANEL_BOTTOM_EDIT_URL*}}</span>
	</p>
</div>