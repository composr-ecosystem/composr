{$REQUIRE_JAVASCRIPT,activity_feed}

<div data-tpl="activity" data-tpl-params="{+START,PARAMS_JSON,LIID}{_*}{+END}">
	{+START,IF,{ALLOW_REMOVE}}
		<form title="{!REMOVE} #{LIID*}" id="feed-remove-{LIID*}" class="activities-remove js-submit-confirm-update-remove" action="#" method="post">
			{$INSERT_SPAMMER_BLACKHOLE}

			<button class="btn btn-link remove-button" type="submit" title="{!REMOVE}">
				{+START,INCLUDE,ICON}
					NAME=admin/delete
					ICON_SIZE=14
				{+END}
			</button>
		</form>
	{+END}

	<div class="activities-avatar-box">
		{+START,IF_NON_EMPTY,{ADDON_ICON}}
			<img alt="" width="36" height="36" src="{$THUMBNAIL*,{ADDON_ICON},36x36,,,,pad,both,#FFFFFF00}" />
		{+END}
		{+START,IF_EMPTY,{ADDON_ICON}}
			<img alt="" width="36" height="36" src="{$THUMBNAIL*,{$IMG,icons/admin/edit_this},36x36,,,,pad,both,#FFFFFF00}" />
		{+END}

		{+START,SET,commented_out}
			{+START,IF_EMPTY,{AVATAR}}{+START,IF_NON_EMPTY,{$IMG,cns_default_avatars/default,0,,1}}
				<img alt="" width="36" height="36" src="{$THUMBNAIL*,{$IMG,cns_default_avatars/default,0,,1},36x36,,,,pad,both,#FFFFFF00}" />
			{+END}{+END}
			{+START,IF_NON_EMPTY,{AVATAR}}
				<img alt="" width="36" height="36" src="{$THUMBNAIL*,{AVATAR},36x36,,,,pad,both,#FFFFFF00}" />
			{+END}
		{+END}
	</div>

	<div class="activities-line">
		<div class="activity-time right">
			{!_AGO,{$MAKE_RELATIVE_DATE*,{TIMESTAMP},1}}
		</div>

		{+START,SET,commented_out}
			{+START,IF_PASSED,USERNAME}
				<div class="activity-name left">
					<a href="{MEMBER_URL*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>
				</div>
			{+END}
		{+END}

		<div class="activities-content">
			{$,The main message}
			{+START,IF,{$EQ,{LANG_STRING},RAW_DUMP}}
				{+START,IF,{$EQ,{MODE},all}}
					{!ACTIVITY_SAYS,<a href="{MEMBER_URL*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>,{MESSAGE}}
				{+END}
				{+START,IF,{$NEQ,{MODE},all}}
					{MESSAGE}
				{+END}
			{+END}
			{+START,IF,{$NEQ,{LANG_STRING},RAW_DUMP}}
				{$,Because it is being included, the including templates preprocessor will hit the SET but without any data, so we have an IF_PASSED}
				{+START,SET,named}{+START,IF_PASSED,MEMBER_ID}{$OR,{$NEQ,{MEMBER_IDS},{MEMBER_ID}},{$EQ,{MODE},all}}{+END}{+END}

				{+START,IF,{$GET,named}}
					{!ACTIVITY_HAS,<a href="{MEMBER_URL*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>,{$LCASE,{$SUBSTR,{MESSAGE},0,1}}{$SUBSTR,{MESSAGE},1}}
				{+END}
				{+START,IF,{$NOT,{$GET,named}}}
					{MESSAGE}
				{+END}
			{+END}
		</div>
	</div>
</div>
