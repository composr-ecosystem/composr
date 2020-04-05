{$SET,rand,{$RAND}}

<div class="box_skitter skitter" id="skitter-{$GET*,rand}" data-tpl="blockMainMultiContentSlider" data-tpl-params="{+START,PARAMS_JSON,rand,MILL}{_*}{+END}">
	<ul>
		{+START,LOOP,IMAGES}
			<li>
				<a href="#slider_{_loop_key*}"><img alt="" src="{$?*,{$PREG_MATCH,^\d+px$,{WIDTH}},{$THUMBNAIL,{FULL_URL},{$REPLACE,px,,{WIDTH}}x{$REPLACE,px,,{HEIGHT}},,,,pad,both},{FULL_URL}}" class="{TRANSITION_TYPE*}" /></a>
				<div class="label_text">{$PARAGRAPH,{TITLE*}}</div>
			</li>
		{+END}
	</ul>
</div>
