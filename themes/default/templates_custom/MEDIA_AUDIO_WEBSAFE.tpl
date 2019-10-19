{+START,SET,media}
	{$SET,player_id,player_{$RAND}}

	{$REQUIRE_JAVASCRIPT,mediaelement-and-player}
	{$REQUIRE_CSS,mediaelementplayer}

	{$SET,audio_width,{$?,{$AND,{$EQ,{WIDTH},{$CONFIG_OPTION,default_video_width}},{$EQ,{HEIGHT},{$CONFIG_OPTION,default_video_height}}},400,{WIDTH}}}
	{$SET,audio_height,{$?,{$AND,{$EQ,{WIDTH},{$CONFIG_OPTION,default_video_width}},{$EQ,{HEIGHT},{$CONFIG_OPTION,default_video_height}}},30,{HEIGHT}}}

	{+START,IF_NON_PASSED_OR_FALSE,WYSIWYG_EDITABLE}
		{+START,IF_EMPTY,{$METADATA,video}}
			{$METADATA,video,{URL}}
			{$METADATA,video:height,{$GET,audio_height}}
			{$METADATA,video:width,{$GET,audio_width}}
			{$METADATA,video:type,{MIME_TYPE}}
		{+END}
	{+END}

	<audio controls="controls" preload="none" id="{$GET%,player_id}">
		<source type="{MIME_TYPE*}" src="{$ENSURE_PROTOCOL_SUITABILITY*,{URL}}" />
		<object width="{$GET*,audio_width}" height="{$GET&,audio_height}" type="application/x-shockwave-flash" data="{$BASE_URL*}/data_custom/mediaelement/flashmediaelement.swf">
			<param name="movie" value="{$BASE_URL*}/data_custom/mediaelement/flashmediaelement.swf" />
			<param name="flashvars" value="controls=true&amp;file={URL&*}" />

			<img src="{THUMB_URL*}" width="{$GET*,audio_width}" height="{$GET*,audio_height}" alt="No audio playback capabilities" title="No audio playback capabilities" />
		</object>
	</audio>

	<script>// <![CDATA[
		add_event_listener_abstract(window,'load',function() {
			var player=new MediaElementPlayer('#{$GET%,player_id}',{
				{$,Scale to a maximum width because we can always maximise - for object/embed players we can use max-width for this}
				{+START,IF_NON_EMPTY,{$GET,audio_width}}
					audioWidth: {$MIN%,950,{$GET,audio_width}},
				{+END}
				{+START,IF_NON_EMPTY,{$GET,audio_height}}
					audioHeight: {$MIN%,{$MULT,{$GET,audio_height},{$DIV_FLOAT,950,{$GET,audio_width}}},{$GET,audio_height}},
				{+END}

				enableKeyboard: true,

				success: function(media) {
					{+START,IF,{$NOT,{$INLINE_STATS}}}
						media.addEventListener('play',function() { ga_track(null,'{!AUDIO;/}','{URL;/}'); });
					{+END}
					if (document.getElementById('next_slide'))
					{
						media.addEventListener('canplay',function() { stop_slideshow_timer(); player.play(); });
						media.addEventListener('ended',function() { player_stopped(); });
					}
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
