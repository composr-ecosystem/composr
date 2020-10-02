{$SET,rand,{$RAND}}

<script>
	$(document).ready(function() {
		$('#skitter_{$GET;/,rand}').skitter({
			auto_play: true,
			controls: true,
			dots: true,
			enable_navigation_keys: true,
			interval: {MILL%},
			numbers_align: 'center',
			preview: true,
			progressbar: false,
			theme: 'clean',
			thumbs: false
		});
	});
</script>

<div class="box_skitter" id="skitter_{$GET*,rand}">
	<ul>
		{+START,LOOP,IMAGES}
			<li>
				<a href="#slider_{_loop_key*}"><img src="{$?*,{$PREG_MATCH,^\d+px$,{WIDTH}},{$THUMBNAIL,{FULL_URL},{$REPLACE,px,,{WIDTH}}x{$REPLACE,px,,{HEIGHT}},,,,pad,both},{FULL_URL}}" class="{TRANSITION_TYPE*}" /></a>
				<div class="label_text">{$PARAGRAPH,{TITLE*}}</div>
			</li>
		{+END}
	</ul>
</div>
