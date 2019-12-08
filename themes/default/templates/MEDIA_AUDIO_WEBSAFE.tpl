{+START,SET,media}
	{$SET,player_id,player_{$RAND}}

	{$REQUIRE_JAVASCRIPT,jwplayer}

	{$SET,audio_width,{$?,{$AND,{$EQ,{WIDTH},{$CONFIG_OPTION,default_video_width,1}},{$EQ,{HEIGHT},{$CONFIG_OPTION,default_video_height,1}}},400,{WIDTH}}}
	{$SET,audio_height,{$?,{$AND,{$EQ,{WIDTH},{$CONFIG_OPTION,default_video_width,1}},{$EQ,{HEIGHT},{$CONFIG_OPTION,default_video_height,1}}},30,{HEIGHT}}}

	{+START,IF_NON_PASSED_OR_FALSE,WYSIWYG_EDITABLE}
		{+START,IF_EMPTY,{$METADATA,video}}
			{$METADATA,video,{URL}}
			{$METADATA,video:height,{$GET,audio_height}}
			{$METADATA,video:width,{$GET,audio_width}}
			{$METADATA,video:type,{MIME_TYPE}}
		{+END}
	{+END}

	<meta itemprop="width" content="{$GET,audio_width}" />
	<meta itemprop="height" content="{$GET*,audio_height}" />
	{+START,IF_NON_EMPTY,{LENGTH}}
		<meta itemprop="duration" content="T{LENGTH*}S" />
	{+END}
	<meta itemprop="thumbnailURL" content="{THUMB_URL*}" />
	<meta itemprop="embedURL" content="{URL*}" />

	<div class="webstandards_checker_off" id="{$GET%,player_id}"></div>

	{$,API: http://www.longtailvideo.com/support/jw-player/jw-player-for-flash-v5/12540/javascript-api-reference}

	<script>// <![CDATA[
		{$,Carefully tuned to avoid this problem: http://www.longtailvideo.com/support/forums/jw-player/setup-issues-and-embedding/8439/sound-but-no-video}
		add_event_listener_abstract(window,'load',function() {
			jwplayer('{$GET%,player_id}').setup({
				width: {$GET%,audio_width},
				height: {$GET%,audio_height},
				autostart: false,
				{+START,IF_NON_EMPTY,{LENGTH}}
					duration: {LENGTH%},
				{+END}
				file: '{URL;/}',
				type: '{$PREG_REPLACE*,.*\.,,{$LCASE,{FILENAME}}}',
				image: '{THUMB_URL;/}',
				flashplayer: '{$BASE_URL;/}/data/jwplayer.flash.swf{+START,IF,{$NOT,{$BROWSER_MATCHES,bot}}}?rand={$RAND;/}{+END}',
				events: {
					{+START,IF,{$NOT,{$INLINE_STATS}}}onPlay: function() { ga_track(null,'{!AUDIO;/}','{URL;/}'); },{+END}
					onComplete: function() { if (document.getElementById('next_slide')) player_stopped(); },
					onReady: function() { if (document.getElementById('next_slide')) { stop_slideshow_timer(); jwplayer('{$GET%,player_id}').play(true); } }
				}
			});
		});
	//]]></script>

	{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<figcaption class="associated_details">
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
