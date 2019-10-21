{$REQUIRE_JAVASCRIPT,core_rich_media}

{+START,SET,media}
	{$SET,player_id,player-{$RAND}}

	{$REQUIRE_JAVASCRIPT,jquery}
	{$REQUIRE_JAVASCRIPT,mediaelement-and-player}
	{$REQUIRE_JAVASCRIPT,core_rich_media}
	{$REQUIRE_CSS,mediaelementplayer}

	{+START,IF_NON_PASSED_OR_FALSE,WYSIWYG_EDITABLE}
		{+START,IF_EMPTY,{$METADATA,video}}
			{$METADATA,video,{URL}}
			{$METADATA,video:height,{HEIGHT}}
			{$METADATA,video:width,{WIDTH}}
			{$METADATA,video:type,{MIME_TYPE}}
		{+END}
	{+END}

	{$SET,player_width,{$MIN,950,{WIDTH}}}
	{$SET,player_height,{$MIN,{$MULT,{HEIGHT},{$DIV_FLOAT,950,{WIDTH}}},{HEIGHT}}}

	<video style="display: none" width="{$GET%,player_width}" height="{$GET%,player_height}" poster="{THUMB_URL*}"
			controls="controls" preload="none" id="{$GET%,player_id}" data-tpl="mediaVideoWebsafe"
			data-tpl-params="{+START,PARAMS_JSON,player_id,player_width,player_height,LENGTH,URL,THUMB_URL,type,flashplayer,inline_stats,RESPONSIVE,AUTOSTART,CLOSED_CAPTIONS_URL}{_*}{+END}"
			{+START,IF_PASSED_AND_TRUE,AUTOSTART} autoplay="true"{+END} data-cms-embedded-media="{ width: {$GET%,player_width}, height: {$GET%,player_height}, emits: ['play', 'pause', 'ended'], listens: ['do-play', 'do-pause', 'do-resize'] }">
		<source type="{MIME_TYPE*}" src="{$ENSURE_PROTOCOL_SUITABILITY*,{URL}}" />
		{+START,IF_PASSED,CLOSED_CAPTIONS_URL}{+START,IF_NON_EMPTY,{CLOSED_CAPTIONS_URL}}
			<track src="{$ENSURE_PROTOCOL_SUITABILITY*,{CLOSED_CAPTIONS_URL}}" kind="captions" label="{!CLOSED_CAPTIONS}" srclang="{$LCASE*,{$LANG}}" />
		{+END}{+END}

		<img src="{$ENSURE_PROTOCOL_SUITABILITY*,{THUMB_URL}}" width="{$GET%,player_width}" height="{$GET%,player_height}" alt="No video playback capabilities" title="No video playback capabilities" />
	</video>

	{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<figcaption class="associated-details">
			{$PARAGRAPH,{DESCRIPTION}}
		</figcaption>
	{+END}

	{$,Uncomment for a download link \{+START,INCLUDE,MEDIA__DOWNLOAD_LINK\}\{+END\}}
{+END}
{+START,IF,{$GET,raw_video}}
	<video {+START,IF_NON_EMPTY,{THUMB_URL}} poster="{THUMB_URL*}"{+END} width="{$MIN*,950,{WIDTH}}" height="{$MIN*,{$MULT,{HEIGHT},{$DIV_FLOAT,950,{WIDTH}}},{HEIGHT}}" controls="controls"{+START,IF_PASSED_AND_TRUE,AUTOSTART} autoplay="true"{+END}>
		<source src="{$ENSURE_PROTOCOL_SUITABILITY*,{URL}}" type="{MIME_TYPE*}" />
		{+START,IF_PASSED,CLOSED_CAPTIONS_URL}{+START,IF_NON_EMPTY,{CLOSED_CAPTIONS_URL}}
			<track src="{$ENSURE_PROTOCOL_SUITABILITY*,{CLOSED_CAPTIONS_URL}}" kind="captions" label="{!CLOSED_CAPTIONS}" srclang="{$LCASE*,{$LANG}}" />
		{+END}{+END}

		<span>{DESCRIPTION}</span>
	</video>
{+END}
{+START,IF,{$NOT,{$GET,raw_video}}}
	{+START,IF_PASSED_AND_TRUE,FRAMED}
		<figure>
			{$GET,media}
		</figure>
	{+END}
	{+START,IF_NON_PASSED_OR_FALSE,FRAMED}
		{$GET,media}
	{+END}
{+END}
