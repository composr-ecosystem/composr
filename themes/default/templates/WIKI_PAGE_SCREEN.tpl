{$REQUIRE_JAVASCRIPT,wiki}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<div data-tpl="wikiPageScreen">
	{TITLE}

	{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,wiki_page,{ID}}}

	{$REQUIRE_CSS,cns}

	<div class="wiki-screen">
		{+START,IF_NON_EMPTY,{DESCRIPTION}}
			{+START,IF,{SHOW_POSTS}}
				<div class="pe-wiki-page-description" itemprop="description">
					<div class="box box---wiki-page-screen"><div class="box-inner">
						<div>{$,To disassociated headers}
							{DESCRIPTION}
						</div>
					</div></div>

					{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}
				</div>
			{+END}
			{+START,IF,{$NOT,{SHOW_POSTS}}}
				<div class="pe-wiki-page-description">
					{DESCRIPTION}
				</div>

				{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

				<hr class="spaced-rule" />
			{+END}
		{+END}

		{+START,IF_EMPTY,{CHILDREN}}
			<p class="nothing-here">{!NO_CHILDREN}</p>
		{+END}
		{+START,IF_NON_EMPTY,{CHILDREN}}
			<div class="wiki-page-children">
				<p class="lonely-label">{!CHILD_PAGES}:</p>
				<ul itemprop="significantLinks" class="spaced-list">
					{+START,LOOP,CHILDREN}
						<li>
							<a title="{CHILD*}: {!WIKI_PAGES}" href="{URL*}">{CHILD*}</a>
							{+START,IF,{$OR,{$GT,{MY_CHILD_POSTS},0},{$GT,{MY_CHILD_CHILDREN},0},{$IS_NON_EMPTY,{BODY_CONTENT}}}}
								<br />
								{+START,IF_PASSED,BODY_CONTENT}{!BODY_CONTENT}, {+END}
								{!POST_PLU,{MY_CHILD_POSTS*}},
								{!CHILD_PLU,{MY_CHILD_CHILDREN*}}
							{+END}
							{+START,IF,{$NOR,{$GT,{MY_CHILD_POSTS},0},{$GT,{MY_CHILD_CHILDREN},0},{$IS_NON_EMPTY,{BODY_CONTENT}}}}
								{+START,IF,{$NOT,{SHOW_POSTS}}}
									{!EMPTY}
								{+END}
							{+END}
						</li>
					{+END}
				</ul>
			</div>
		{+END}

		{+START,IF,{$OR,{$IS_NON_EMPTY,{POSTS}},{SHOW_POSTS}}}
			{+START,IF,{$NOT,{SHOW_POSTS}}}
				<div data-toggleable-tray="{}">
					<p class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" title="{!DISCUSSION}: {!EXPAND}/{!CONTRACT}" href="#!">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" title="{!DISCUSSION}: {!EXPAND}/{!CONTRACT}" href="#!">{!DISCUSSION}</a> ({!POST_PLU,{NUM_POSTS*}})
					</p>
					<div class="toggleable-tray js-tray-content" id="hidden-posts" style="display: {$JS_ON,none,block}">
			{+END}

			{+START,IF_EMPTY,{POSTS}}
				<p class="nothing-here">{!NO_POSTS}</p>
			{+END}
			{+START,IF_NON_EMPTY,{POSTS}}
				<div class="map-table results-table wide-table cns-topic wiki-table">
					<div>
						{POSTS}
					</div>
				</div>
			{+END}

			{+START,IF,{$NOT,{SHOW_POSTS}}}
					</div>
				</div>
			{+END}
		{+END}

		<div class="buttons-group">
			<div class="buttons-group-inner">
				{+START,INCLUDE,NOTIFICATION_BUTTONS}
					NOTIFICATIONS_TYPE=wiki
					NOTIFICATIONS_ID={ID}
				{+END}

				{BUTTONS}

				{+START,IF_NON_EMPTY,{POSTS}}
					{+START,IF,{$AND,{$JS_ON},{STAFF_ACCESS}}}
						<form class="inline" title="{!MERGE_WIKI_POSTS}" action="{$PAGE_LINK*,_SEARCH:wiki:merge:{ID},1}" method="post">
							{$INSERT_FORM_POST_SECURITY}

							<div class="inline">
								<button id="wiki-merge-button" style="display: none" class="btn btn-primary btn-scr admin--merge button-faded js-click-btn-add-form-marked-posts" type="submit">{+START,INCLUDE,ICON}NAME=admin/merge{+END} {!_MERGE_WIKI_POSTS}</button>
							</div>
						</form>
					{+END}
				{+END}
			</div>
		</div>

		{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

		{+START,IF,{$THEME_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}

		{$REVIEW_STATUS,wiki_page,{ID}}
	</div>
</div>
