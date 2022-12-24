{$REQUIRE_JAVASCRIPT,core_rich_media}

<div{+START,IF,{$CNS}} data-tpl="comcodeMemberLink" data-tpl-params="{+START,PARAMS_JSON,MEMBER_ID}{_*}{+END}"{+END}>
	{+START,IF,{$NOT,{$TAPATALK}}}
		<span class="comcode-member-link js-comcode-member-link">
			<img class="embedded-mini-avatar" src="{$?*,{$IS_EMPTY,{$AVATAR,{MEMBER_ID}}},{$IMG,cns_default_avatars/default},{$ENSURE_PROTOCOL_SUITABILITY*,{$AVATAR,{MEMBER_ID}}}}" alt="" />
			<a href="{MEMBER_URL*}">{USERNAME*}</a>
		</span>
	{+END}
	{+START,IF,{$TAPATALK}}
		<a href="{MEMBER_URL*}">{USERNAME*}</a>
	{+END}
</div>
