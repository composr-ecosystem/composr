{+START,SET,media}
	{$SET,player_id,player-{$RAND}}

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

	<audio style="display: none" controls="controls" preload="none" id="{$GET%,player_id}"
			 data-tpl="mediaAudioWebsafe" data-tpl-params="{+START,PARAMS_JSON,player_id,WIDTH,HEIGHT,LENGTH,URL,THUMB_URL,type,flashplayer,inline_stats,AUTOSTART,CLOSED_CAPTIONS_URL}{_*}{+END}"
			 {+START,IF_PASSED_AND_TRUE,AUTOSTART} autoplay="true"{+END} data-cms-embedded-media="{ width: {WIDTH%}, height: {HEIGHT%}, emits: ['play', 'pause', 'ended'], listens: ['do-play', 'do-pause'] }">
		<source type="{MIME_TYPE*}" src="{$ENSURE_PROTOCOL_SUITABILITY*,{URL}}" />
		{+START,IF_PASSED,CLOSED_CAPTIONS_URL}{+START,IF_NON_EMPTY,{CLOSED_CAPTIONS_URL}}
			<track src="{$ENSURE_PROTOCOL_SUITABILITY*,{CLOSED_CAPTIONS_URL}}" kind="captions" label="{!CLOSED_CAPTIONS}" srclang="{$LCASE*,{$LANG}}" />
		{+END}{+END}

		<img src="{$ENSURE_PROTOCOL_SUITABILITY*,{THUMB_URL}}" width="{WIDTH*}" height="{HEIGHT*}" alt="No audio playback capabilities" title="No audio playback capabilities" />
	</audio>

	{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<figcaption class="associated-details">
			{$PARAGRAPH,{DESCRIPTION}}
		</figcaption>
	{+END}

	{$,Uncomment for a download link \{+START,INCLUDE,MEDIA__DOWNLOAD_LINK\}\{+END\}}
{+END}
{+START,IF_PASSED_AND_TRUE,FRAMED}
	<figure>
		{$GET,media}
	</figure>
{+END}
{+START,IF_NON_PASSED_OR_FALSE,FRAMED}
	{$GET,media}
{+END}
