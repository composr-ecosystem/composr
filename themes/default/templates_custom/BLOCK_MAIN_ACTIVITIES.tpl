{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{$SET,ajax_block_main_activities_wrapper,ajax_block_main_activities_wrapper_{$RAND%}}
	<div id="{$GET*,ajax_block_main_activities_wrapper}">
		<div class="float_surrounder">
			<div id="activities_feed">
				<div id="activities_general_notify"></div>
				<ul class="float_surrounder activities_holder raw_ajax_grow_spot" id="activities_holder">
					{+START,IF_EMPTY,{CONTENT}}
						<li id="activity_-1"><p class="nothing_here">{!NO_ACTIVITIES}</p></li>
					{+END}

					{+START,LOOP,CONTENT}
						<li id="activity_{LIID*}" class="activities_box box">
							{+START,INCLUDE,ACTIVITY}{+END}
						</li>
					{+END}
				</ul>
			</div>

			{+START,IF_NON_EMPTY,{PAGINATION}}
				<div class="pagination_spacing float_surrounder ajax_block_wrapper_links">
					{PAGINATION}
				</div>
			{+END}
		</div>

		<script>//<![CDATA[
			add_event_listener_abstract(window,'load',function() {
				window.activities_mode='{MODE;/}';
				window.activities_member_ids='{MEMBER_IDS;/}';

				{+START,IF,{$EQ,{START},0}}
					// "Grow" means we should keep stacking new content on top of old. If not
					// then we should allow old content to "fall off" the bottom of the feed.
					{+START,IF,{GROW}}
						window.activities_feed_grow=true;
					{+END}
					{+START,IF,{$NOT,{GROW}}}
						window.activities_feed_grow=false;
					{+END}
					window.activities_feed_max={MAX%};
					if ($('#activities_feed').length!=0) {
						window.setInterval(s_update_get_data,{REFRESH_TIME%}*1000);
					}
				{+END}
			});
		//]]></script>

		{+START,IF_NON_EMPTY,{PAGINATION}}
			{+START,INCLUDE,AJAX_PAGINATION}
				ALLOW_INFINITE_SCROLL={$NOT,{$GET,show_syndication_options}}
				WRAPPER_ID={$GET,ajax_block_main_activities_wrapper}
			{+END}
		{+END}
	</div>
{+END}

{+START,IF,{$EQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,LOOP,CONTENT}
		<li id="{LIID*}" class="activities_box box">
			{+START,INCLUDE,ACTIVITY}{+END}
		</li>
	{+END}

	{PAGINATION}
{+END}
