{+START,IF,{$CONFIG_OPTION,web_notifications_enabled}}
	<div class="top-button-wrapper" data-tpl="blockTopNotifications_webNotifications" data-tpl-params="{+START,PARAMS_JSON,MAX}{_*}{+END}">
		<a title="{!NOTIFICATIONS}" id="web-notifications-button" class="top-button count-{NUM_UNREAD_WEB_NOTIFICATIONS%} js-click-toggle-button-popup" data-click-pd="1" href="{$PAGE_LINK*,_SEARCH:notifications:browse}">{+START,INCLUDE,ICON}
			NAME=tool_buttons/notifications
			ICON_SIZE=24
		{+END}<span class="top-button-bubble" aria-label="{!COUNT_TOTAL} {!NOTIFICATIONS}">{NUM_UNREAD_WEB_NOTIFICATIONS*}</span></a>
		<div class="top-button-popup" id="web-notifications-rel" style="display: none">
			<div class="box box-arrow box--block-top-notifications-web"><div class="box-inner">
				<div id="web-notifications-spot" role="log">
					{+START,IF_EMPTY,{NOTIFICATIONS}}
						<p class="nothing-here">{!NO_NOTIFICATIONS}</p>
					{+END}
					{+START,IF_NON_EMPTY,{NOTIFICATIONS}}
						{NOTIFICATIONS}
					{+END}
				</div>

				<ul class="associated-links-block-group horizontal-links">
					<li><a href="{$PAGE_LINK*,_SEARCH:notifications:browse}">{!VIEW_ARCHIVE}</a></li>
					{+START,IF,{$NOT,{$MATCH_KEY_MATCH,_WILD:members:view}}}
						<li><a class="js-click-find-url-tab js-click-toggle-button-popup" title="{!VIEW_ARCHIVE} {!NOTIFICATIONS}: {!SETTINGS}" href="{$PAGE_LINK*,{$?,{$CNS},_SEARCH:members:view#tab--edit--notifications,_SEARCH:notifications:overall}}">{!SETTINGS}</a></li>
					{+END}
					<li><a href="#!" class="js-click-notifications-mark-all-read js-click-toggle-button-popup">{!cns:MARK_READ}</a></li>
				</ul>
			</div></div>
		</div>
	</div>
{+END}

{+START,IF,{$NOT,{$CONFIG_OPTION,pt_notifications_as_web}}}{+START,IF,{$CNS}}
	<div class="top-button-wrapper" data-tpl="blockTopNotifications_pts">
		<a title="{!cns:PRIVATE_TOPICS}" id="pts-button" class="top-button count-{NUM_UNREAD_PTS%} js-click-toggle-button-popup" data-click-pd="1" href="{$PAGE_LINK*,_SEARCH:members:view#tab--pts}">{+START,INCLUDE,ICON}
			NAME=tool_buttons/inbox
			ICON_SIZE=24
		{+END}<span class="top-button-bubble" aria-label="{!COUNT_TOTAL} {!cns:PRIVATE_TOPICS}">{NUM_UNREAD_PTS*}</span></a>
		<div class="top-button-popup" id="pts-rel" style="display: none">
			<div class="box box-arrow box--block-top-notifications-pts"><div class="box-inner">
				<div id="pts-spot" role="log">
					{+START,IF_EMPTY,{PTS}}
						<p class="nothing-here">{!cns:NO_INBOX}</p>
					{+END}
					{+START,IF_NON_EMPTY,{PTS}}
						{PTS}
					{+END}
				</div>

				<ul class="associated-links-block-group horizontal-links">
					<li><a class="js-click-find-url-tab js-click-toggle-button-popup" href="{$PAGE_LINK*,_SEARCH:members:view#tab--pts}">{!cns:PRIVATE_TOPICS_INBOX}</a></li>
					<li><a href="{$PAGE_LINK*,_SEARCH:topics:new_pt}">{!cns:NEW_PRIVATE_TOPIC}</a></li>
				</ul>
			</div></div>
		</div>
	</div>
{+END}{+END}
