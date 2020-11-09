{$REQUIRE_JAVASCRIPT,activity_feed}

<div data-tpl="cnsMemberProfileActivities" data-tpl-params="{+START,PARAMS_JSON,SYNDICATIONS}{_*}{+END}">
	<div class="clearfix">
		{+START,IF,{$EQ,{MEMBER_ID},{$MEMBER}}}
			{$BLOCK,block=main_activity_feed_state,member={MEMBER_ID},mode=some_members,param=}
		{+END}

		{$BLOCK,block=main_activity_feed,member={MEMBER_ID},mode=some_members,param=,max=10,grow=1}

		<hr class="spaced-rule" />

		<div class="right">
			{+START,INCLUDE,NOTIFICATION_BUTTONS}
				NOTIFICATIONS_TYPE=activity
				NOTIFICATIONS_ID={MEMBER_ID}
			{+END}
		</div>
	</div>
</div>
