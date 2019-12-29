{$REQUIRE_JAVASCRIPT,activity_feed}

<div data-tpl="cnsMemberProfileActivities" data-tpl-params="{+START,PARAMS_JSON,SYNDICATIONS}{_*}{+END}">
	<div class="clearfix">
		{+START,IF,{$EQ,{MEMBER_ID},{$MEMBER}}}
			{$BLOCK,block=main_activities_state,member={MEMBER_ID},mode=some_members,param=}
		{+END}

		{$BLOCK,block=main_activities,member={MEMBER_ID},mode=some_members,param=,max=10,grow=1}

		<hr class="spaced-rule" />

		<div class="right">
			{+START,INCLUDE,NOTIFICATION_BUTTONS}
				NOTIFICATIONS_TYPE=activity
				NOTIFICATIONS_ID={MEMBER_ID}
			{+END}
		</div>
	</div>

	{$SET,show_syndication_options,{$AND,{$IS_NON_EMPTY,{SYNDICATIONS}},{$EQ,{MEMBER_ID},{$MEMBER}}}}
	{+START,IF,{$GET,show_syndication_options}}
		<p>{!CREATE_SYNDICATION_LINK}</p>

		<form action="{$PAGE_LINK*,_SEARCH:members:view:{MEMBER_ID}}#tab--activities" method="post">
			{$INSERT_SPAMMER_BLACKHOLE}

			<p>
				{+START,LOOP,SYNDICATIONS}
					{+START,IF,{SYNDICATION_IS_SET}}
						<button class="btn btn-primary btn-scri buttons--cancel" data-disable-on-click="1" type="submit" id="syndicate_stop__{_loop_key*}" name="syndicate_stop__{_loop_key*}">{+START,INCLUDE,ICON}NAME=buttons/cancel{+END} {!STOP_SYNDICATING_TO,{SYNDICATION_SERVICE_NAME*}}</button>
					{+END}
					{+START,IF,{$NOT,{SYNDICATION_IS_SET}}}
						<button class="btn btn-primary btn-scri buttons--proceed" data-disable-on-click="1" type="submit" id="syndicate_start__{_loop_key*}" name="syndicate_start__{_loop_key*}">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!START_SYNDICATING_TO,{SYNDICATION_SERVICE_NAME*}}</button>
					{+END}
				{+END}
			</p>
		</form>
	{+END}
</div>
