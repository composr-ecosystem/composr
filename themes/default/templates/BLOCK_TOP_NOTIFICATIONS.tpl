{$REQUIRE_CSS,notifications}

{+START,IF,{$CONFIG_OPTION,web_notifications_enabled}}
	<div class="global_button_ref_point" id="web_notifications_rel" style="display: none">
		<div class="box box_arrow box__block_top_notifications_web"><span></span><div class="box_inner">
			<div id="web_notifications_spot" role="log">
				{+START,IF_EMPTY,{NOTIFICATIONS}}
					<p class="nothing_here">{!NO_NOTIFICATIONS}</p>
				{+END}
				{+START,IF_NON_EMPTY,{NOTIFICATIONS}}
					{NOTIFICATIONS}
				{+END}
			</div>

			<ul class="associated_links_block_group horizontal_links">
				<li><a href="{$PAGE_LINK*,_SEARCH:notifications:browse}">{!VIEW_ARCHIVE}</a></li>
				{+START,IF,{$NOT,{$MATCH_KEY_MATCH,_WILD:members:view}}}
					<li><a onmouseup="toggle_web_notifications(event); find_url_tab();" title="{!VIEW_ARCHIVE} {!NOTIFICATIONS}: {!SETTINGS}" href="{$PAGE_LINK*,{$?,{$CNS},_SEARCH:members:view#tab__edit__notifications,_SEARCH:notifications:overall}}">{!SETTINGS}</a></li>
				{+END}
				<li><a href="#" onclick="return notifications_mark_all_read(event);">{!NOTIFICATIONS_MARK_READ}</a></li>
			</ul>
		</div></div>
	</div>
	<a title="{!NOTIFICATIONS}" id="web_notifications_button" class="count_{NUM_UNREAD_WEB_NOTIFICATIONS%}" onclick="return toggle_web_notifications(event);" href="{$PAGE_LINK*,_SEARCH:notifications:browse}"><span aria-label="{!COUNT_TOTAL} {!NOTIFICATIONS}">{NUM_UNREAD_WEB_NOTIFICATIONS*}</span></a>
{+END}

{+START,IF,{$NOT,{$CONFIG_OPTION,pt_notifications_as_web}}}{+START,IF,{$CNS}}
	<div class="global_button_ref_point" id="pts_rel" style="display: none">
		<div class="box box_arrow box__block_top_notifications_pts"><span></span><div class="box_inner">
			<div id="pts_spot" role="log">
				{+START,IF_EMPTY,{PTS}}
					<p class="nothing_here">{!cns:NO_INBOX}</p>
				{+END}
				{+START,IF_NON_EMPTY,{PTS}}
					{PTS}
				{+END}
			</div>

			<ul class="associated_links_block_group horizontal_links">
				<li><a onmouseup="toggle_pts(event); find_url_tab();" href="{$PAGE_LINK*,_SEARCH:members:view#tab__pts}">{!cns:PRIVATE_TOPICS_INBOX}</a></li>
				<li><a href="{$PAGE_LINK*,_SEARCH:topics:new_pt}">{!cns:NEW_PRIVATE_TOPIC}</a></li>
			</ul>
		</div></div>
	</div>
	<a title="{!cns:PRIVATE_TOPICS}" id="pts_button" class="count_{NUM_UNREAD_PTS%}" onclick="return toggle_pts(event);" href="{$PAGE_LINK*,_SEARCH:members:view#tab__pts}"><span aria-label="{!COUNT_TOTAL} {!cns:PRIVATE_TOPICS}">{NUM_UNREAD_PTS*}</span></a>
{+END}{+END}

<script>// <![CDATA[
	window.max_notifications_to_show={MAX%};
//]]></script>
