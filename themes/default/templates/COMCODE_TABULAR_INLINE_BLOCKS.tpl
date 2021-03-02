{+START,IF_NON_EMPTY,{SUMMARY}}
	<p class="accessibility-hidden">
		{SUMMARY*}
	</p>
{+END}
<div class="inline-block-wrapper{+START,IF_PASSED,CLASS} {CLASS*}{+END}"{+START,IF_PASSED,ID} id="{ID*}"{+END}>
	{+START,LOOP,ROWS}
		{+START,LOOP,CELLS}<div class="{+START,IF,{IS_HEADER}}inline-block-box-header {+END}inline-block-box"{+START,IF_PASSED,WIDTH} style="width: {WIDTH*'}"{+END}>{VALUE}</div>{+END}
	{+END}
</div>
