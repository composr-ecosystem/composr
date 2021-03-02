{+START,IF_NON_EMPTY,{SUMMARY}}
	<p class="accessibility-hidden">
		{SUMMARY*}
	</p>
{+END}
<div class="floats-wrap{+START,IF_PASSED,CLASS} {CLASS*}{+END}"{+START,IF_PASSED,ID} id="{ID*}"{+END}>
	{+START,LOOP,ROWS}
		<div class="float-surrounder{+START,IF,{IS_HEADER_ROW}} floats-header-row{+END}">
			{+START,LOOP,CELLS}
				<div class="{+START,IF,{IS_HEADER}}float-header {+END}float-{$?,{LAST_CELL_ON_ROW},right,left}"{+START,IF_PASSED,WIDTH} style="width: {WIDTH*'}"{+END}>
					{VALUE}
				</div>
			{+END}
		</div>
	{+END}
</div>
