<div class="box box---block-side-shoutbox-message"><div class="box-inner">
	<a class="shoutbox-message-avatar" href="{MEMBER_URL*}" title="{!AVATAR}"><img src="{$ENSURE_PROTOCOL_SUITABILITY*,{$AVATAR,{MEMBER_ID}}}" alt="{!AVATAR}" width="20" height="20" /></a>

	<div class="shoutbox-message-header">
		<p class="shoutbox-message-author associated-details">{MEMBER_LINK}</p>

		<p class="shoutbox-message-time associated-details">{!_AGO*,{$MAKE_RELATIVE_DATE,{_TIME}}}</p>
	</div>

	<blockquote class="shoutbox-message">{$TRUNCATE_LEFT,{MESSAGE},92,1,1}</blockquote>
</div></div>
