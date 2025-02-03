<a id="revisions"></a>

{+START,IF_NON_EMPTY,{RESULTS}}
	<div class="box box---revisions-wrap"><div class="box-inner">
		<h2>{!REVISIONS}</h2>

		{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
		{+START,IF_PASSED,WARNING_DETAILS}
			{WARNING_DETAILS}
		{+END}

		{RESULTS}
	</div></div>
{+END}
