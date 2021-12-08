{$SET,reply_id,{$RAND}}
{$SET,retweet_id,{$RAND}}
{$SET,favorite_id,{$RAND}}

{+START,SET,tweet_buttons}
	<a href="{REPLY_URL*}" rel="nofollow"><img src="{$IMG*,twitter_feed/reply}" id="{$GET*,reply_id}" title="Reply to this Tweet" /></a> &nbsp;
	<a href="{RETWEET_URL*}" rel="nofollow"><img src="{$IMG*,twitter_feed/retweet}" id="{$GET*,retweet_id}" title="Retweet this Tweet" /></a> &nbsp;
	<a href="{FAVORITE_URL}" rel="nofollow"><img src="{$IMG*,twitter_feed/favorite}" id="{$GET*,favorite_id}" title="Favorite this Tweet" /></a>
{+END}

{+START,SET,tweet_timestamp}
	{$FROM_TIMESTAMP*,%d %B %Y\, %I:%M:%S %p,{$TO_TIMESTAMP,{TWEET_CREATED_AT}}} &ndash; {TWEET_TIME_AGO*}
{+END}

{+START,IF,{$EQ,{STYLE},1}}
	 <div class="box"><div class="box_inner">
		<div class="webstandards_checker_off">
			{TWEET_TEXT`}
		</div>

		<div>
			<div style="float: right; padding-left: 1em">
				{$GET,tweet_buttons}
			</div>

			<div class="associated_details">
				{$GET,tweet_timestamp}
			</div>
		</div>
	</div></div>
{+END}

{+START,IF,{$EQ,{STYLE},2}}
	<div class="float_surrounder" style="margin-bottom: 1em">
		{+START,IF,{SHOW_PROFILE_IMAGE}}
			<img style="float: left" alt="Profile image of @{USER_SCREEN_NAME*}" src="{$ENSURE_PROTOCOL_SUITABILITY*,{USER_PROFILE_IMG_URL}}" />
		{+END}

		<div style="float: right; padding-left: 1em">
			{+START,IF,{$EQ,{FOLLOW_BUTTON_SIZE},0}}
				<img src="{$IMG*,{TWITTER_LOGO_IMG_CODE}}" />
			{+END}
			{+START,IF,{$EQ,{FOLLOW_BUTTON_SIZE},1}}
				{FOLLOW_BUTTON_NORMAL`}
			{+END}
			{+START,IF,{$EQ,{FOLLOW_BUTTON_SIZE},2}}
				{FOLLOW_BUTTON_LARGE`}
			{+END}
		</div>

		<div style="{+START,IF,{SHOW_PROFILE_IMAGE}}float:left; border-left: 1px solid #ccc; padding: 0; margin-left: 1em; padding-left: 1em; width: calc(100% - 48px - 2em - 1px - 80px);{+END}">
			<strong>{USER_NAME*}</strong><br />
			<a href="{USER_PAGE_URL*}" target="_blank" title="@{USER_SCREEN_NAME*} {!LINK_NEW_WINDOW}">@{USER_SCREEN_NAME*}</a>
		</div>
	</div>

	<div class="webstandards_checker_off">
		{TWEET_TEXT`}
	</div>

	<div>
		<div style="float: right; padding-left: 1em">
			{$GET,tweet_buttons}
		</div>

		<div class="associated_details">
			{$GET,tweet_timestamp}
		</div>
	</div>

	<hr class="spaced_rule" />
{+END}

{+START,IF,{$EQ,{STYLE},3}}
	<div class="float_surrounder">
		{+START,IF,{SHOW_PROFILE_IMAGE}}
			<img style="float: left" alt="Profile image of @{USER_SCREEN_NAME*}" src="{$ENSURE_PROTOCOL_SUITABILITY*,{USER_PROFILE_IMG_URL}}" />
		{+END}

		<div style="{+START,IF,{SHOW_PROFILE_IMAGE}}float:left; border-left: 1px solid #ccc; padding: 0; margin-left: 0.5em; padding-left: 0.5em; width: calc(100% - 48px - 1em - 1px);{+END}">
			<div class="webstandards_checker_off">
				{TWEET_TEXT`}
			</div>

			<div class="associated_details">
				{$GET,tweet_timestamp}
			</div>

			<div style="margin-top: 1em">
				{+START,BOX,Click for more details,,,tray_closed}
					<div class="float_surrounder">
						<div style="float: right; padding-left: 1em">
							{+START,IF,{$EQ,{FOLLOW_BUTTON_SIZE},0}}
								<img src="{$IMG*,{TWITTER_LOGO_IMG_CODE}}" />
							{+END}
							{+START,IF,{$EQ,{FOLLOW_BUTTON_SIZE},1}}
								{FOLLOW_BUTTON_NORMAL`}
							{+END}
							{+START,IF,{$EQ,{FOLLOW_BUTTON_SIZE},2}}
								{FOLLOW_BUTTON_LARGE`}
							{+END}
						</div>

						<div>
							<strong>{USER_NAME*}</strong> (<a href="{USER_PAGE_URL*}" title="@{USER_SCREEN_NAME*} {!LINK_NEW_WINDOW}" target="_blank">@{USER_SCREEN_NAME*}</a>)
						</div>
					</div>

					<div class="float_surrounder">
						<div style="float: right; padding-left: 1em">
							{$GET,tweet_buttons}
						</div>

						<div>
							Retweets: {TWEET_RETWEET_COUNT*}
						</div>
					</div>
				{+END}
			</div>
		</div>
	</div>

	<hr class="spaced_rule" />
{+END}

<script>// <![CDATA[
	add_event_listener_abstract(window,'load',function() {
		create_rollover('{$GET;/,reply_id}','{$IMG;/,twitter_feed/reply_hover}');
		create_rollover('{$GET;/,retweet_id}','{$IMG;/,twitter_feed/retweet_hover}');
		create_rollover('{$GET;/,favorite_id}','{$IMG;/,twitter_feed/favorite_hover}');
	});
//]]></script>
