{+START,IF_NON_EMPTY,{SUMMARY}}
	<p class="accessibility-hidden">
		{SUMMARY*}
	</p>
{+END}
<div class="flex-wrapper{+START,IF_PASSED,CLASS} {CLASS*}{+END}"{+START,IF_PASSED,ID} id="{ID*}"{+END}>
	{+START,LOOP,ROWS}
		{+START,LOOP,CELLS}
			<div{+START,IF,{IS_HEADER}} class="flex-box-header"{+END}{+START,IF_PASSED,WIDTH} style="width: {WIDTH*'}"{+END}>
				{VALUE}
			</div>
		{+END}
	{+END}
</div>
