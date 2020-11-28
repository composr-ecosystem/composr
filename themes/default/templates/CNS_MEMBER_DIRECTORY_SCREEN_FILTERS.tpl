{+START,IF,{INCLUDE_FORM}}
	{+START,IF_PASSED,SYMBOLS}
		<div class="float_surrounder"><div class="pagination alphabetical_jumper">
			{+START,LOOP,SYMBOLS}{+START,IF,{$EQ,{$_GET,{BLOCK_ID}_start},{START}}}<span class="results_page_num">{SYMBOL*}</span>{+END}{+START,IF,{$NEQ,{$_GET,{BLOCK_ID}_start},{START}}}<a class="results_continue alphabetical_jumper_cont" target="_self" href="{$PAGE_LINK*,_SELF:_SELF:{BLOCK_ID}_start={START}:{BLOCK_ID}_max={MAX}:{BLOCK_ID}_sort=m_username ASC}">{SYMBOL*}</a>{+END}{+END}
		</div></div>
	{+END}

	{$REQUIRE_JAVASCRIPT,ajax}
	{$REQUIRE_JAVASCRIPT,ajax_people_lists}

	{+START,IF_NON_EMPTY,{FILTERS_ROW_A}{FILTERS_ROW_B}}
		<div class="box advanced_member_search"><div class="box_inner">
			<form title="{!SEARCH}" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}" target="_self" method="get" autocomplete="off">
				{$HIDDENS_FOR_GET_FORM,{$SELF_URL},{BLOCK_ID}_start,{BLOCK_ID}_max,{BLOCK_ID}_sort,filter_{BLOCK_ID}_*}

				<div class="search_fields float_surrounder">
					<div class="search_button">
						<input onclick="disable_button_just_clicked(this);" accesskey="u" class="button_screen_item buttons__filter" type="submit" value="{!FILTER}{+START,IF_NON_EMPTY,{FILTERS_ROW_B}} &#9745;{+END}" />
					</div>

					{+START,LOOP,{FILTERS_ROW_A}}
						{+START,INCLUDE,CNS_MEMBER_DIRECTORY_SCREEN_FILTER}
							NAME={_loop_key}
							LABEL={_loop_var}
						{+END}
					{+END}
				</div>

				{+START,IF_NON_EMPTY,{FILTERS_ROW_B}}
					<div class="search_fields float_surrounder">
						<div class="search_button">
							<input onclick="window.location.href='{$PAGE_LINK;*,_SELF:_SELF}';" class="button_screen_item buttons__clear" type="button" value="{$,{!RESET_FILTER} }&#9746;" />
						</div>

						{+START,LOOP,{FILTERS_ROW_B}}
							{+START,INCLUDE,CNS_MEMBER_DIRECTORY_SCREEN_FILTER}
								NAME={_loop_key}
								LABEL={_loop_var}
							{+END}
						{+END}
					</div>
				{+END}
			</form>
		</div></div>
	{+END}

	{+START,IF,{$NOT,{HAS_ACTIVE_FILTER}}}
		{+START,IF_NON_EMPTY,{MEMBER_BOXES}}
			<p>{!MEMBER_DIRECTORY_UNFILTERED,{$SITE_NAME*}}</p>
		{+END}

		{+START,IF_EMPTY,{MEMBER_BOXES}}
			<p class="nothing_here">{$?,{$EQ,{DISPLAY_MODE},media},{!MEMBER_DIRECTORY_UNFILTERED_NO_RESULTS_GALLERIES,{$SITE_NAME*}},{!MEMBER_DIRECTORY_UNFILTERED_NO_RESULTS,{$SITE_NAME*}}}</p>
		{+END}
	{+END}
	{+START,IF,{HAS_ACTIVE_FILTER}}
		{+START,IF_NON_EMPTY,{MEMBER_BOXES}}
			<p>{!MEMBER_DIRECTORY_FILTERED,{$SITE_NAME*}}</p>
		{+END}

		{+START,IF_EMPTY,{MEMBER_BOXES}}
			<p class="nothing_here">{!MEMBER_DIRECTORY_FILTERED_NO_RESULTS,{$SITE_NAME*}}</p>
		{+END}
	{+END}
{+END}
