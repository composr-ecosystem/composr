{$SET,player_id,player-{$RAND}}

<div class="responsive-media-wrapper media-youtube" data-tpl="mediaYouTube" data-tpl-params="{+START,PARAMS_JSON,player_id,WIDTH,HEIGHT,REMOTE_ID}{_*}{+END}"
		data-cms-embedded-media="{ width: {WIDTH%}, height: {HEIGHT%}, emits: ['play', 'pause', 'ended'], listens: ['do-play', 'do-pause'] }">
	<div id="{$GET*,player_id}"></div>
</div>

{+START,IF_NON_EMPTY,{DESCRIPTION}}
	<figcaption class="associated-details">
		{$PARAGRAPH,{DESCRIPTION}}
	</figcaption>
{+END}
