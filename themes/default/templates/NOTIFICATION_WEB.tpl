{$REQUIRE_JAVASCRIPT,core_notifications}

<div class="notification notification-priority--{PRIORITY*} notification-code--{NOTIFICATION_CODE*} notification-{$?,{HAS_READ},has-read,has-not-read}" data-tpl="notificationWeb">
	{+START,IF_NON_EMPTY,{FROM_AVATAR_URL}}
		<img class="right spaced" src="{$ENSURE_PROTOCOL_SUITABILITY*,{FROM_AVATAR_URL}}" title="{FROM_USERNAME*}" alt="{FROM_USERNAME*}" />
	{+END}

	<p class="notification-subject">
		{+START,IF_PASSED,URL}
			<a href="{URL*}">{SUBJECT*}</a>
		{+END}

		{+START,IF_NON_PASSED,URL}
			<a class="js-click-poll-for-notifications" data-open-as-overlay="{}" href="{$PAGE_LINK*,_SEARCH:notifications:view:{ID}}">{SUBJECT*}</a>
		{+END}
	</p>

	<ul class="notification-meta horizontal-meta-details">
		<li>{DATE*}</li>
		<li>({$?,{HAS_READ},{!notifications:HAS_READ},{!notifications:HAS_NOT_READ}})</li>
	</ul>
</div>
