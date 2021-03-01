{+START,IF,{INCLUDE_FORM}}
	{+START,IF_PASSED,SYMBOLS}
		<div class="clearfix"><div class="pagination alphabetical-jumper">
			{+START,LOOP,SYMBOLS}{+START,IF,{$EQ,{$_GET,{BLOCK_ID}_start},{START}}}<span class="results-page-num">{SYMBOL*}</span>{+END}{+START,IF,{$NEQ,{$_GET,{BLOCK_ID}_start},{START}}}<a class="results-continue alphabetical-jumper-cont" target="_self" href="{$PAGE_LINK*,_SELF:_SELF:{BLOCK_ID}_start={START}:{BLOCK_ID}_max={MAX}:{BLOCK_ID}_sort=m_username ASC}">{SYMBOL*}</a>{+END}{+END}
		</div></div>
	{+END}

	{+START,IF_NON_EMPTY,{FILTERS_ROW_A}{FILTERS_ROW_B}}
		<div class="box advanced-member-search"><div class="box-inner">
			<form title="{!SEARCH}" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}" target="_self" method="get">
				{$HIDDENS_FOR_GET_FORM,{$SELF_URL},{BLOCK_ID}_start,{BLOCK_ID}_max,{BLOCK_ID}_sort,filter_{BLOCK_ID}_*}

				<div class="search-fields clearfix">
					<div class="search-button">
						<button data-disable-on-click="1" accesskey="u" class="btn btn-primary btn-scri buttons--filter" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!FILTER}{+START,IF_NON_EMPTY,{FILTERS_ROW_B}} &#9745;{+END}</button>
					</div>

					{+START,LOOP,{FILTERS_ROW_A}}
						{+START,INCLUDE,CNS_MEMBER_DIRECTORY_SCREEN_FILTER}
							NAME={_loop_key}
							LABEL={_loop_var}
							BLOCK_ID={BLOCK_ID}
						{+END}
					{+END}
				</div>

				{+START,IF_NON_EMPTY,{FILTERS_ROW_B}}
					<div class="search-fields clearfix">
						<div class="search-button">
							<button data-cms-href="{$PAGE_LINK*,_SELF:_SELF}" class="btn btn-primary btn-scri buttons--clear" type="button">{+START,INCLUDE,ICON}NAME=buttons/clear{+END} {$,{!RESET_FILTER} }&#9746;</button>
						</div>

						{+START,LOOP,{FILTERS_ROW_B}}
							{+START,INCLUDE,CNS_MEMBER_DIRECTORY_SCREEN_FILTER}
								NAME={_loop_key}
								LABEL={_loop_var}
								BLOCK_ID={BLOCK_ID}
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
			<p class="nothing-here">{$?,{$EQ,{DISPLAY_MODE},media},{!MEMBER_DIRECTORY_UNFILTERED_NO_RESULTS_GALLERIES,{$SITE_NAME*}},{!MEMBER_DIRECTORY_UNFILTERED_NO_RESULTS,{$SITE_NAME*}}}</p>
		{+END}
	{+END}
	{+START,IF,{HAS_ACTIVE_FILTER}}
		{+START,IF_NON_EMPTY,{MEMBER_BOXES}}
			<p>{!MEMBER_DIRECTORY_FILTERED,{$SITE_NAME*}}</p>
		{+END}

		{+START,IF_EMPTY,{MEMBER_BOXES}}
			<p class="nothing-here">{!MEMBER_DIRECTORY_FILTERED_NO_RESULTS,{$SITE_NAME*}}</p>
		{+END}
	{+END}
{+END}
