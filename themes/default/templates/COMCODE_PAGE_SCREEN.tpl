{+START,IF,{$NOR,{IS_PANEL},{BEING_INCLUDED}}}
	{+START,IF_EMPTY,{$TRIM,{CONTENT}}}
		<p class="nothing-here">{!NO_PAGE_OUTPUT}</p>
	{+END}
{+END}

{+START,IF,{$OR,{$NOT,{IS_PANEL}},{$IS_NON_EMPTY,{$TRIM,{CONTENT}}}}}
	<div class="comcode-page">
		{WARNING_DETAILS}

		{$TRIM,{CONTENT}}
		{$METADATA_IMAGE_EXTRACT,{CONTENT}}

		{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,comcode_page,{NATIVE_ZONE}:{NAME}}}
		{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

		{+START,IF,{SHOW_AS_EDIT}}{+START,IF_NON_EMPTY,{EDIT_DATE_RAW}}
			<div class="edited" role="note">
				<img alt="" width="9" height="6" src="{$IMG*,edited}" />
				<span>{!EDITED}</span>
				<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{EDIT_DATE_RAW}}" itemprop="datePublished">{$DATE*,,,,{EDIT_DATE_RAW}}</time>
			</div>
		{+END}{+END}

		{+START,IF,{$NOR,{IS_PANEL},{$EQ,{NAME},rules,{$DEFAULT_ZONE_PAGE_NAME}},{$WIDE_HIGH},{IS_PANEL},{BEING_INCLUDED},{$GET,already_loaded_screen_actions}}}
			{+START,IF,{$THEME_OPTION,show_screen_actions}}
				{$REQUIRE_CSS,{$?,{$THEME_OPTION,show_screen_actions},screen_actions}}
				{$BLOCK-,failsafe=1,block=main_screen_actions}
			{+END}

			{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

			{$REVIEW_STATUS,comcode_page,{NATIVE_ZONE}:{NAME}}
		{+END}

		{+START,IF_NON_EMPTY,{EDIT_URL}}
			{+START,IF,{$AND,{IS_PANEL},{$THEME_OPTION,enable_edit_page_panel_buttons}}}
				{+START,IF,{$EQ,{NAME},panel_left,panel_right}}
					<p class="quick-self-edit-link associated-link">
						<a data-open-as-overlay="{}" class="link-exempt" title="{!COMCODE_MESSAGE,Comcode} {!LINK_NEW_WINDOW}" target="_blank" href="{$PAGE_LINK*,_SEARCH:userguide_comcode}">{+START,INCLUDE,ICON}NAME=editor/comcode{+END}</a>
						<a href="{EDIT_URL*}" title="{$?,{$HAS_ZONE_ACCESS,adminzone},{!EDIT_ZONE_EDITOR},{!EDIT_PAGE}}: {NAME*} ({!IN,&quot;{$?,{$IS_EMPTY,{$ZONE}},{!_WELCOME},{$ZONE*}}&quot;})"><span>{$?,{$HAS_ZONE_ACCESS,adminzone},{!EDIT_ZONE_EDITOR},{!EDIT_PAGE}}</span></a>
					</p>
				{+END}

				{+START,IF,{$EQ,{NAME},panel_top,panel_bottom}}
					<div class="edit-page-link-inline-wrapper">
						<a class="edit-page-link-inline" href="{EDIT_URL*}" title="{!EDIT_PAGE}: {NAME*}">{+START,INCLUDE,ICON}NAME=editor/comcode{+END}</a>
					</div>
				{+END}
			{+END}

			{+START,IF,{$NOR,{IS_PANEL},{$GET,no_comcode_page_edit_links},{$AND,{BEING_INCLUDED},{$NOT,{$THEME_OPTION,enable_edit_page_include_buttons}}}}}
				{+START,INCLUDE,STAFF_ACTIONS}
					1_URL={EDIT_URL*}
					1_TITLE={$?,{BEING_INCLUDED},&uarr; {!EDIT},{!EDIT_PAGE}}
					1_NOREDIRECT=1
					1_ACCESSKEY={$?,{$NOR,{IS_PANEL},{BEING_INCLUDED}},q}
					1_REL=edit
					1_ICON=admin/edit_this
					2_URL={$?,{$GET,has_comcode_page_children_block},{ADD_CHILD_URL*}}
					2_TITLE={!ADD_CHILD_PAGE}
					2_REL=add
					2_ICON=admin/add
					{+START,COMMENT}
						{+START,IF,{$ADDON_INSTALLED,tickets}}
							3_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=comcode_page:content_id={NATIVE_ZONE}%3A{NAME}:redirect={$SELF_URL&}}
							3_TITLE={!report_content:REPORT_THIS}
							3_ICON=buttons/report
							3_REL=report
						{+END}
					{+END}
				{+END}
			{+END}
		{+END}
	</div>
{+END}
