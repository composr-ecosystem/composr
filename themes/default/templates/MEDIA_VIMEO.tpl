{$SET,player_id,player-{$RAND}}
<div class="responsive-media-wrapper media-vimeo" data-tpl="mediaVimeo" data-tpl-params="{+START,PARAMS_JSON,player_id,REMOTE_ID}{_*}{+END}"
		data-cms-embedded-media="{ width: {WIDTH%}, height: {HEIGHT%}, emits: ['play', 'pause', 'ended'], listens: ['do-play', 'do-pause'] }">
	<div class="media-vimeo-inner webstandards-checker-off">
		<iframe id="{$GET*,player_id}" src="https://player.vimeo.com/video/{REMOTE_ID*}?dnt=1" width="{WIDTH*}" height="{HEIGHT*}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen allow="autoplay; fullscreen"></iframe>
	</div>

	{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<figcaption class="associated-details">
			{$PARAGRAPH,{DESCRIPTION}}
		</figcaption>
	{+END}
</div>
