{$REQUIRE_JAVASCRIPT,twitter_feed}

<section class="box box---block-main-twitter-feed" data-tpl="blockTwitterFeed"><div class="box-inner">
	{+START,IF_NON_EMPTY,{TWITTER_TITLE}}<h3>{TWITTER_TITLE*}</h3>{+END}

	{+START,IF_EMPTY,{TWITTER_ERROR}}
		{+START,IF,{$EQ,{STYLE},1}}
			{+START,IF_NON_EMPTY,{USER_SCREEN_NAME}}
				{+START,BOX,@{USER_SCREEN_NAME*} Twitter Profile Details,,,tray_open}
					{+START,IF,{SHOW_PROFILE_IMAGE}}
						<img style="float: left" alt="Profile image of @{USER_SCREEN_NAME*}" src="{$ENSURE_PROTOCOL_SUITABILITY*,{USER_PROFILE_IMG_URL}}" />
					{+END}

					<div style="{+START,IF,{SHOW_PROFILE_IMAGE}}float:left; border-left: 1px solid #ccc; padding: 0; margin-left: 0.5em; padding-left: 0.5em; width: calc(100% - 48px - 1em - 1px);{+END}">
						<div style="float: right; text-align: right; padding-left: 1em">
							{+START,IF,{$EQ,{FOLLOW_BUTTON_SIZE},0}}
								<img src="{$IMG*,{TWITTER_LOGO_IMG_CODE}}" />
							{+END}
							{+START,IF,{$EQ,{FOLLOW_BUTTON_SIZE},1}}
								{FOLLOW_BUTTON_NORMAL`}<br />
							{+END}
							{+START,IF,{$EQ,{FOLLOW_BUTTON_SIZE},2}}
								{FOLLOW_BUTTON_LARGE`}<br />
							{+END}
							<br />

							<span style="font-size: 1.2em;">{USER_STATUS_COUNT*}</span> Tweets<br />
							<hr style="height: 1px;" />
							<span style="font-size: 1.2em;">{USER_FOLLOWING_COUNT*}</span> Following<br />
							<hr style="height: 1px;" />
							<span style="font-size: 1.2em;">{USER_FOLLOWERS_COUNT*}</span> Followers<br />
						</div>

						<div>
							<span style="font-size: 1.2em;"><strong>{USER_NAME*}</strong></span><br />
							<strong><a href="{USER_PAGE_URL*}" title="@{USER_SCREEN_NAME*} {!LINK_NEW_WINDOW}" target="_blank">@{USER_SCREEN_NAME*}</a></strong><br /><br />
							{USER_DESCRIPTION`}<br /><br />
							{USER_LOCATION*} &nbsp;&nbsp;&nbsp; <a href="{USER_URL*}" title="@{USER_SCREEN_NAME*} Home page {!LINK_NEW_WINDOW}" target="_blank">{USER_URL*}</a><br />
							Joined on {$FROM_TIMESTAMP*,%d %B %Y\, %I:%M:%S %p,{$TO_TIMESTAMP*,{USER_CREATED_AT}}}
						</div>
					</div>
				{+END}
			{+END}
		{+END}

		<div>
			{CONTENT}
		</div>
	{+END}

	{+START,IF_NON_EMPTY,{TWITTER_ERROR}}
		<p class="webstandards-checker-off">
			{+START,IF,{$IS_STAFF}}
				{+START,IF_NON_EMPTY,{USER_SCREEN_NAME}}
				   <strong>Twitter Name:</strong> <a rel="noopener" href="https://www.twitter.com/{USER_SCREEN_NAME&*}" title="@{USER_SCREEN_NAME*} {!LINK_NEW_WINDOW}" target="_blank">{USER_SCREEN_NAME*}</a><br />
				{+END}
				<strong>Error:</strong> {TWITTER_ERROR`}<br />
				You are seeing the block error message(s) because you are staff.<br />
				Normal web site guests and members won't see this.
			{+END}

			{+START,IF,{$NOT,{$IS_STAFF}}}
				Sorry, we are experiencing technical difficulties with Twitter.<br />
				Please check back later.
			{+END}
		</p>
	{+END}
</div></section>
