<ol style="list-style-type: {$REPLACE*,;,,{TYPE}}" class="comcode-contents-level">
	{+START,LOOP,LINES}
		<li class="comcode-contents-level">
			<a target="_self" href="{URL*}#title--{ID*}">{$TRUNCATE_LEFT,{LINE`},100,1,1}</a>
			{UNDER}
		</li>
	{+END}
</ol>
