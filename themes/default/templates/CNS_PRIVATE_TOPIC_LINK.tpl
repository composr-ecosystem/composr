<div class="topic-list-topic notification-{$?,{HAS_READ},has-read,has-not-read}" data-tpl="notificationWeb">
	{$SET,label,{$?,{$EQ,{BY_POSTER_ID},{$MEMBER}},{!WITH_SIMPLE_LOWER,{$DISPLAYED_USERNAME*,{TO_USERNAME}}},{!BY_SIMPLE_LOWER,{$DISPLAYED_USERNAME*,{BY_USERNAME}}}}}
	<img class="right spaced" src="{$?*,{$IS_EMPTY,{$AVATAR,{WITH_POSTER_ID}}},{$IMG,cns_default_avatars/default},{$ENSURE_PROTOCOL_SUITABILITY*,{$AVATAR,{WITH_POSTER_ID}}}}" title="{$GET,label}" alt="{$GET,label}" />

	<div>
		<a class="js-click-poll-for-notifications" data-click-pd="1" data-open-as-overlay="{}" title="{!POST_PLU,{NUM_POSTS*}}, {TITLE*}" href="{TOPIC_URL*}">{$TRUNCATE_LEFT,{TITLE},30}</a>, {$GET,label}<br />
		<span class="associated-details">({$?,{HAS_READ},{!notifications:HAS_READ},{!notifications:HAS_NOT_READ}})</span>
	</div>

	{+START,IF_NON_EMPTY,{LAST_POST_BY_USERNAME}}
		<div class="topic-list-meta">
			{!LAST_POST_BY_SIMPLE,<a class="topic-list-by" href="{LAST_POST_BY_POSTER_URL*}">{$DISPLAYED_USERNAME*,{LAST_POST_BY_USERNAME}}</a>} @ <span class="must-show-together">{DATE*}</span>
		</div>
	{+END}
</div>
